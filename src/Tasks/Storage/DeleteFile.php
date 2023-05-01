<?php

namespace Backup\Manager\Tasks\Storage;

use Backup\Manager\Tasks\Task;
use League\Flysystem\Filesystem;

/**
 * Class DeleteFile.
 */
class DeleteFile implements Task
{
    private Filesystem $filesystem;

    private string $filePath;

    public function __construct(Filesystem $filesystem, $filePath)
    {
        $this->filesystem = $filesystem;
        $this->filePath = $filePath;
    }

    /**
     * @return bool
     */
    public function execute()
    {
        $this->filesystem->delete($this->filePath);
    }
}
