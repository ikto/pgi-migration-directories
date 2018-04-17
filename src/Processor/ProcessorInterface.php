<?php

namespace IKTO\PgiMigrationDirectories\Processor;

use IKTO\PgI\Database\ConvenientDatabaseInterface;
use IKTO\PgiMigrationDirectories\Migration\DefinitionInterface;

interface ProcessorInterface
{
    /**
     * Applies the migration via the adapter.
     *
     * @param ConvenientDatabaseInterface $db
     *   The db to apply migration.
     * @param DefinitionInterface $migration
     *   The migration to apply.
     */
    public function applyMigration(ConvenientDatabaseInterface $db, DefinitionInterface $migration);
}
