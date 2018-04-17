<?php

namespace IKTO\PgiMigrationDirectories\Migration;

interface DefinitionInterface
{
    /**
     * Gets db version to migrate from.
     *
     * @return int
     */
    public function getStartingVersion();

    /**
     * Gets db version to migrate to.
     *
     * @return int
     */
    public function getTargetVersion();

    /**
     * Indicates that this migration is upgrade.
     *
     * @return bool
     */
    public function isUpgrade();

    /**
     * Indicates that this migration is downgrade.
     *
     * @return bool
     */
    public function isDowngrade();
}
