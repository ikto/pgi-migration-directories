<?php

namespace IKTO\PgiMigrationDirectories\MigrationPathBuilder;

use IKTO\PgiMigrationDirectories\Discovery\DiscoveryInterface;

class MigrationPathBuilder implements MigrationPathBuilderInterface
{
    /**
     * @var DiscoveryInterface
     */
    protected $discovery;

    /**
     * MigrationPathBuilder constructor.
     *
     * @param DiscoveryInterface $discovery
     */
    public function __construct(DiscoveryInterface $discovery)
    {
        $this->discovery = $discovery;
    }

    /**
     * {@inheritdoc}
     */
    public function getMigrationPath($startingVersion, $targetVersion)
    {
        if ($startingVersion < $targetVersion) {
            return (new UpgradePathBuilder($this->discovery))->getMigrationPath($startingVersion, $targetVersion);
        } elseif ($startingVersion > $targetVersion) {
            return (new DowngradePathBuilder($this->discovery))->getMigrationPath($startingVersion, $targetVersion);
        } else {
            throw new \InvalidArgumentException('Versions should be different');
        }
    }
}
