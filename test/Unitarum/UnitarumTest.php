<?php

namespace UnitarumTest;

use PHPUnit\Framework\TestCase;
use Unitarum\DataBase;
use Unitarum\DataBaseInterface;
use Unitarum\Options;
use Unitarum\OptionsInterface;
use Unitarum\Reader;
use Unitarum\ReaderInterface;
use Unitarum\Unitarum;

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
        $unitarum = new Unitarum(new Options([OptionsInterface::DSN_OPTION => 'sqlite::memory:']));
        $this->assertInstanceOf(DataBaseInterface::class, $unitarum->getDataBase());
    }

    // --------------- Test MagicMethod
    public function testMagicCallMethod()
    {
        $unitarum = new Unitarum([
            OptionsInterface::FIXTURE_FOLDER_OPTION => realpath(__DIR__ . DIRECTORY_SEPARATOR . '../data'),
            OptionsInterface::DSN_OPTION => 'sqlite:data/sqlite.db',
        ]);
        $unitarum->getDataBase()->startTransaction();

        $return = $unitarum->user(['name' => 'Super Test']);
        $this->assertInstanceOf(Unitarum::class, $return);

        $unitarum->getDataBase()->rollbackTransaction();
    }

    // --------------- Test chain of methods
    public function testChainOfMethods()
    {
        $unitarum = new Unitarum([
            OptionsInterface::FIXTURE_FOLDER_OPTION => realpath(__DIR__ . DIRECTORY_SEPARATOR . '../data'),
            OptionsInterface::DSN_OPTION => 'sqlite:data/sqlite.db',
        ]);

        $unitarum->getDataBase()->startTransaction();

        $unitarum->user(['name' => 'Bob'])->role(['role' => 'viewer', 'user_id' => '{{user.id}}']);

        /* @TODO Check data in table */

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
