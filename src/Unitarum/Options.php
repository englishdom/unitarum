<?php

namespace Unitarum;

/**
 * Class Options
 * @package Unitarum
 */
class Options implements OptionsInterface
{
    private $fixtureFolder;
    private $dsn;
    private $dbUserName;
    private $dbPassword;

    public function __construct(array $options)
    {
        if (isset($options[self::FIXTURE_FOLDER_OPTION])) {
            $this->setFixtureFolder($options[self::FIXTURE_FOLDER_OPTION]);
        }
        if (isset($options[self::DSN_OPTION])) {
            $this->setDsn($options[self::DSN_OPTION]);
        }
        if (isset($options[self::DB_USERNAME])) {
            $this->setDbUserName($options[self::DB_USERNAME]);
        }
        if (isset($options[self::DB_PASSWORD])) {
            $this->setDbPassword($options[self::DB_PASSWORD]);
        }
    }

    /**
     * @return mixed
     */
    public function getFixtureFolder()
    {
        return $this->fixtureFolder;
    }

    /**
     * @param mixed $fixtureFolder
     * @return Options
     */
    public function setFixtureFolder($fixtureFolder): self
    {
        $this->fixtureFolder = $fixtureFolder;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDsn()
    {
        return $this->dsn;
    }

    /**
     * @param mixed $dsn
     * @return Options
     */
    public function setDsn($dsn): self
    {
        $this->dsn = $dsn;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDbUserName()
    {
        return $this->dbUserName;
    }

    /**
     * @param mixed $dbUserName
     * @return Options
     */
    public function setDbUserName($dbUserName)
    {
        $this->dbUserName = $dbUserName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDbPassword()
    {
        return $this->dbPassword;
    }

    /**
     * @param mixed $dbPassword
     * @return Options
     */
    public function setDbPassword($dbPassword)
    {
        $this->dbPassword = $dbPassword;
        return $this;
    }
}
