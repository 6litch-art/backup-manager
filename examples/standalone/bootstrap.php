<?php

// path to composer autoloader
require '../../vendor/autoload.php';

use Backup\Manager\Compressors;
use Backup\Manager\Config\Config;
use Backup\Manager\Databases;
use Backup\Manager\Filesystems;
use Backup\Manager\Manager;

// build providers
$filesystems = new Filesystems\FilesystemProvider(Config::fromPhpFile('config/storage.php'));
$filesystems->add(new Filesystems\Awss3Filesystem());
$filesystems->add(new Filesystems\GcsFilesystem());
$filesystems->add(new Filesystems\DropboxFilesystem());
$filesystems->add(new Filesystems\FtpFilesystem());
$filesystems->add(new Filesystems\LocalFilesystem());
$filesystems->add(new Filesystems\RackspaceFilesystem());
$filesystems->add(new Filesystems\SftpFilesystem());
$filesystems->add(new Filesystems\WebdavFilesystem());

$databases = new Databases\DatabaseProvider(Config::fromPhpFile('config/database.php'));
$databases->add(new Databases\MysqlDatabase());
$databases->add(new Databases\PostgresqlDatabase());

$compressors = new Compressors\CompressorProvider();
$compressors->add(new Compressors\GzipCompressor());
$compressors->add(new Compressors\NullCompressor());

// build manager
return new Manager($filesystems, $databases, $compressors);
