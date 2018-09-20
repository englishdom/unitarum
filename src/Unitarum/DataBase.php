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

    private $tables = [];

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

    public function truncate()
    {
        foreach ($this->tables as $tableName) {
            $this->getAdapter()->getPdo()->exec('SET FOREIGN_KEY_CHECKS=0;');
            $this->getAdapter()->getPdo()->exec(sprintf('TRUNCATE TABLE `%s`', $tableName));
        }
        $this->tables = [];
    }

    public function execute($defaultData, $incomeEntity, $tableAlias)
    {
        $defaultEntity = reset($defaultData);
        $tableName = key($defaultData);
        $this->tables[] = $tableName;
        $insertingEntity = $this->mergeArrays($defaultEntity, $incomeEntity);
        $hydrator = new SimpleHydrator($this->options->getWhiteList());
        $insertingData = $hydrator->extract($insertingEntity);
        $insertingData = array_filter($insertingData, function($value) {
            return $value !== null;
        });
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
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        return $result;
    }

    protected function mergeArrays($originalEntity, $changedEntity)
    {
        if ($changedEntity === null) {
            return $originalEntity;
        }

        $hydrator = new SimpleHydrator($this->options->getWhiteList());
        $entityName = get_class($originalEntity);

        $firstArray = array_filter($hydrator->extract($originalEntity), function($value) {
            return $value !== null;
        });
        $secondArray = array_filter($hydrator->extract($changedEntity), function($value) {
            return $value !== null;
        });
        $mergedArray = array_merge($firstArray, $secondArray);
        return $hydrator->hydrate($mergedArray, new $entityName());
    }
}