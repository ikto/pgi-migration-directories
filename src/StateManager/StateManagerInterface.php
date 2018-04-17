<?php

namespace IKTO\PgiMigrationDirectories\StateManager;

interface StateManagerInterface
{
    /**
     * Gets current db version.
     *
     * @return int
     *   Current db version.
     */
    public function getCurrentVersion();

    /**
     * Sets current db version.
     *
     * @param int $version
     *   New db version.
     */
    public function setCurrentVersion($version);
}
