<?php

namespace Unitarum\Adapter;

use Unitarum\DataBaseInterface;

class MysqlAdapter implements AdapterInterface
{
    /**
     * @var \PDO
     */
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getPdo(): \PDO
    {
        return $this->pdo;
    }

    public function getTableStructure($tableName): array
    {
        $sql = sprintf(
            'SHOW COLUMNS FROM `%s`',
            $tableName
        );

        $statement = $this->pdo->prepare($sql);
        $statement->execute();
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

        $columns = [];
        foreach($result as $row) {
            if ($row['Extra'] == 'auto_increment') {
                $columns[DataBaseInterface::AUTO_INCREMENT] = $row['Field'];
            } else {
                $columns[] = $row['Field'];
            }
        }

        return $columns;
    }
}