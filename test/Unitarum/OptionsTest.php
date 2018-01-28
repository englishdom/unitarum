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
        $dbUserName = 'username';
        $dbPassword = 'password';

        $params = [
            OptionsInterface::FIXTURE_FOLDER_OPTION => $fixtureFolder,
            OptionsInterface::DSN_OPTION => $dsn,
            OptionsInterface::DB_USERNAME => $dbUserName,
            OptionsInterface::DB_PASSWORD => $dbPassword,
        ];

        $options = new Options($params);
        $this->assertEquals($fixtureFolder, $options->getFixtureFolder());
        $this->assertEquals($dsn, $options->getDsn());
        $this->assertEquals($dbUserName, $options->getDbUserName());
        $this->assertEquals($dbPassword, $options->getDbPassword());
    }
}
