<?php

namespace Unitarum;

interface DataBaseInterface
{

    /**
     * DataBase constructor.
     * @param OptionsInterface $options
     */
    public function __construct(OptionsInterface $options);

    /**
     * @return \ArrayObject
     */
    public function getCollection(): \ArrayObject;
}