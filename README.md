# PgI Migration Directories

[![Build Status](https://travis-ci.org/ikto/pgi-migration-directories.svg?branch=dev)](https://travis-ci.org/ikto/pgi-migration-directories)

In the past it was a standalone library for applying db migrations using [PgI](https://github.com/ikto/pgi).
Now it is a connection adapter for [Pg Migration Directories](https://github.com/ikto/pg-migration-directories).

## Requirements (environment)

- PHP 7.0 or higher
- [PgI](https://github.com/ikto/pgi) library
- [Pg Migration Directories](https://github.com/ikto/pg-migration-directories) library

## How to use

```php
use IKTO\PgI;
use IKTO\PgiMigrationDirectories\Adapter\PgiConnectionAdapter;

// Connecting to the database.
$dbh = PgI::connect('host=127.0.0.1 port=5432 dbname=pgi_test', 'postgres', 'postgres');
$connection_adapter = new PgiConnectionAdapter($dbh);
// ... and the pass connection adapter to managed db object
```
