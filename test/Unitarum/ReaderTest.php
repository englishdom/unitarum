<?php

namespace UnitarumTest;

use PHPUnit\Framework\TestCase;
use Unitarum\Options;
use Unitarum\OptionsInterface;
use Unitarum\Reader;

/**
 * @package UnitarumTest
 */
class ReaderTest extends TestCase
{
    /**
     * @expectedException \Unitarum\Exception\NotExistFileException
     */
    public function testSetPathException()
    {
        $fixturePath = 'tmp';
        $options = new Options([OptionsInterface::FIXTURE_FOLDER_OPTION => $fixturePath]);
        new Reader($options);
    }

    public function testSetPath()
    {
        $fixturePath = realpath(__DIR__ . DIRECTORY_SEPARATOR . '../data');
        $options = new Options([OptionsInterface::FIXTURE_FOLDER_OPTION => $fixturePath]);
        $reader = new Reader($options);
        $this->assertEquals($fixturePath, $reader->getPath());
    }

    public function testRead()
    {
        $fixturePath = realpath(__DIR__ . DIRECTORY_SEPARATOR . '../data');
        $options = new Options([OptionsInterface::FIXTURE_FOLDER_OPTION => $fixturePath]);
        $reader = new Reader($options);

        $fixtureName = 'user';
        $file = $reader->read($fixtureName);

        $filePath = realpath(__DIR__ . DIRECTORY_SEPARATOR . '../data/user.php');
        $this->assertEquals((include $filePath), $file);
    }
}
