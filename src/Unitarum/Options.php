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

    public function __construct(array $options)
    {
        if (isset($options[self::FIXTURE_FOLDER_OPTION])) {
            $this->setFixtureFolder($options[self::FIXTURE_FOLDER_OPTION]);
        }
        if (isset($options[self::DSN_OPTION])) {
            $this->setDsn($options[self::DSN_OPTION]);
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
}
