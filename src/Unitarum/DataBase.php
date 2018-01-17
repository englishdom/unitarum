<?php

namespace Unitarum;

class DataBase implements DataBaseInterface
{
    /**
     * @var \SplObjectStorage
     */
    protected $collection;

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

    public function execute($defaultData, $changeData)
    {
        $dataArray = reset($defaultData);
        $tableName = key($defaultData);

        $mergedData = $this->mergeArrays($dataArray, $changeData);
        // Insert data to table
    }

    protected function mergeArrays(array $originalData, array $changedData)
    {
        return array_merge($originalData, $changedData);
    }

    /**
     * @return \ArrayObject
     */
    public function getCollection(): \ArrayObject
    {
        if (!$this->collection instanceof \ArrayObject) {
            $this->collection = new \ArrayObject();
        }
        return $this->collection;
    }
}