<?php

namespace IKTO\PgiMigrationDirectories\Database;

use IKTO\PgiMigrationDirectories\Migration\DefinitionInterface;

interface ManagedDatabaseInterface
{
    /**
     * Gets desired db version for this managed db.
     *
     * @return int
     */
    public function getDesiredVersion();

    /**
     * Sets desired db version for this managed db.
     *
     * @param int $desiredVersion
     */
    public function setDesiredVersion($desiredVersion);

    /**
     * Gets the current version of the database.
     *
     * @return int
     */
    public function getCurrentVersion();

    /**
     * Apply the migration.
     *
     * @param DefinitionInterface $migration
     *   The migration to apply.
     */
    public function applyMigration(DefinitionInterface $migration);

    /**
     * Start transaction.
     */
    public function openTransaction();

    /**
     * Commit transaction.
     */
    public function commitTransaction();

    /**
     * Roll transaction back.
     */
    public function rollbackTransaction();
}
