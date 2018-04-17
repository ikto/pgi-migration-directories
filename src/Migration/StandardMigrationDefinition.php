<?php

namespace IKTO\PgiMigrationDirectories\Migration;

class StandardMigrationDefinition implements DefinitionInterface
{
    /**
     * @var string
     */
    protected $base;

    /**
     * @var int
     */
    protected $startingVersion;

    /**
     * @var int
     */
    protected $targetVersion;

    /**
     * StandardMigrationDefinition constructor.
     *
     * @param string$base
     *   The path where migration files are stored.
     */
    public function __construct($base)
    {
        $this->base = $base;
        $this->parseBasename(basename($base));
    }

    /**
     * {@inheritdoc}
     */
    public function getStartingVersion()
    {
        return $this->startingVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function getTargetVersion()
    {
        return $this->targetVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function isUpgrade()
    {
        return $this->startingVersion < $this->targetVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function isDowngrade()
    {
        return $this->startingVersion > $this->targetVersion;
    }

    /**
     * Gets the path migration files are stored.
     *
     * @return string
     */
    public function getBase()
    {
        return $this->base;
    }

    /**
     * Parse migration base name.
     *
     * @param string $base
     */
    protected function parseBasename($base)
    {
        if (preg_match('/^(\d+)\-(\d+)$/', $base, $matches)) {
            $this->startingVersion = (int) ltrim($matches[1], '0');
            $this->targetVersion = (int) ltrim($matches[2], '0');
        } elseif (preg_match('/^(\d+)$/', $base, $matches)) {
            $this->startingVersion = 0;
            $this->targetVersion = (int) ltrim($matches[1], '0');
        } else {
            throw new \InvalidArgumentException(sprintf('Invalid migration base name "%s"', $base));
        }
    }
}
