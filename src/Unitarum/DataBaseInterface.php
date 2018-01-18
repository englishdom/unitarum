<?php

namespace Unitarum;

interface DataBaseInterface
{

    /**
     * DataBase constructor.
     * @param OptionsInterface $options
     */
    public function __construct(OptionsInterface $options);

    /**
     * @param null $tableName
     * @return array
     */
    public function getCollection($tableName = null): array;

    public function execute($defaultData, $changeData, $tableAlias);

    public function startTransaction();

    public function rollbackTransaction();
}