<?php

namespace Unitarum;

interface DataBaseInterface
{
    /**
     * DataBase constructor.
     * @param OptionsInterface $options
     */
    public function __construct(OptionsInterface $options);

    public function execute($defaultEntity, $incomeEntity, $tableAlias);

    public function startTransaction();

    public function rollbackTransaction();
}