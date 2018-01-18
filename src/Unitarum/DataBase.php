<?php

namespace Unitarum;

use Unitarum\Exception\ParamNotExistException;

define('AUTO_INCREMENT', 'AUTO_INCREMENT');
define('OPEN_BRACE', '{{');
define('CLOSE_BRACE', '}}');

class DataBase implements DataBaseInterface
{
    /**
     * @var array
     */
    protected $collection = [];

    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * DataBase constructor.
     * @param OptionsInterface $options
     */
    public function __construct(OptionsInterface $options)
    {
        $this->pdo = new \PDO($options->getDsn());
    }

    public function startTransaction()
    {
        if (!$this->pdo->inTransaction()) {
            $this->pdo->beginTransaction();
        }
    }

    public function rollbackTransaction()
    {
        $this->pdo->rollBack();
    }

    public function execute($defaultData, $changeData, $tableAlias)
    {
        $dataArray = reset($defaultData);
        $tableName = key($defaultData);
        $insertingData = $this->mergeArrays($dataArray, $changeData);

        /* Get autoincrement field and remove from inserting data */
        $aiField = $this->getAutoincrementField($insertingData, $tableName);
        unset($insertingData[$aiField]);

        /* Get identifier from previous data */
        $insertingData = $this->executeExpression($insertingData);

        $lastInsertId = $this->insertData($insertingData, $tableName);
        $insertedData = $this->selectById($lastInsertId, $aiField, $tableName);
        $this->appendToCollection($tableAlias, $insertedData);
        return $insertedData;
    }

    protected function insertData(array $insertingData, $tableName)
    {
        /* Prepare sql */
        $columnNames = array_keys($insertingData);
        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $tableName,
            implode(',', $columnNames),
            implode(',', array_fill(0, count($insertingData), '?'))
        );
        $statement = $this->pdo->prepare($sql);

        // Insert data to table
        if (!$statement->execute(array_values($insertingData))) {
            throw new \SQLiteException(
                sprintf(
                    'Can not insert data to the table! Query: %s. Data: %s',
                    $sql,
                    implode(',', $insertingData)
                )
            );
        }

        return $this->pdo->lastInsertId();
    }

    protected function selectById($identifier, $fieldName, $tableName)
    {
        $sql = sprintf(
            'SELECT * FROM %s WHERE %s = ?',
            $tableName,
            $fieldName
        );
        $statement = $this->pdo->prepare($sql);
        $statement->execute([$identifier]);
        return $statement->fetch(\PDO::FETCH_ASSOC);
    }

    protected function getAutoincrementField(array $fields, $tableName)
    {
        foreach ($fields as $fieldName => $value) {
            if ($value == AUTO_INCREMENT) {
                return $fieldName;
            }
        }
        throw new ParamNotExistException(
            'The `'.AUTO_INCREMENT.'` field does not exist in a table `'.$tableName.'``'
        );
    }

    protected function mergeArrays(array $originalData, array $changedData)
    {
        return array_merge($originalData, $changedData);
    }

    /**
     * @param null $tableName
     * @return array
     */
    public function getCollection($tableName = null): array
    {
        if ($tableName !== null && isset($this->collection[$tableName])) {
            return $this->collection[$tableName];
        }
        return $this->collection;
    }

    protected function appendToCollection($tableName, array $data)
    {
        $this->collection[$tableName][] = $data;
        return $this->getCollection();
    }

    protected function executeExpression($insertingData): array
    {
        foreach ($insertingData as &$value) {
            if (substr($value, 0, 2) == OPEN_BRACE && substr($value, -2) == CLOSE_BRACE) {
                $expression = substr($value, 2, -2);
                list($tableAlias, $fieldName) = explode('.', $expression);

                $fields = $this->getCollection($tableAlias);
                /* @TODO Get only first inserted DATA. Need create duplicate row for inserting data */
                if (!isset($fields[0][$fieldName])) {
                    throw new ParamNotExistException(
                        sprintf(
                            'Collection does not have field `%s` for table alias `%s`',
                            $fieldName,
                            $tableAlias
                        )
                    );
                }
                $value = $fields[0][$fieldName];
            }
        }
        return $insertingData;
    }
}