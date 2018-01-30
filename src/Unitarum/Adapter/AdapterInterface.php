<?php

namespace Unitarum\Adapter;

interface AdapterInterface
{
    /**
     * SqliteAdapter constructor.
     */
    public function __construct(\PDO $pdo);

    public function getPdo(): \PDO;

    public function getTableStructure($tableName): array;
}