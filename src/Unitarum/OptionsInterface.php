<?php

namespace Unitarum;

/**
 * Class OptionsInterface
 * @package Unitarum
 */
interface OptionsInterface
{
    const FIXTURE_FOLDER_OPTION = 'fixtureFolder';
    const DSN_OPTION = 'dsn';
    const DB_USERNAME = 'dbUserName';
    const DB_PASSWORD = 'dbPassword';

    public function __construct(array $options);

    /**
     * @return mixed
     */
    public function getFixtureFolder();

    /**
     * @param mixed $fixtureFolder
     * @return OptionsInterface
     */
    public function setFixtureFolder($fixtureFolder);

    /**
     * @return mixed
     */
    public function getDsn();

    /**
     * @param mixed $dsn
     * @return Options
     */
    public function setDsn($dsn);

    /**
     * @return mixed
     */
    public function getDbUserName();

    /**
     * @param mixed $dbUserName
     * @return Options
     */
    public function setDbUserName($dbUserName);

    /**
     * @return mixed
     */
    public function getDbPassword();

    /**
     * @param mixed $dbPassword
     * @return Options
     */
    public function setDbPassword($dbPassword);
}
