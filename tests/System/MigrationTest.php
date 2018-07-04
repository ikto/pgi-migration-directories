<?php

namespace IKTO\PgiMigrationDirectories\Tests\System;

use IKTO\PgI\Database\Database;
use IKTO\PgiMigrationDirectories\Database\DefaultManagedDatabase;
use IKTO\PgiMigrationDirectories\Processor\ProcessorFactory;
use IKTO\PgiMigrationDirectories\Discovery\StandardDiscovery;
use IKTO\PgiMigrationDirectories\MigrationPathBuilder\MigrationPathBuilder;
use IKTO\PgiMigrationDirectories\MigrationPathBuilder\UpgradePathBuilder;
use IKTO\PgiMigrationDirectories\MigrationPathBuilder\AbstractMigrationPathBuilder;
use PHPUnit\Framework\TestCase;

class MigrationTest extends TestCase
{
    private static $dbs;

    private $db;

    public static function setUpBeforeClass()
    {
        $db = new Database(
            $GLOBALS['test_db_dsn'],
            $GLOBALS['test_db_user'],
            $GLOBALS['test_db_pass']
        );

        $db = new DefaultManagedDatabase($db, 'backlist', 'public');

        $db->setProcessorFactory(new ProcessorFactory());

        static::$dbs = $db;
    }

    public function setUp()
    {
        $this->db = static::$dbs;
    }
    
    public function testRollingMigrations()
    {
        $this->db->setDesiredVersion(3);

        $startingVersion = $this->db->getCurrentVersion();

        $discovery = new StandardDiscovery(__DIR__ . '/../../test_data/migrations', 'backlist');

        $builder = new MigrationPathBuilder($discovery);

        $path = $builder->getMigrationPath($startingVersion, $this->db->getDesiredVersion());

        foreach ($path as $migration) {
            $startingVersion = $migration->getStartingVersion();
            $targetVersion = $migration->getTargetVersion();

            $this->assertEquals(($startingVersion+1), $targetVersion);
        }
    }
}
