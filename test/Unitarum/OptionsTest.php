<?php

namespace UnitarumTest;

use PHPUnit\Framework\TestCase;
use Unitarum\Options;
use Unitarum\OptionsInterface;

class OptionsTest extends TestCase
{
    /**
     * @var OptionsInterface
     */
    private $options;

    public function setUp()
    {
        $this->options = new Options();
    }

    public function testSetFixtureFolder()
    {
        $fixtureFolder = 'data';
        $this->options->setFixtureFolder($fixtureFolder);
        $this->assertEquals($fixtureFolder, $this->options->getFixtureFolder());
    }

    public function testParseOptions()
    {
        $fixtureFolder = 'data';
        $options = [
            OptionsInterface::FIXTURE_FOLDER_OPTION => $fixtureFolder
        ];

        $this->options->parse($options);
        $this->assertEquals($fixtureFolder, $this->options->getFixtureFolder());
    }

    public function testPDOConnectionOptions()
    {

    }
}
