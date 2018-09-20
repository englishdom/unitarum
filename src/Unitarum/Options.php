<?php

namespace Unitarum;

/**
 * Class Options
 * @package Unitarum
 */
class Options implements OptionsInterface
{
    private $fixtureFolder;
    private $whiteList;

    public function __construct(array $options)
    {
        if (isset($options[self::FIXTURE_FOLDER_OPTION])) {
            $this->setFixtureFolder($options[self::FIXTURE_FOLDER_OPTION]);
        }
        if (isset($options[self::WHITE_LIST_FOR_FIELD_IN_DB])) {
            $this->setWhiteList($options[self::WHITE_LIST_FOR_FIELD_IN_DB]);
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
    public function getWhiteList()
    {
        return $this->whiteList;
    }

    /**
     * @param mixed $whiteList
     */
    public function setWhiteList($whiteList): self
    {
        $this->whiteList = $whiteList;
        return $this;
    }
}
