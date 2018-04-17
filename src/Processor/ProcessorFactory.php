<?php

namespace IKTO\PgiMigrationDirectories\Processor;

use IKTO\PgiMigrationDirectories\Migration\DefinitionInterface;
use IKTO\PgiMigrationDirectories\Migration\StandardMigrationDefinition;

class ProcessorFactory
{
    /**
     * Gets the migration processor for the migration.
     *
     * @param DefinitionInterface $migration
     *   The migration to get processor for.
     *
     * @return ProcessorInterface
     */
    public function getProcessorForMigration(DefinitionInterface $migration)
    {
        if ($migration instanceof StandardMigrationDefinition) {
            return new StandardProcessor();
        }

        throw new \InvalidArgumentException(sprintf('Unable to fund processor for the "%s"', get_class($migration)));
    }
}
