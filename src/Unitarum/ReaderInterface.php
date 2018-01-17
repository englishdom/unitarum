<?php

namespace Unitarum;
use Unitarum\Exception\NotExistFileException;

/**
 * Class ReaderInterface
 * @package Unitarum
 */
interface ReaderInterface
{
    /**
     * FixtureReader constructor.
     * @param OptionsInterface $options
     * @throws NotExistFileException
     */
    public function __construct(OptionsInterface $options);

    /**
     * @param string $fixtureName
     */
    public function read(string $fixtureName);

    public function getPath(): string;
}
