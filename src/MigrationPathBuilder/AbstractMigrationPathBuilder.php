<?php

namespace IKTO\PgiMigrationDirectories\MigrationPathBuilder;

use IKTO\PgiMigrationDirectories\Discovery\DiscoveryInterface;
use IKTO\PgiMigrationDirectories\Migration\DefinitionInterface;
use IKTO\PgiMigrationDirectories\Migration\MigrationPath;

abstract class AbstractMigrationPathBuilder implements MigrationPathBuilderInterface
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
        $migrations = $this->getRelevantMigrations($startingVersion, $targetVersion);

        $migrationsMap = $this->buildMigrationsMap($migrations);

        $chosenMigrations = $this->chooseMigrations($migrationsMap, $startingVersion, $targetVersion);

        return new MigrationPath($chosenMigrations);
    }

    /**
     * Gets relevant migrations from the available.
     *
     * @param int $startingVersion
     *   The db version to migrate from.
     * @param int $targetVersion
     *   The db version to migrate to.
     *
     * @return DefinitionInterface[]
     */
    abstract protected function getRelevantMigrations($startingVersion, $targetVersion);

    /**
     * Builds migrations map.
     *
     * @param DefinitionInterface[] $migrations
     *
     * @return array
     */
    protected function buildMigrationsMap(array $migrations)
    {
        $migrationsMap = [];
        foreach ($migrations as $migration) {
            $migrationsMap[$migration->getStartingVersion()][$migration->getTargetVersion()] = $migration;
        }

        return $migrationsMap;
    }

    /**
     * Gets migrations array which is need to be applied to get to target version.
     *
     * @param array $migrationsMap
     *   The migrations map.
     * @param int $startingVersion
     *   The db version to migrate from.
     * @param int $targetVersion
     *   The db version to migrate to.
     *
     * @return DefinitionInterface[]
     */
    abstract protected function chooseMigrations($migrationsMap, $startingVersion, $targetVersion);
}
