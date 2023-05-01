<?php

namespace Backup\Manager\Filesystems;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\MountManager;

/**
 * Class FlysystemFilesystem.
 */
class FlysystemFilesystem implements Filesystem
{
    /**
     * @var array|FilesystemInterface[]
     */
    private mixed $filesystems;

    /**
     * @var MountManager|null
     */
    private ?MountManager $manager;

    /**
     * @param $filesystems
     * @param MountManager|null $manager
     */
    public function __construct(/* iterable */ $filesystems = [], MountManager $manager = null)
    {
        $this->filesystems = $filesystems;
        $this->manager = $manager;
    }

    /**
     * @return bool
     */
    public function handles($type)
    {
        return 'flysystem' === strtolower($type ?? '');
    }

    /**
     * @param array $config
     * @return Filesystem|FilesystemInterface|mixed
     */
    public function get(array $config)
    {
        if (isset($config['prefix']) && null !== $this->manager) {
            return $this->manager->getFilesystem($config['prefix']);
        }

        return $this->filesystems[$config['name']];
    }
}
