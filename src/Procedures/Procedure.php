<?php

namespace Backup\Manager\Procedures;

use Backup\Manager\Compressors\CompressorProvider;
use Backup\Manager\Config\ConfigFieldNotFound;
use Backup\Manager\Config\ConfigNotFoundForConnection;
use Backup\Manager\Databases\DatabaseProvider;
use Backup\Manager\Filesystems\FilesystemProvider;
use Backup\Manager\ShellProcessing\ShellProcessor;

/**
 * Class Procedure.
 */
abstract class Procedure
{
    protected FilesystemProvider $filesystems;

    protected DatabaseProvider $databases;

    protected CompressorProvider $compressors;

    protected ShellProcessor $shellProcessor;

    /**
     * @internal param Sequence $sequence
     */
    public function __construct(FilesystemProvider $filesystemProvider, DatabaseProvider $databaseProvider, CompressorProvider $compressorProvider, ShellProcessor $shellProcessor)
    {
        $this->filesystems = $filesystemProvider;
        $this->databases = $databaseProvider;
        $this->compressors = $compressorProvider;
        $this->shellProcessor = $shellProcessor;
    }

    /**
     * @param null $filename
     *
     * @return string
     *
     * @throws ConfigNotFoundForConnection
     * @throws ConfigFieldNotFound
     */
    protected function getWorkingFile($name, $filename = null)
    {
        if (is_null($filename)) {
            $filename = uniqid();
        }

        return sprintf('%s/%s', $this->getRootPath($name), $filename);
    }

    /**
     * @return string
     *
     * @throws ConfigNotFoundForConnection
     * @throws ConfigFieldNotFound
     */
    protected function getRootPath($name)
    {
        $path = $this->filesystems->getConfig($name, 'root');

        return preg_replace('/\/$/', '', $path);
    }
}
