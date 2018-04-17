<?php

namespace IKTO\PgiMigrationDirectories\Migration;

interface PathInterface extends \Iterator, \Countable, \ArrayAccess
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
}
