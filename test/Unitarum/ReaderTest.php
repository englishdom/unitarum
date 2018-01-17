<?php

namespace UnitarumTest;

use PHPUnit\Framework\TestCase;
use Unitarum\Reader;
use Unitarum\ReaderInterface;

/**
 * @package UnitarumTest
 */
class ReaderTest extends TestCase
{
    /**
     * @var ReaderInterface
     */
    private $reader;

    public function setUp()
    {
        $fixturePath = realpath(__DIR__ . DIRECTORY_SEPARATOR . '../data');
        $this->reader = new Reader($fixturePath);
    }
    /**
     * @expectedException \Unitarum\Exception\NotExistFileException
     */
    public function testSetPathException()
    {
        $fixturePath = 'tmp';
        new Reader($fixturePath);
    }

    public function testSetPath()
    {
        $fixturePath = realpath(__DIR__ . DIRECTORY_SEPARATOR . '../data');
        $reader = new Reader($fixturePath);
        $this->assertEquals($fixturePath, $reader->getPath());
    }

    public function testRead()
    {
        $filePath = realpath(__DIR__ . DIRECTORY_SEPARATOR . '../data/user.php');
        $fixtureName = 'user';
        $file = $this->reader->read($fixtureName);
        $this->assertEquals((include $filePath), $file);
    }
}
