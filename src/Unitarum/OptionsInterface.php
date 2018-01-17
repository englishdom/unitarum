<?php

namespace Unitarum;

/**
 * Class OptionsInterface
 * @package Unitarum
 */
interface OptionsInterface
{

    const FIXTURE_FOLDER_OPTION = 'fixtureFolder';

    public function parse(array $options);

    /**
     * @return mixed
     */
    public function getFixtureFolder();

    /**
     * @param mixed $fixtureFolder
     */
    public function setFixtureFolder($fixtureFolder): void;
}
