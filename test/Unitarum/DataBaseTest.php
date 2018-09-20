<?php

namespace UnitarumTest;

use PHPUnit\Framework\TestCase;
use Unitarum\Adapter\SqliteAdapter;
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
        $options = new Options([]);
        $this->dataBase = new DataBase($options);
        $pdo = new \PDO('sqlite:data/sqlite.db', null, null, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
        $adapter = new SqliteAdapter($pdo);
        $this->dataBase->setAdapter($adapter);
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

    public function testMergeArrayWithoutSecond()
    {
        $firstEntity = new User();
        $firstEntity->setName('Test');
        $firstEntity->setEmail('test@test.no');

        $secondEntity = null;

        $method = self::getProtectedMethod(DataBase::class, 'mergeArrays');
        $returnEntity = $method->invokeArgs($this->dataBase, [$firstEntity, $secondEntity]);
        $this->assertEquals($firstEntity, $returnEntity);
    }

    public function testInsertDataFunctional()
    {
        /* Start transaction */
        $this->dataBase->startTransaction();

        $insertData = [
            'id' => '100',
            'email' => 'test@test.no',
            'name' => 'TestName',
            'md5_hash'=> ''
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

    public function testGetTableStructure()
    {
        $originalColumns = [
            DataBaseInterface::AUTO_INCREMENT => 'id',
            'name',
            'email',
            'md5_hash'
        ];
        $returnColumns = $this->dataBase->getAdapter()->getTableStructure(self::TEST_TABLE_USERS);
        $this->assertEquals($originalColumns, $returnColumns);
    }

    public function testAddTables()
    {
        $this->dataBase->addTables(['users', 'words']);
        $this->dataBase->addTables(['test']);
        $property = $this->getProtectedProperty(DataBase::class, 'tables');
        $this->assertEquals(['users', 'words', 'test'], $property->getValue($this->dataBase));
    }

    public function tearDown()
    {
        $this->dataBase->rollbackTransaction();
    }
}