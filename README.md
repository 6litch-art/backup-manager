Database Backup
===============

A framework-agnostic database backup package.

# Installation

## Laravel

1. add to composer.json

    "mccool/database-backup": "dev-master"

2. install dependency

    composer update

3. install configuration file

    php artisan config:publish mccool/database-backup

4. add service provider to config/app.php

    'McCool\DatabaseBackup\ServiceProviders\LaravelServiceProvider',

5. add key / secret to the config file in ```app/config/packages/mccool/database-backup/aws.php```

# Usage

## Laravel

**Get a List of Options**

    php artisan help db:backup

## Native PHP

```PHP
<?php

require '../vendor/autoload.php';

// dump the database to backup/test.sql
$dumper = new McCool\DatabaseBackup\Dumpers\MysqlDumper(new McCool\DatabaseBackup\Processors\ShellProcessor, 'localhost', 3306, 'username', 'password', 'test_db', 'backup/test.sql');

$backup = new McCool\DatabaseBackup\BackupProcedure($dumper);
$backup->backup();

// dump the database to backup/test.sql and gzip it
$dumper   = new McCool\DatabaseBackup\Dumpers\MysqlDumper(new McCool\DatabaseBackup\Processors\ShellProcessor, 'localhost', 3306, 'username', 'password', 'test_db', 'backup/test.sql');
$archiver = new McCool\DatabaseBackup\Archivers\GzipArchiver(new McCool\DatabaseBackup\Processors\ShellProcessor);

$backup = new McCool\DatabaseBackup\BackupProcedure($dumper, $archiver);
$backup->backup();

// dump the database to backup/test.sql, gzip it, upload it to S3, and clean up after ourselves
$dumper   = new McCool\DatabaseBackup\Dumpers\MysqlDumper(new McCool\DatabaseBackup\Processors\ShellProcessor, 'localhost', 3306, 'username', 'password', 'test_db', 'backup/test.sql');
$archiver = new McCool\DatabaseBackup\Archivers\GzipArchiver(new McCool\DatabaseBackup\Processors\ShellProcessor);
$storer   = new McCool\DatabaseBackup\Storers\S3Storer($awsKey, $awsSecret, 'us-east-1', $bucket, $s3Path);

$backup = new McCool\DatabaseBackup\BackupProcedure($dumper, $archiver, $storer);
$backup->backup();
$backup->cleanup();
```

# License

MIT