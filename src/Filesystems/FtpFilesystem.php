<?php

namespace Backup\Manager\Filesystems;

use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\Ftp\FtpAdapter;
use League\Flysystem\Ftp\FtpConnectionOptions;

/**
 * Class FtpFilesystem.
 */
class FtpFilesystem implements Filesystem
{
    /**
     * Test fitness of visitor.
     *
     * @return bool
     */
    public function handles($type)
    {
        return 'ftp' == strtolower($type ?? '');
    }

    /**
     * @return Flysystem
     */
    public function get(array $config)
    {
        return new Flysystem(new FtpAdapter(new FtpConnectionOptions(...($config['connection'] ?? []))));
    }
}
