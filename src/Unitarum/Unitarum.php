<?php

namespace Unitarum;

use Unitarum\Exception\UnsupportedValueException;

/**
 * @package Unitarum
 */
class Unitarum
{
    /**
     * @var OptionsInterface
     */
    protected $options;

    /**
     * @var ReaderInterface
     */
    protected $reader;

    /**
     * @var \SplObjectStorage
     */
    protected $collection;

    /**
     * Unitarum constructor.
     * @param OptionsInterface|array $options
     */
    public function __construct($options)
    {
        $this->setOptions($options);
    }

    /**
     * @param OptionsInterface|array $options
     * @return Unitarum
     */
    public function setOptions($options): self
    {
        if (is_array($options)) {
            $this->options = new Options();
            $this->options->parse($options);
        } elseif ($options instanceof OptionsInterface) {
            $this->options = $options;
        } else {
            throw new UnsupportedValueException('Unsupported type of options.');
        }

        return $this;
    }

    public function getOptions(): OptionsInterface
    {
        return $this->options;
    }

    /**
     * @param $fixtureName
     * @param $arguments
     * @return $this
     */
    public function __call($fixtureName, $arguments)
    {
        /* Read default data from file */
        $data = $this->getReader()->read($fixtureName);
        /* The data change in dataSet */

        /* The data append */
        return $this;
    }

    /**
     * @return ReaderInterface
     */
    public function getReader(): ReaderInterface
    {
        if (!$this->reader instanceof ReaderInterface) {
            $this->reader = new Reader($this->options->getFixtureFolder());
        }
        return $this->reader;
    }

    /**
     * @param ReaderInterface $reader
     * @return Unitarum
     */
    public function setReader(ReaderInterface $reader): self
    {
        $this->reader = $reader;
        return $this;
    }

    /**
     * @return \SplObjectStorage
     */
    public function getCollection(): \SplObjectStorage
    {
        if (!$this->collection instanceof \SplObjectStorage) {
            $this->collection = new \SplObjectStorage();
        }
        return $this->collection;
    }
}
