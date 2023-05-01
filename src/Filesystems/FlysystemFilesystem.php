<?php namespace Backup\Manager\Filesystems;

use League\Flysystem\FilesystemInterface;
use League\Flysystem\MountManager;

/**
 * Class FlysystemFilesystem
 * @package Backup\Manager\Filesystems
 */
class FlysystemFilesystem implements Filesystem
{
    /**
     * @var array|FilesystemInterface[]
     */
    private mixed $filesystems;

    /**
     * @var MountManager
     */
    private ?MountManager $manager;

    public function __construct(/* iterable */ $filesystems = [], MountManager $manager = null)
    {
        $this->filesystems = $filesystems;
        $this->manager = $manager;
    }

    /**
     * @param $type
     * @return bool
     */
    public function handles($type)
    {
        return strtolower($type ?? '') === 'flysystem';
    }

    public function get(array $config)
    {
        if (isset($config['prefix']) && null !== $this->manager) {
            return $this->manager->getFilesystem($config['prefix']);
        }

        return $this->filesystems[$config['name']];
    }
}
