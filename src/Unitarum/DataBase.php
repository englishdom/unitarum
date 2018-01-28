<?php

namespace Unitarum;

use Unitarum\Adapter\AdapterInterface;
use Unitarum\Adapter\SqliteAdapter;

class DataBase implements DataBaseInterface
{

    /**
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * @var OptionsInterface
     */
    private $options;

    /**
     * DataBase constructor.
     * @param OptionsInterface $options
     */
    public function __construct(OptionsInterface $options)
    {
        $this->options = $options;
    }

    public function startTransaction()
    {
        if (!$this->getAdapter()->getPdo()->inTransaction()) {
            $this->getAdapter()->getPdo()->beginTransaction();
        }
    }

    public function rollbackTransaction()
    {
        if ($this->getAdapter()->getPdo()->inTransaction()) {
            $this->getAdapter()->getPdo()->rollBack();
        }
    }

    public function execute($defaultData, $incomeEntity, $tableAlias)
    {
        $defaultEntity = reset($defaultData);
        $tableName = key($defaultData);
        $insertingEntity = $this->mergeArrays($defaultEntity, $incomeEntity);

        $hydrator = new SimpleHydrator();
        $insertingData = $hydrator->extract($insertingEntity);
        $insertingData = array_filter($insertingData);
        /* Get real columns name from table structure */
        $columns = $this->getAdapter()->getTableStructure($tableName);

        /* Remove unused columns from data */
        $clearData = array_intersect_key($insertingData, array_flip($columns));

        $lastInsertId = $this->insertData($clearData, $tableName);
        $insertedData = $this->selectById($lastInsertId, $columns[DataBaseInterface::AUTO_INCREMENT], $tableName);

        $hydrator->hydrate($insertedData, $defaultEntity);
        return $defaultEntity;
    }

    /**
     * @return AdapterInterface
     */
    public function getAdapter(): AdapterInterface
    {
        if (!$this->adapter) {
            $this->adapter = new SqliteAdapter($this->options->getDsn());
        }
        return $this->adapter;
    }

    /**
     * @param AdapterInterface $adapter
     * @return DataBase
     */
    public function setAdapter(AdapterInterface $adapter): self
    {
        $this->adapter = $adapter;
        return $this;
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
        $statement = $this->getAdapter()->getPdo()->prepare($sql);
        $statement->execute(array_values($insertingData));

        return $this->getAdapter()->getPdo()->lastInsertId();
    }

    protected function selectById($identifier, $fieldName, $tableName)
    {
        $sql = sprintf(
            'SELECT * FROM %s WHERE %s = ?',
            $tableName,
            $fieldName
        );
        $statement = $this->getAdapter()->getPdo()->prepare($sql);
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
}