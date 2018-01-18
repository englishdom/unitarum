<?php

namespace Unitarum;

use Unitarum\Exception\ParamNotExistException;
use Zend\Hydrator\ClassMethods;

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

    public function execute($defaultData, $incomeEntity, $tableAlias)
    {
        $defaultEntity = reset($defaultData);
        $tableName = key($defaultData);
        $insertingEntity = $this->mergeArrays($defaultEntity, $incomeEntity);

        $hydrator = new SimpleHydrator();
        $insertingData = $hydrator->extract($insertingEntity);
        /* Get identifier from previous data */
        $insertingData = $this->executeExpression($insertingData);

        $lastInsertId = $this->insertData($insertingData, $tableName);
        $insertedData = $this->selectById($lastInsertId, $aiField, $tableName);
        $this->appendToCollection($tableAlias, $insertedData);
        return $insertedData;
    }

    protected function insertData($insertingEntity, $tableName)
    {
        /* Prepare sql */
        $columnNames = array_keys($insertingEntity);
        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $tableName,
            implode(',', $columnNames),
            implode(',', array_fill(0, count($insertingEntity), '?'))
        );
        $statement = $this->pdo->prepare($sql);

        // Insert data to table
        if (!$statement->execute(array_values($insertingEntity))) {
            throw new \SQLiteException(
                sprintf(
                    'Can not insert data to the table! Query: %s. Data: %s',
                    $sql,
                    implode(',', $insertingEntity)
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

    protected function mergeArrays($originalEntity, $changedEntity)
    {
        $hydrator = new SimpleHydrator();
        $entityName = get_class($originalEntity);

        $firstArray = $hydrator->extract($originalEntity);
        $secondArray = array_filter($hydrator->extract($changedEntity));
        $mergedArray = array_merge($firstArray, $secondArray);
        return $hydrator->hydrate($mergedArray, new $entityName());
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

    protected function executeExpression($insertingData) : array
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

    public function getPDO(): \PDO
    {
        return $this->pdo;
    }

    protected function getTableStructure($tableName)
    {

    }
}