<?php

namespace Backup\Manager\Filesystems;

use League\Flysystem\Filesystem as Flysystem;
use Srmklive\Dropbox\Adapter\DropboxAdapter;
use Srmklive\Dropbox\Client\DropboxClient;

/**
 *
 */
class DropboxV2Filesystem implements Filesystem
{
    /**
     * Test fitness of visitor.
     *
     * @return bool
     */
    public function handles($type)
    {
        return 'dropboxv2' == strtolower($type ?? '');
    }

    /**
     * @return Flysystem
     */
    public function get(array $config)
    {
        $client = new DropboxClient($config['token']);

        return new Flysystem(new DropboxAdapter($client, $config['root']));
    }
}
