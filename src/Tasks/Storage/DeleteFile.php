<?php

namespace Backup\Manager\Tasks\Storage;

use Backup\Manager\Tasks\Task;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;

/**
 * Class DeleteFile.
 */
class DeleteFile implements Task
{
    private Filesystem $filesystem;

    private string $filePath;

    /**
     * @param Filesystem $filesystem
     * @param $filePath
     */
    public function __construct(Filesystem $filesystem, $filePath)
    {
        $this->filesystem = $filesystem;
        $this->filePath = $filePath;
    }

    /**
     * @return void
     * @throws FilesystemException
     */
    public function execute()
    {
        $this->filesystem->delete($this->filePath);
    }
}
