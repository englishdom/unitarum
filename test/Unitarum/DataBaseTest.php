<?php

namespace UnitarumTest;

use PHPUnit\Framework\TestCase;
use Unitarum\DataBase;
use Unitarum\DataBaseInterface;
use Unitarum\Options;
use Unitarum\OptionsInterface;

class DataBaseTest extends TestCase
{
    use GetProtectedTrait;

    /**
     * @var DataBaseInterface
     */
    protected $dataBase;

    public function setUp()
    {
        $options = new Options([OptionsInterface::DSN_OPTION => 'sqlite::memory:']);
        $this->dataBase = new DataBase($options);
    }

    public function testGetCollection()
    {
        $collection = $this->dataBase->getCollection();
        $this->assertInstanceOf(\ArrayObject::class, $collection);
    }

    public function testMergeArrays() {
        $firstArray = ['name' => 'Test', 'email' => 'test@test.no'];
        $secondArray = ['name' => 'SuperTest'];
        $changedArray = ['name' => 'SuperTest', 'email' =>'test@test.no'];

        $method = self::getProtectedMethod(DataBase::class, 'mergeArrays');
        $returnArray = $method->invokeArgs($this->dataBase, [$firstArray, $secondArray]);
        $this->assertEquals($changedArray, $returnArray);
    }
}