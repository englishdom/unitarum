<?php

namespace Unitarum;

use Unitarum\Exception\DataBaseException;

define('AUTO_INCREMENT', 'AUTO_INCREMENT');

class DataBase implements DataBaseInterface
{
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
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
    }

    public function execute($defaultData, $incomeEntity, $tableAlias)
    {
        $defaultEntity = reset($defaultData);
        $tableName = key($defaultData);
        $insertingEntity = $this->mergeArrays($defaultEntity, $incomeEntity);

        $hydrator = new SimpleHydrator();
        $insertingData = $hydrator->extract($insertingEntity);

        /* Get real columns name from table structure */
        $columns = $this->getTableStructure($tableName);

        /* Remove unused columns from data */
        $clearData = array_intersect_key($insertingData, array_flip($columns));

        $lastInsertId = $this->insertData($clearData, $tableName);
        $insertedData = $this->selectById($lastInsertId, $columns[AUTO_INCREMENT], $tableName);

        $hydrator->hydrate($insertedData, $incomeEntity);
        return $incomeEntity;
    }

    protected function insertData($insertingData, $tableName)
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
            throw new DataBaseException(
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

    protected function mergeArrays($originalEntity, $changedEntity)
    {
        if ($changedEntity === null) {
            return $originalEntity;
        }

        $hydrator = new SimpleHydrator();
        $entityName = get_class($originalEntity);

        $firstArray = $hydrator->extract($originalEntity);
        $secondArray = array_filter($hydrator->extract($changedEntity));
        $mergedArray = array_merge($firstArray, $secondArray);
        return $hydrator->hydrate($mergedArray, new $entityName());
    }

    protected function getTableStructure($tableName): array
    {
        $sql = sprintf(
            'SELECT sql FROM sqlite_master WHERE tbl_name = "%s"',
            $tableName
        );

        $statement = $this->pdo->prepare($sql);
        $statement->execute();
        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        $columns = [];
        preg_match('~\((.+)\)~si', $result['sql'], $matches);
        $array = explode(',', $matches[1]);
        foreach ($array as $value) {
            $clearString = trim($value);
            $parts = explode(' ', $clearString);
            if (stristr($clearString, 'autoincrement')) {
                $columns[AUTO_INCREMENT] = $parts[0];
            } else {
                $columns[] = $parts[0];
            }
        }

        return $columns;
    }
}