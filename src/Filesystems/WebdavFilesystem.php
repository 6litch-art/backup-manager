<?php

namespace Backup\Manager\Filesystems;

use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\WebDAV\WebDAVAdapter;
use Sabre\DAV\Client;

/**
 * Class WebdavFilesystem.
 */
class WebdavFilesystem implements Filesystem
{
    /**
     * Test fitness of visitor.
     *
     * @return bool
     */
    public function handles($type)
    {
        return 'webdav' === strtolower($type ?? '');
    }

    /**
     * @return Flysystem
     */
    public function get(array $config)
    {
        return new Flysystem(new WebDAVAdapter(new Client($config), $config['prefix']));
    }
}
