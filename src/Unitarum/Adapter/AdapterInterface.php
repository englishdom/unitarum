<?php

namespace Unitarum\Adapter;

use Unitarum\DataBaseInterface;

interface AdapterInterface
{

    /**
     * SqliteAdapter constructor.
     */
    public function __construct($dsn);

    public function getPdo(): \PDO;

    public function getTableStructure($tableName): array;
}