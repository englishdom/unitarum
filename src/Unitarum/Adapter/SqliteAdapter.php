<?php

namespace Unitarum\Adapter;

use Unitarum\DataBaseInterface;

class SqliteAdapter implements AdapterInterface
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * SqliteAdapter constructor.
     */
    public function __construct($dsn)
    {
        $this->pdo = new \PDO($dsn);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function getPdo(): \PDO
    {
        return $this->pdo;
    }

    public function getTableStructure($tableName): array
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
                $columns[DataBaseInterface::AUTO_INCREMENT] = str_replace('`', '', $parts[0]);
            } else {
                $columns[] = str_replace('`', '', $parts[0]);
            }
        }

        return $columns;
    }
}