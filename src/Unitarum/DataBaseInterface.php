<?php

namespace Unitarum;

use Unitarum\Adapter\AdapterInterface;
use Unitarum\Adapter\SqliteAdapter;

interface DataBaseInterface
{
    const AUTO_INCREMENT = 'auto_increment';

    /**
     * DataBase constructor.
     * @param OptionsInterface $options
     */
    public function __construct(OptionsInterface $options);

    public function execute($defaultEntity, $incomeEntity, $tableAlias);

    public function startTransaction();

    public function rollbackTransaction();

    /**
     * @return AdapterInterface
     */
    public function getAdapter(): AdapterInterface;

    /**
     * @param AdapterInterface $adapter
     * @return DataBase
     */
    public function setAdapter(AdapterInterface $adapter);

    public function truncate();

    /**
     * @param array $tables
     * @return mixed
     */
    public function addTables(array $tables);

    public function setTables(array $tables);
}