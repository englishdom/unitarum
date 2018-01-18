<?php

namespace UnitarumTest;

use PHPUnit\Framework\TestCase;
use Unitarum\DataBase;
use Unitarum\DataBaseInterface;
use Unitarum\Options;
use Unitarum\OptionsInterface;

class DataBaseTest extends TestCase
{
    const TEST_TABLE_USERS = 'test_users';
    const TEST_TABLE_ROLES = 'test_roles';

    use GetProtectedTrait;

    /**
     * @var DataBaseInterface
     */
    protected $dataBase;

    public function setUp()
    {
        $options = new Options([OptionsInterface::DSN_OPTION => 'sqlite:data/sqlite.db']);
        $this->dataBase = new DataBase($options);
    }

    public function testGetCollection()
    {
        $collection = $this->dataBase->getCollection();
        $this->assertTrue(is_array($collection));
    }

    public function testGetCollectionRecord()
    {
        $collectionData = ['field' => 1];
        $collection = $this->getProtectedProperty(DataBase::class, 'collection');
        $collection->setValue($this->dataBase, ['test' => $collectionData]);

        $return = $this->dataBase->getCollection('test');
        $this->assertEquals($collectionData, $return);
    }

    public function testMergeArrays() {
        $firstArray = ['name' => 'Test', 'email' => 'test@test.no'];
        $secondArray = ['name' => 'SuperTest'];
        $changedArray = ['name' => 'SuperTest', 'email' =>'test@test.no'];

        $method = self::getProtectedMethod(DataBase::class, 'mergeArrays');
        $returnArray = $method->invokeArgs($this->dataBase, [$firstArray, $secondArray]);
        $this->assertEquals($changedArray, $returnArray);
    }

    public function testGetAutoincrementField()
    {
        $aiField = 'id';
        $fields = [
            'id' => AUTO_INCREMENT,
            'email' => 'test@test.no'
        ];
        $method = self::getProtectedMethod(DataBase::class, 'getAutoincrementField');
        $returnField = $method->invokeArgs($this->dataBase, [$fields, 'test']);
        $this->assertEquals($aiField, $returnField);
    }

    /**
     * @expectedException \Unitarum\Exception\ParamNotExistException
     */
    public function testGetAutoincrementFieldException()
    {
        $fields = [
            'name' => 'Test',
            'email' => 'test@test.no'
        ];
        $method = self::getProtectedMethod(DataBase::class, 'getAutoincrementField');
        $method->invokeArgs($this->dataBase, [$fields, 'test']);
    }

    public function testInsertDataFunctional()
    {
        /* Start transaction */
        $this->dataBase->startTransaction();

        $insertData = [
            'id' => 100,
            'email' => 'test@test.no',
            'name' => 'TestName'
        ];
        $methodInsert = self::getProtectedMethod(DataBase::class, 'insertData');
        $lastInsertId = $methodInsert->invokeArgs($this->dataBase, [$insertData, self::TEST_TABLE_USERS]);
        $this->assertNotFalse($lastInsertId);

        $methodSelect = self::getProtectedMethod(DataBase::class, 'selectById');
        $result = $methodSelect->invokeArgs($this->dataBase, [$lastInsertId, 'id', self::TEST_TABLE_USERS]);
        $this->assertEquals($insertData, $result);

        /* Rollback transaction */
        $this->dataBase->rollbackTransaction();
    }

    public function testInsertException()
    {
//        $insertData = [];
//        $methodInsert = self::getProtectedMethod(DataBase::class, 'insertData');
//        $methodInsert->invokeArgs($this->dataBase, [$insertData, self::TEST_TABLE_USERS]);
    }
}