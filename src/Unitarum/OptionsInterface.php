<?php

namespace Unitarum;

/**
 * Class OptionsInterface
 * @package Unitarum
 */
interface OptionsInterface
{
    const FIXTURE_FOLDER_OPTION = 'fixtureFolder';
    const WHITE_LIST_FOR_FIELD_IN_DB = 'whiteListForFieldInDb';

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
    public function getWhiteList();

    /**
     * @param $whiteList
     * @return mixed
     */
    public function setWhiteList($whiteList);
}
