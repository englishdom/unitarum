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
     * @return array
     */
    public function getCollection(): array;

    public function execute($defaultData, $changeData);

    public function startTransaction();

    public function rollbackTransaction();
}