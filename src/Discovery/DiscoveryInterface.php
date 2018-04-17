<?php

namespace IKTO\PgiMigrationDirectories\Discovery;

use IKTO\PgiMigrationDirectories\Migration\DefinitionInterface;

interface DiscoveryInterface
{
    /**
     * Gets existing migrations array.
     *
     * @return DefinitionInterface[]
     */
    public function getMigrations();
}
