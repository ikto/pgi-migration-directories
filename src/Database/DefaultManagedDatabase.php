<?php

namespace IKTO\PgiMigrationDirectories\Database;

use IKTO\PgI\Database\ConvenientDatabaseInterface as PgiConvenientDatabaseInterface;
use IKTO\PgI\Database\DatabaseInterface as PgiDatabaseInterface;
use IKTO\PgiMigrationDirectories\Migration\DefinitionInterface;
use IKTO\PgiMigrationDirectories\Processor\ProcessorFactory;
use IKTO\PgiMigrationDirectories\StateManager\StateManager;
use IKTO\PgiMigrationDirectories\StateManager\StateManagerInterface;

class DefaultManagedDatabase implements ManagedDatabaseInterface
{
    /**
     * @var PgiDatabaseInterface|PgiConvenientDatabaseInterface
     */
    protected $dbh;

    /**
     * @var StateManagerInterface
     */
    protected $stateManager;

    /**
     * @var ProcessorFactory
     */
    protected $processorFactory;

    /**
     * @var int
     */
    protected $desiredVersion;

    public function __construct(PgiDatabaseInterface $dbh, $migrationSchemaName, $storageSchemaName)
    {
        $this->dbh = $dbh;
        $this->stateManager = new StateManager($this->dbh, $migrationSchemaName);
        $this->stateManager->setMigrationSchemaLogTableSchemaName($storageSchemaName);
        $this->stateManager->setMigrationSchemaVersionTableSchemaName($storageSchemaName);
    }

    /**
     * @param ProcessorFactory $processorFactory
     */
    public function setProcessorFactory(ProcessorFactory $processorFactory)
    {
        $this->processorFactory = $processorFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getDesiredVersion()
    {
        return $this->desiredVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function setDesiredVersion($desiredVersion)
    {
        $this->desiredVersion = $desiredVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentVersion()
    {
        return $this->stateManager->getCurrentVersion();
    }

    public function applyMigration(DefinitionInterface $migration)
    {
        $processor = $this->processorFactory->getProcessorForMigration($migration);
        $processor->applyMigration($this->dbh, $migration);
        $this->stateManager->setCurrentVersion($migration->getTargetVersion());
    }

    /**
     * {@inheritdoc}
     */
    public function openTransaction()
    {
        $this->dbh->beginWork();
    }

    /**
     * {@inheritdoc}
     */
    public function commitTransaction()
    {
        $this->dbh->commit();
    }

    /**
     * {@inheritdoc}
     */
    public function rollbackTransaction()
    {
        $this->dbh->rollback();
    }
}
