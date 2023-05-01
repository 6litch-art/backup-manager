<?php

namespace Backup\Manager\Filesystems;

use BackblazeB2\Client;
use League\Flysystem\Filesystem as Flysystem;
use Mhetreramesh\Flysystem\BackblazeAdapter;

/**
 * Class BackblazeFilesystem.
 */
class BackblazeFilesystem implements Filesystem
{
    /**
     * Test fitness of visitor.
     *
     * @return bool
     */
    public function handles($type)
    {
        return 'b2' == strtolower($type ?? '');
    }

    /**
     * @return Flysystem
     *
     * @throws \Exception
     */
    public function get(array $config)
    {
        if (!isset($config['options'])) {
            $config['options'] = [];
        }

        $client = new Client($config['accountId'], $config['key'], $config['options']);

        return new Flysystem(new BackblazeAdapter($client, $config['bucket']));
    }
}
