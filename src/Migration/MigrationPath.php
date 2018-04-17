<?php

namespace IKTO\PgiMigrationDirectories\Migration;

class MigrationPath implements PathInterface
{
    /**
     * @var DefinitionInterface[]
     */
    protected $migrations = [];

    /**
     * @var int
     */
    private $position = 0;

    /**
     * MigrationPath constructor.
     *
     * @param array $migrations
     *   The array of migrations.
     */
    public function __construct(array $migrations)
    {
        if (!$migrations) {
            throw new \InvalidArgumentException('The migration path should not be empty.');
        }
        $this->migrations = $migrations;
    }

    /**
     * {@inheritdoc}
     */
    public function getStartingVersion()
    {
        return reset($this->migrations)->getStartingVersion();
    }

    /**
     * {@inheritdoc}
     */
    public function getTargetVersion()
    {
        return end($this->migrations)->getTargetVersion();
    }

    /**
     * @return DefinitionInterface
     */
    public function current()
    {
        return $this->migrations[$this->position];
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->position++;
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return array_key_exists($this->position, $this->migrations);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->migrations);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->migrations);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->migrations[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        throw new \LogicException('The migration path does not allow to overwrite migrations');
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        throw new \LogicException('The migration path does not allow to overwrite migrations');
    }
}
