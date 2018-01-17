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
     * @param string $fixturePath
     * @throws NotExistFileException
     */
    public function __construct(string $fixturePath);

    /**
     * @param string $fixtureName
     */
    public function read(string $fixtureName);

    public function getPath(): string;
}
