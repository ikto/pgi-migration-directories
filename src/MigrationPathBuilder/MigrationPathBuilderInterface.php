<?php

namespace IKTO\PgiMigrationDirectories\MigrationPathBuilder;

use IKTO\PgiMigrationDirectories\Migration\PathInterface;

interface MigrationPathBuilderInterface
{
    /**
     * Generates migration path.
     *
     * @param int $startingVersion
     *   The db version to migrate from.
     * @param int $targetVersion
     *   The db version to migrate to.
     *
     * @return PathInterface
     *   The migration path.
     */
    public function getMigrationPath($startingVersion, $targetVersion);
}
