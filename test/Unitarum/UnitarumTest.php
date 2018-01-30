<?php

namespace UnitarumTest;

use PHPUnit\Framework\TestCase;
use Unitarum\Adapter\SqliteAdapter;
use Unitarum\DataBase;
use Unitarum\DataBaseInterface;
use Unitarum\Options;
use Unitarum\OptionsInterface;
use Unitarum\Reader;
use Unitarum\ReaderInterface;
use Unitarum\Unitarum;
use UnitarumExample\Entity\Role;
use UnitarumExample\Entity\User;
use UnitarumTest\DataBaseTest;

/**
 * @package UnitarumTest
 */
class UnitarumTest extends TestCase
{
    use GetProtectedTrait;

    /**
     * @dataProvider supportOptionsTypesData
     */
    public function testSupportOptions($options)
    {
        $unitarum = new Unitarum(new Options([]));
        $unitarum->setOptions($options);
        $this->assertInstanceOf(OptionsInterface::class, $unitarum->getOptions());
    }

    /**
     * @dataProvider unsupportOptionsTypesData
     * @expectedException \Unitarum\Exception\UnsupportedValueException
     */
    public function testExceptionOptionsType($options)
    {
        $unitarum = new Unitarum(new Options([]));
        $unitarum->setOptions($options);
    }

    // --------------- Test Reader
    public function testGetDefaultReader()
    {
        $unitarum = new Unitarum([OptionsInterface::FIXTURE_FOLDER_OPTION => '/tmp']);
        $reader = $unitarum->getReader();
        $this->assertInstanceOf(ReaderInterface::class, $reader);
    }

    public function testSetReader()
    {
        $reader = $this->getMockBuilder(Reader::class)->disableOriginalConstructor()->getMock();
        $unitarum = new Unitarum(new Options([]));
        $unitarum->setReader($reader);
        $this->assertInstanceOf(ReaderInterface::class, $unitarum->getReader());
    }

    // -------------- Test DataBase
    public function testSetDataBase()
    {
        $database = $this->getMockBuilder(DataBase::class)->disableOriginalConstructor()->getMock();
        $unitarum = new Unitarum(new Options([]));
        $unitarum->setDataBase($database);
        $this->assertInstanceOf(DataBaseInterface::class, $unitarum->getDataBase());
    }

    public function testGetDefaultDataBase()
    {
        $unitarum = new Unitarum(new Options([]));
        $this->assertInstanceOf(DataBaseInterface::class, $unitarum->getDataBase());
    }

    // --------------- Test MagicMethod
    public function testMagicCallMethod()
    {
        $unitarum = new Unitarum([
            OptionsInterface::FIXTURE_FOLDER_OPTION => realpath(__DIR__ . DIRECTORY_SEPARATOR . '../data'),
        ]);
        $adapter = new SqliteAdapter(new \PDO('sqlite:data/sqlite.db', null, null, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]));
        $unitarum->getDataBase()->setAdapter($adapter);

        $unitarum->getDataBase()->startTransaction();

        $userEntity = new User();
        $userEntity->setName('Super Test');
        $return = $unitarum->user($userEntity);
        $this->assertInstanceOf(Unitarum::class, $return);

        $unitarum->getDataBase()->rollbackTransaction();
    }

    // --------------- Test chain of methods
    public function testChainOfMethods()
    {
        $unitarum = new Unitarum([
            OptionsInterface::FIXTURE_FOLDER_OPTION => realpath(__DIR__ . DIRECTORY_SEPARATOR . '../data')
        ]);
        $adapter = new SqliteAdapter(new \PDO('sqlite:data/sqlite.db', null, null, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]));
        $unitarum->getDataBase()->setAdapter($adapter);

        $unitarum->getDataBase()->startTransaction();

        $userEntity = new User();
        $userEntity->setId(10);
        $userEntity->setName('Chain Yung');

        $roleEntity = new Role();
        $roleEntity->setId(11);
        $roleEntity->setRole('viewer');
        $roleEntity->setUserId($userEntity->getId());

        $unitarum->user($userEntity)->role($roleEntity);

        $methodSelect = self::getProtectedMethod(DataBase::class, 'selectById');
        $result = $methodSelect->invokeArgs($unitarum->getDataBase(), [10, 'id', DataBaseTest::TEST_TABLE_USERS]);
        $this->assertTrue($result['name'] == 'Chain Yung');

        $methodSelect = self::getProtectedMethod(DataBase::class, 'selectById');
        $result = $methodSelect->invokeArgs($unitarum->getDataBase(), [11, 'id', DataBaseTest::TEST_TABLE_ROLES]);
        $this->assertTrue($result['role'] == 'viewer');
        $this->assertTrue($result['user_id'] == 10);

        $unitarum->getDataBase()->rollbackTransaction();
    }

    // --------------- Data Providers
    public function supportOptionsTypesData()
    {
        return [
            [[]],
            [new Options([])],
        ];
    }

    public function unsupportOptionsTypesData()
    {
        return [
            ['string'],
            [0],
            [new \stdClass()],
            [true],
            [null],
        ];
    }
}
