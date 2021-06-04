<?php

namespace IKTO\PgiMigrationDirectories\Tests\System;

use IKTO\PgI\Database\Database;
use IKTO\PgiMigrationDirectories\Adapter\PgiConnectionAdapter;
use IKTO\PgMigrationDirectories\Database\DefaultManagedDatabase;
use IKTO\PgMigrationDirectories\Processor\DefaultProcessorFactory;
use IKTO\PgMigrationDirectories\Discovery\SqlFilesDiscovery;
use IKTO\PgMigrationDirectories\MigrationPathBuilder\MigrationPathBuilder;
use PHPUnit\Framework\TestCase;

class MigrationsIntegrationTest extends TestCase
{
    private static $dbs;

    /**
     * @var \IKTO\PgMigrationDirectories\Database\ManagedDatabaseInterface
     */
    private $db;

    public static function setUpBeforeClass()
    {
        $db = new Database(
            $GLOBALS['test_db_dsn'],
            $GLOBALS['test_db_user'],
            $GLOBALS['test_db_pass']
        );

        $db = new DefaultManagedDatabase(new PgiConnectionAdapter($db), 'backlist', 'public');

        $db->setProcessorFactory(new DefaultProcessorFactory());

        static::$dbs = $db;
    }

    public function setUp()
    {
        $this->db = static::$dbs;
    }

    /**
     * @dataProvider dbSchemaVersionsProvider
     * @param int $desiredVersion
     */
    public function testRollingMigrations($desiredVersion)
    {
        $this->db->setDesiredVersion($desiredVersion);

        $startingVersion = $this->db->getCurrentVersion();

        $discovery = new SqlFilesDiscovery(__DIR__ . '/../../test_data/migrations', 'backlist');

        $builder = new MigrationPathBuilder($discovery);

        $path = $builder->getMigrationPath($startingVersion, $this->db->getDesiredVersion());
        $this->assertEquals($startingVersion, $path->getStartingVersion());
        $this->assertEquals($desiredVersion, $path->getTargetVersion());

        $this->db->openTransaction();
        foreach ($path as $migration) {
            $this->assertEquals($migration->getStartingVersion(), $this->db->getCurrentVersion());
            $this->db->applyMigration($migration);
            $this->assertEquals($migration->getTargetVersion(), $this->db->getCurrentVersion());
        }
        $this->db->commitTransaction();
        $this->assertEquals($desiredVersion, $this->db->getCurrentVersion());
    }

    public function dbSchemaVersionsProvider()
    {
        return array_map(function ($value) {
            return [$value];
        }, [3, 6, 5, 1, 2, 4, 6, 1, 6]);
    }
}
