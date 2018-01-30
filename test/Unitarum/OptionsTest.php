<?php

namespace UnitarumTest;

use PHPUnit\Framework\TestCase;
use Unitarum\Options;
use Unitarum\OptionsInterface;

class OptionsTest extends TestCase
{
    public function testOptionsConstructor()
    {
        $fixtureFolder = 'data';

        $params = [
            OptionsInterface::FIXTURE_FOLDER_OPTION => $fixtureFolder,
        ];

        $options = new Options($params);
        $this->assertEquals($fixtureFolder, $options->getFixtureFolder());
    }
}
