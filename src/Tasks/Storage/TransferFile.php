<?php

namespace Backup\Manager\Tasks\Storage;

use Backup\Manager\Tasks\Task;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;

/**
 * Class TransferFile.
 */
class TransferFile implements Task
{
    /** @var Filesystem */
    private $sourceFilesystem;
    /** @var string */
    private $sourcePath;
    /** @var Filesystem */
    private $destinationFilesystem;
    /** @var string */
    private $destinationPath;

    /**
     * @param Filesystem $sourceFilesystem
     * @param $sourcePath
     * @param Filesystem $destinationFilesystem
     * @param $destinationPath
     */
    public function __construct(Filesystem $sourceFilesystem, $sourcePath, Filesystem $destinationFilesystem, $destinationPath)
    {
        $this->sourceFilesystem = $sourceFilesystem;
        $this->sourcePath = $sourcePath;
        $this->destinationFilesystem = $destinationFilesystem;
        $this->destinationPath = $destinationPath;
    }

    /**
     * @throws FilesystemException
     */
    public function execute()
    {
        $this->destinationFilesystem->writeStream(
            $this->destinationPath,
            $this->sourceFilesystem->readStream($this->sourcePath)
        );
    }
}
