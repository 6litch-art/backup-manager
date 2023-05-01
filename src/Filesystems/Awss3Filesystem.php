<?php namespace Backup\Manager\Filesystems;

use League\Flysystem\AwsS3v3\AwsS3Adapter;
use Aws\S3\S3Client;
use League\Flysystem\Filesystem as Flysystem;

/**
 * Class Awss3Filesystem
 * @package Backup\Manager\Filesystems
 */
class Awss3Filesystem implements Filesystem
{
    /**
     * @param $type
     * @return bool
     */
    public function handles($type)
    {
        return strtolower($type ?? '') == 'awss3';
    }

    /**
     * @param array $config
     * @return Flysystem
     */
    public function get(array $config)
    {
        $client = S3Client::factory([
            'credentials' => [
                'key' => $config['key'],
                'secret' => $config['secret'],
            ],
            'region' => $config['region'],
            'version' => $config['version'] ?? 'latest',
            'endpoint' => $config['endpoint'] ?? null,
            'use_path_style_endpoint' => $config['use_path_style_endpoint'] ?? false,
        ]);

        return new Flysystem(new AwsS3Adapter($client, $config['bucket'], $config['root']));
    }
}
