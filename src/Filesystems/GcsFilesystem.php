<?php

namespace Backup\Manager\Filesystems;

use Google\Cloud\Storage\StorageClient;
use League\Flysystem\Filesystem as Flysystem;
use Superbalist\Flysystem\GoogleStorage\GoogleStorageAdapter;

/**
 * Class GcsFilesystem.
 */
class GcsFilesystem implements Filesystem
{
    /**
     * @return bool
     */
    public function handles($type)
    {
        return 'gcs' == strtolower($type ?? '');
    }

    /**
     * @return Flysystem
     */
    public function get(array $config)
    {
        $storageClient = new StorageClient([
            'projectId' => $config['project'],
            'keyFilePath' => $config['keyFilePath'] ?? null,
        ]);
        $bucket = $storageClient->bucket($config['bucket']);

        return new Flysystem(new GoogleStorageAdapter($storageClient, $bucket, $config['prefix']));
    }
}
