<?php

namespace IKTO\PgiMigrationDirectories\MigrationPathBuilder;

use IKTO\PgiMigrationDirectories\Migration\DefinitionInterface;

class UpgradePathBuilder extends AbstractMigrationPathBuilder
{
    /**
     * {@inheritdoc}
     */
    protected function getRelevantMigrations($startingVersion, $targetVersion)
    {
        $migrations = $this->discovery->getMigrations();

        $migrations = array_filter($migrations, function (DefinitionInterface $migration) {
            return $migration->isUpgrade();
        });
        $migrations = array_filter($migrations, function (DefinitionInterface $migration) use ($startingVersion) {
            return $migration->getStartingVersion() >= $startingVersion;
        });
        $migrations = array_filter($migrations, function (DefinitionInterface $migration) use ($targetVersion) {
            return $migration->getTargetVersion() <= $targetVersion;
        });

        return $migrations;
    }

    /**
     * {@inheritdoc}
     */
    protected function chooseMigrations($migrationsMap, $startingVersion, $targetVersion)
    {
        $currentVersion = $startingVersion;
        $chosenMigrations = [];
        while ($currentVersion < $targetVersion) {
            if (!isset($migrationsMap[$currentVersion])) {
                throw new \InvalidArgumentException(
                    sprintf('Unable to find migration from version %d', $currentVersion)
                );
            }
            $localTargetVersion = max(array_keys($migrationsMap[$currentVersion]));
            $chosenMigrations[] = $migrationsMap[$currentVersion][$localTargetVersion];
            $currentVersion = $localTargetVersion;
        }

        return $chosenMigrations;
    }
}
