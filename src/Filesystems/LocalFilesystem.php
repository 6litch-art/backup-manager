<?php

namespace Backup\Manager\Filesystems;

use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\Local\LocalFilesystemAdapter;

/**
 * Class LocalFilesystem.
 */
class LocalFilesystem implements Filesystem
{
    /**
     * Test fitness of visitor.
     *
     * @return bool
     */
    public function handles($type)
    {
        return 'local' == strtolower($type ?? '');
    }

    /**
     * @return Flysystem
     */
    public function get(array $config)
    {
        return new Flysystem(new LocalFilesystemAdapter($config['root']));
    }
}
