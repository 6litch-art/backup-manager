<?php

namespace Backup\Manager\Filesystems;

use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\PhpseclibV3\SftpAdapter;
use League\Flysystem\PhpseclibV3\SftpConnectionProvider;

/**
 * Class SftpFilesystem.
 */
class SftpFilesystem implements Filesystem
{
    /**
     * Test fitness of visitor.
     *
     * @return bool
     */
    public function handles($type)
    {
        return 'sftp' == strtolower($type ?? '');
    }

    /**
     * @return Flysystem
     */
    public function get(array $config)
    {
        $keys = array_flip(['host', 'username', 'password', 'privateKey', 'passphrase', 'port', 'useAgent', 'timeout', 'maxTries', 'hostFingerprint', 'connectivityChecker', 'preferredAlgorithms']);
        $connection = array_intersect_key($config['connection'] ?? [], $keys);
        $connectionProvider = SftpConnectionProvider::fromArray($connection);

        return new Flysystem(new SftpAdapter($connectionProvider, $config['root'] ?? ''));
    }
}
