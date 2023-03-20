<?php namespace Backup\Manager\Tasks\Storage;

use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use Backup\Manager\Tasks\Task;

/**
 * Class DeleteFile
 * @package Backup\Manager\Tasks\Storage
 */
class DeleteFile implements Task
{
    /** @var Filesystem */
    private $filesystem;
    /** @var string */
    private $filePath;

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
     * @return bool
     * @throws FileNotFoundException
     */
    public function execute()
    {
        return $this->filesystem->delete($this->filePath);
    }
}
