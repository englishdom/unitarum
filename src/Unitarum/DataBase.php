<?php

namespace Unitarum;

use Unitarum\Exception\ParamNotExistException;

define('AUTO_INCREMENT', 'AUTO_INCREMENT');

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

    public function execute($defaultData, $changeData)
    {
        $dataArray = reset($defaultData);
        $tableName = key($defaultData);
        $insertingData = $this->mergeArrays($dataArray, $changeData);

        /* Get autoincrement field and remove from inserting data */
        $aiField = $this->getAutoincrementField($insertingData, $tableName);
        unset($insertingData[$aiField]);

        $lastInsertId = $this->insertData($insertingData, $tableName);
        if (!$lastInsertId) {
            return false;
        }

        $insertedData = $this->selectById($lastInsertId, $aiField, $tableName);
        $this->appendToCollection($tableName, $insertedData);
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
            return false;
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
     * @return array
     */
    public function getCollection(): array
    {
        return $this->collection;
    }

    protected function appendToCollection($tableName, array $data)
    {
        $this->getCollection()[$tableName][] = $data;
        return $this->getCollection();
    }
}