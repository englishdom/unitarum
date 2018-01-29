<?php

namespace Unitarum\Adapter;

interface AdapterInterface
{

    /**
     * SqliteAdapter constructor.
     */
    public function __construct($dsn);

    public function getPdo(): \PDO;

    public function getTableStructure($tableName): array;

    public function truncateTables(array $tables);
}