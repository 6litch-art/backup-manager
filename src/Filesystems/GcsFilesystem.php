<?php namespace Backup\Manager\Filesystems;

use Google\Cloud\Storage\StorageClient;
use League\Flysystem\Filesystem as Flysystem;
use Superbalist\Flysystem\GoogleStorage\GoogleStorageAdapter;

/**
 * Class GcsFilesystem
 * @package Backup\Manager\Filesystems
 */
class GcsFilesystem implements Filesystem
{
    /**
     * @param $type
     * @return bool
     */
    public function handles($type)
    {
        return strtolower($type ?? '') == 'gcs';
    }

    /**
     * @param array $config
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
