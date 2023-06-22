<?php

namespace Backup\Manager\Filesystems;

use Backup\Manager\Config\Config;
use Backup\Manager\Config\ConfigFieldNotFound;
use Backup\Manager\Config\ConfigNotFoundForConnection;

/**
 * Class FilesystemProvider.
 */
class FilesystemProvider
{
    private Config $config;

    private array $filesystems = [];

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function add(Filesystem $filesystem)
    {
        $this->filesystems[] = $filesystem;
    }

    /**
     * @param null $key
     *
     * @return mixed
     *
     * @throws ConfigNotFoundForConnection
     * @throws ConfigFieldNotFound
     */
    public function getConfig($name, $key = null)
    {
        return $this->config->get($name, $key);
    }

    /**
     * @return array
     */
    public function getAvailableProviders()
    {
        return array_keys($this->config->getItems());
    }

    /**
     * @return \League\Flysystem\Filesystem
     *
     * @throws FilesystemTypeNotSupported
     * @throws ConfigNotFoundForConnection
     * @throws ConfigFieldNotFound
     */
    public function get($name)
    {
        $type = $this->getConfig($name, 'type');

        foreach ($this->filesystems as $filesystem) {
            if ($filesystem->handles($type)) {
                return $filesystem->get($this->config->get($name));
            }
        }

        throw new FilesystemTypeNotSupported('The requested filesystem type `' . $type . '` is not currently supported.');
    }


    /**
     * @return \League\Flysystem\Filesystem
     *
     * @throws FilesystemTypeNotSupported
     * @throws ConfigNotFoundForConnection
     * @throws ConfigFieldNotFound
     */
    public function lastModified($name)
    {
        $type = $this->getConfig($name, 'type');

        foreach ($this->filesystems as $filesystem) {
            if ($filesystem->handles($type)) {
                return $filesystem->lastModified($this->config->get($name));
            }
        }

        throw new FilesystemTypeNotSupported('The requested filesystem type `' . $type . '` is not currently supported.');
    }

    /**
     * @return \League\Flysystem\Filesystem
     *
     * @throws FilesystemTypeNotSupported
     * @throws ConfigNotFoundForConnection
     * @throws ConfigFieldNotFound
     */
    public function has($name)
    {
        $type = $this->getConfig($name, 'type');

        foreach ($this->filesystems as $filesystem) {
            if ($filesystem->handles($type)) {
                return $filesystem->has($this->config->get($name));
            }
        }

        throw new FilesystemTypeNotSupported('The requested filesystem type `' . $type . '` is not currently supported.');
    }
}
