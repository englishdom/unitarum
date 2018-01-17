<?php

namespace Unitarum;

/**
 * Class Options
 * @package Unitarum
 */
class Options implements OptionsInterface
{
    private $fixtureFolder;

    public function parse(array $options)
    {
        if (isset($options[self::FIXTURE_FOLDER_OPTION])) {
            $this->setFixtureFolder($options[self::FIXTURE_FOLDER_OPTION]);
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
     */
    public function setFixtureFolder($fixtureFolder): void
    {
        $this->fixtureFolder = $fixtureFolder;
    }
}
