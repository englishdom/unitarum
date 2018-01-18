<?php

namespace UnitarumTest;

use PHPUnit\Framework\TestCase;
use Unitarum\DataBase;
use Unitarum\DataBaseInterface;
use Unitarum\Options;
use Unitarum\OptionsInterface;
use UnitarumExample\Entity\User;

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
        $firstEntity = new User();
        $firstEntity->setName('Test');
        $firstEntity->setEmail('test@test.no');

        $secondEntity = new User();
        $secondEntity->setName('SuperTest');

        $changedEntity = new User();
        $changedEntity->setName('SuperTest');
        $changedEntity->setEmail('test@test.no');

        $method = self::getProtectedMethod(DataBase::class, 'mergeArrays');
        $returnEntity = $method->invokeArgs($this->dataBase, [$firstEntity, $secondEntity]);
        $this->assertEquals($changedEntity, $returnEntity);
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

    public function testGetPdo()
    {
        $pdo = $this->dataBase->getPDO();
        $this->assertInstanceOf(\PDO::class, $pdo);
    }
    
    public function testGetTableStructure()
    {
        $tableName = self::TEST_TABLE_USERS;
        $pdo = $this->dataBase->getPDO();

        $sql = sprintf(
            'SELECT sql FROM sqlite_master WHERE tbl_name = "%s"',
            $tableName
        );

        $statement = $pdo->prepare($sql);
        $statement->execute();
        $result = $statement->fetch(\PDO::FETCH_ASSOC);

//        preg_match('~\((.+)\)~si', $result['sql'], $matches);
//        $array = preg_split('~\,~', $matches[1]);
//        var_dump($array); die();

        preg_match('~\([[:space:]]*([a-z]+).*autoincrement~siu', $result['sql'], $matches);
        var_dump($matches); die();
    }
}