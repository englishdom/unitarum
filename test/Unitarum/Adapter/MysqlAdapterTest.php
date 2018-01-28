<?php

namespace UnitarumTest\Adapter;

use PHPUnit\Framework\TestCase;
use Unitarum\DataBaseInterface;
use Unitarum\Adapter\MysqlAdapter;
use Unitarum\Options;
use Unitarum\OptionsInterface;

class MysqlAdapterTest extends TestCase
{
    /**
     * @var DataBaseInterface
     */
    protected $dataBase;

    public function setUp()
    {
        $options = new Options([
            OptionsInterface::DSN_OPTION => 'mysql:host=localhost;dbname=docker',
            OptionsInterface::DB_USERNAME => 'docker',
            OptionsInterface::DB_PASSWORD => 'docker',
        ]);
        $this->dataBase = new MysqlAdapter($options);
    }
}