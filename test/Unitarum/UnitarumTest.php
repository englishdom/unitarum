<?php

namespace UnitarumTest;

use PHPUnit\Framework\TestCase;
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
    /**
     * @var Unitarum
     */
    private $unitarum;

    public function setUp()
    {
        $this->unitarum = new Unitarum(new Options());
    }

    /**
     * @dataProvider supportOptionsTypesData
     */
    public function testSupportOptions($options)
    {
        $this->unitarum->setOptions($options);
        $returnOptions = $this->unitarum->getOptions();

        $this->assertInstanceOf(OptionsInterface::class, $returnOptions);
    }

    /**
     * @dataProvider unsupportOptionsTypesData
     * @expectedException \Unitarum\Exception\UnsupportedValueException
     */
    public function testExceptionOptionsType($options)
    {
        $this->unitarum->setOptions($options);
    }

    public function testGetDefaultReader()
    {
        $unitarum = new Unitarum([OptionsInterface::FIXTURE_FOLDER_OPTION => '/tmp']);
        $reader = $unitarum->getReader();
        $this->assertInstanceOf(ReaderInterface::class, $reader);
    }

    public function testSetReader()
    {
        $reader = $this->getMockBuilder(Reader::class)->disableOriginalConstructor()->getMock();
        $unitarum = new Unitarum(new Options());
        $unitarum->setReader($reader);
        $this->assertInstanceOf(ReaderInterface::class, $reader);
    }

    public function testMagicCallMethod()
    {
        $unitarum = new Unitarum([OptionsInterface::FIXTURE_FOLDER_OPTION => realpath(__DIR__ . DIRECTORY_SEPARATOR . '../data')]);
        $return = $unitarum->user();
        $this->assertInstanceOf(Unitarum::class, $return);
    }

    public function testGetCollection()
    {
        $collection = $this->unitarum->getCollection();
        $this->assertInstanceOf(\SplObjectStorage::class, $collection);
    }

    public function supportOptionsTypesData()
    {
        return [
            [[]],
            [new Options()],
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
