# PgI Migration Directories

[![Build Status](https://travis-ci.org/ikto/pgi-migration-directories.svg?branch=dev)](https://travis-ci.org/ikto/pgi-migration-directories)

## Short description

This library is inspired by [DBIx::Migration::Directories](http://search.cpan.org/~crakrjack/DBIx-Migration-Directories-0.12/) perl module.

## Features

 - Installing database schema from scratch (into empty database).
 - Execution of SQL files to upgrade/downgrade database schemas.

## Requirements (environment)

 - PHP 7.0 or higher
 - **pgsql** extension
 - [PgI](https://github.com/ikto/pgi) library

## How to use

First, need to create a directory with migrations.

### Directory layout

```
DBSCHEMANAME/
 Pg/
  00000001/
  00000001-00000002/
  00000002-00000001/
  00000002-00000003/
  00000003-00000002/
```

At the top level there are directories named as db schema which we manage.
There is a **Pg** directory inside of each.
On the next level there is a set of directories named using the following pattern: *[from_version]*-*[to_version]*.
In other words each directory contains instructions how to update the db schema from one version to another (it may be upgrade or downgrade).
If directory name contains only one version number - it will be considered as *0*-*[version]*.
Zero version number means that db schema is not installed yet.
Each version-named directory should contain a set of SQL files.
The naming of SQL files is arbitrary, but please note, when performing migration SQL files will be sorted alphabetically.


As we have the migrations directory prepared, we can proceed with migration.

### Performing migration

```php
use IKTO\PgI;
use IKTO\PgiMigrationDirectories\Database\DefaultManagedDatabase;
use IKTO\PgiMigrationDirectories\Processor\ProcessorFactory;
use IKTO\PgiMigrationDirectories\Discovery\StandardDiscovery;
use IKTO\PgiMigrationDirectories\MigrationPathBuilder\MigrationPathBuilder;

/**
 * Step 1. Creating db connector (managed db object).
 */

// Connecting to the database.
$dbh = PgI::connect('host=127.0.0.1 port=5432 dbname=pgi_test', 'postgres', 'postgres');
// Creating managed db.
$migration_db = new DefaultManagedDatabase($dbh, 'DBSCHEMANAME', 'public');
// Setting processor factory.
$migration_db->setProcessorFactory(new ProcessorFactory());
// Specifying target db version. In real app it will come from config or something like this.
$migration_db->setDesiredVersion(42);

/**
 * Step 2. Building the migration path.
 */

// Retrieving current version number.
$startingVersion = $migration_db->getCurrentVersion();
// Instantiating migrations discovery.
$discovery = new StandardDiscovery(__DIR__ . '/sql/migrations', 'DBSCHEMANAME');
// Instantiating migration path builder.
$builder = new MigrationPathBuilder($discovery);
// Creating migration path.
$path = $builder->getMigrationPath($startingVersion, $migration_db->getDesiredVersion());

/**
 * Step 3. Applying migration (choose one of two options here).
 */

// Applying migration path to the database (each step in separate transaction).
foreach ($path as $migration) {
    $migration_db->openTransaction();
    $migration_db->applyMigration($migration);
    $migration_db->commitTransaction();
    printf('Migrated from %d to %d', $migration->getStartingVersion(), $migration->getTargetVersion());
}

// Applying migration path to the database (whole migration is single transaction).
$migration_db->openTransaction();
foreach ($path as $migration) {
    $migration_db->applyMigration($migration);
    printf('Migrated from %d to %d', $migration->getStartingVersion(), $migration->getTargetVersion());
}
$migration_db->commitTransaction();
```

To monitor the state of the db the library holds the data about migration inside of db.

### Current migration state tables.

These table should be created with the first migration which install the db schema.

```sql
CREATE TABLE migration_schema_version (
    name character varying(128) NOT NULL,
    version real NOT NULL,
    CONSTRAINT migration_schema_version_pkey PRIMARY KEY (name)
);
```

```sql
CREATE TABLE migration_schema_log (
    id serial NOT NULL,
    schema_name character varying(128) NOT NULL,
    event_time timestamp with time zone DEFAULT now() NOT NULL,
    old_version real DEFAULT 0 NOT NULL,
    new_version real NOT NULL,
    CONSTRAINT migration_schema_log_pkey PRIMARY KEY (id),
    CONSTRAINT migration_schema_log_schema_name_fkey FOREIGN KEY (schema_name)
        REFERENCES migration_schema_version (name) MATCH SIMPLE
        ON UPDATE CASCADE ON DELETE CASCADE DEFERRABLE INITIALLY IMMEDIATE
);
```

Usually these tables are stored under the **public** schema.
But you are able to store them in another, just don't forget to change the third constructor argument when you're creating managed db object.

To be continued...
