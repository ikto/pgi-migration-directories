<?php

namespace IKTO\PgiMigrationDirectories\StateManager;

use IKTO\PgI\Database\ConvenientDatabaseInterface;

class StateManager implements StateManagerInterface
{
    /**
     * @var ConvenientDatabaseInterface
     */
    protected $db;

    /**
     * @var string
     */
    protected $schemaName;

    /**
     * The table name for storing current db version.
     *
     * @var string
     */
    protected $migrationSchemaVersionTableName = 'migration_schema_version';

    /**
     * The table schema name for storing current db version.
     *
     * @var string
     */
    protected $migrationSchemaVersionTableSchemaName = null;

    /**
     * The table name for storing db migrations log.
     *
     * @var string
     */
    protected $migrationSchemaLogTableName = 'migration_schema_log';

    /**
     * The table schema name for storing db migrations log.
     *
     * @var string
     */
    protected $migrationSchemaLogTableSchemaName = null;

    public function __construct(ConvenientDatabaseInterface $db, $schemaName)
    {
        $this->db = $db;
        $this->schemaName = $schemaName;
    }

    /**
     * @param string $migrationSchemaVersionTableName
     */
    public function setMigrationSchemaVersionTableName($migrationSchemaVersionTableName)
    {
        $this->migrationSchemaVersionTableName = $migrationSchemaVersionTableName;
    }

    /**
     * @param string $migrationSchemaVersionTableSchemaName
     */
    public function setMigrationSchemaVersionTableSchemaName($migrationSchemaVersionTableSchemaName)
    {
        $this->migrationSchemaVersionTableSchemaName = $migrationSchemaVersionTableSchemaName;
    }

    /**
     * @param string $migrationSchemaLogTableName
     */
    public function setMigrationSchemaLogTableName($migrationSchemaLogTableName)
    {
        $this->migrationSchemaLogTableName = $migrationSchemaLogTableName;
    }

    /**
     * @param string $migrationSchemaLogTableSchemaName
     */
    public function setMigrationSchemaLogTableSchemaName($migrationSchemaLogTableSchemaName)
    {
        $this->migrationSchemaLogTableSchemaName = $migrationSchemaLogTableSchemaName;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentVersion()
    {
        if (!$this->migrationSchemaVersionTableExists()) {
            return 0;
        }

        if (!$this->migrationSchemaVersionRecordExists($this->schemaName)) {
            return 0;
        }

        $sql = 'SELECT "version" FROM '.$this->getMigrationSchemaVersionTableLiteral().' WHERE "name" = $1';

        list ($version) = $this->db->selectRowArray($sql, [], [$this->schemaName]);

        return $version;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentVersion($version)
    {
        if (!$this->migrationSchemaVersionTableExists()) {
            throw new \RuntimeException('The migration schema version table does not exist in db');
        }

        $startingVersion = $this->getCurrentVersion();

        if ($this->migrationSchemaVersionRecordExists($this->schemaName)) {
            $this->updateMigrationSchemaVersion($this->schemaName, $version);
        } else {
            $this->insertMigrationSchemaVersion($this->schemaName, $version);
        }

        if ($this->migrationSchemaLogTableExists()) {
            $this->insertMigrationSchemaLog($this->schemaName, $startingVersion, $version);
        }
    }

    /**
     * Gets migration schema version table literal (for using in queries).
     *
     * @return null|string
     */
    protected function getMigrationSchemaVersionTableLiteral()
    {
        $tableLiteral = null;

        if ($this->migrationSchemaVersionTableName) {
            $tableLiteral = '"'.$this->migrationSchemaVersionTableName.'"';

            if ($this->migrationSchemaVersionTableSchemaName) {
                $tableLiteral = '"'.$this->migrationSchemaVersionTableSchemaName.'".'.$tableLiteral;
            }
        }

        return $tableLiteral;
    }

    /**
     * Gets migration schema log table literal (for using in queries).
     *
     * @return null|string
     */
    protected function getMigrationSchemaLogTableLiteral()
    {
        $tableLiteral = null;

        if ($this->migrationSchemaLogTableName) {
            $tableLiteral = '"'.$this->migrationSchemaLogTableName.'"';

            if ($this->migrationSchemaLogTableSchemaName) {
                $tableLiteral = '"'.$this->migrationSchemaLogTableSchemaName.'".'.$tableLiteral;
            }
        }

        return $tableLiteral;
    }

    /**
     * Checks whether the migration schema version table exists in db.
     *
     * @return bool
     */
    protected function migrationSchemaVersionTableExists()
    {
        return $this->tableExists($this->migrationSchemaVersionTableName, $this->migrationSchemaVersionTableSchemaName);
    }

    /**
     * Checks whether the migration schema log table exists in db.
     *
     * @return bool
     */
    protected function migrationSchemaLogTableExists()
    {
        return $this->tableExists($this->migrationSchemaLogTableName, $this->migrationSchemaLogTableSchemaName);
    }

    /**
     * Checks whether specified table exists in db.
     *
     * @param string $tableName
     *   The table name to check.
     * @param string $tableSchema
     *   The table schema name to check.
     *
     * @return bool
     */
    protected function tableExists($tableName, $tableSchema = null)
    {
        $args = [$tableName];
        $sql = 'SELECT EXISTS (';
        $sql .= 'SELECT 1 FROM "information_schema"."tables" WHERE "table_name" = $1';
        if ($tableSchema) {
            $sql .= ' AND "table_schema" = $2';
            $args[] = $tableSchema;
        }
        $sql .= ')';

        list ($exists) = $this->db->selectRowArray($sql, [], $args);

        return $exists;
    }

    /**
     * Check whether the migration schema version record already exists in db.
     *
     * @param string $schemaName
     *   The migration schema name.
     *
     * @return bool
     */
    protected function migrationSchemaVersionRecordExists($schemaName)
    {
        $sql = 'SELECT EXISTS (SELECT 1 FROM '.$this->getMigrationSchemaVersionTableLiteral().' WHERE "name" = $1)';
        list ($exists) = $this->db->selectRowArray($sql, [], [$schemaName]);

        return $exists;
    }

    /**
     * Creates migration schema version record.
     *
     * @param $schemaName
     *   The migration schema name.
     * @param $version
     *   The db version.
     */
    protected function insertMigrationSchemaVersion($schemaName, $version)
    {
        $sql = 'INSERT INTO '.$this->getMigrationSchemaVersionTableLiteral().' ("name", "version") VALUES ($1, $2)';
        $this->db->doQuery($sql, [], [$schemaName, $version]);
    }

    /**
     * Updates migration schema version record.
     *
     * @param $schemaName
     *   The migration schema name.
     * @param $version
     *   The db version.
     */
    protected function updateMigrationSchemaVersion($schemaName, $version)
    {
        $sql = 'UPDATE '.$this->getMigrationSchemaVersionTableLiteral().' SET "version" = $1 WHERE "name" = $2';
        $this->db->doQuery($sql, [], [$version, $schemaName]);
    }

    /**
     * Logs the db migration event.
     *
     * @param $schemaName
     *   The migration schema name.
     * @param $startingVersion
     *   The db version we migrating from.
     * @param $targetVersion
     *   The db version we migrating to.
     */
    protected function insertMigrationSchemaLog($schemaName, $startingVersion, $targetVersion)
    {
        $sql = 'INSERT INTO'.$this->getMigrationSchemaLogTableLiteral().
            ' ("schema_name", "event_time", "old_version", "new_version") VALUES ($1, NOW(), $2, $3)';
        $this->db->doQuery($sql, [], [$schemaName, $startingVersion, $targetVersion]);
    }
}
