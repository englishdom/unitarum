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
        $dsn = 'dsn:';
        $params = [
            OptionsInterface::FIXTURE_FOLDER_OPTION => $fixtureFolder,
            OptionsInterface::DSN_OPTION => $dsn
        ];

        $options = new Options($params);
        $this->assertEquals($fixtureFolder, $options->getFixtureFolder());
        $this->assertEquals($dsn, $options->getDsn());
    }
}
