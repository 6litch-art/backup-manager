<?php namespace Backup\Manager\Databases;

use Backup\Manager\Config\Config;
use Backup\Manager\Config\ConfigFieldNotFound;
use Backup\Manager\Config\ConfigNotFoundForConnection;

/**
 * Class DatabaseProvider
 * @package Backup\Manager\Databases
 */
class DatabaseProvider
{
    /** @var Config */
    private Config $config;
    /** @var array */
    private array $databases = [];

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param Database $database
     */
    public function add(Database $database)
    {
        $this->databases[] = $database;
    }

    /**
     * @param $name
     * @return Database
     * @throws DatabaseTypeNotSupported
     * @throws ConfigNotFoundForConnection
     * @throws ConfigFieldNotFound
     */
    public function get($name)
    {
        $type = $this->config->get($name, 'type');
        foreach ($this->databases as $database) {
            if ($database->handles($type)) {
                $database->setConfig($this->config->get($name));
                return $database;
            }
        }
        throw new DatabaseTypeNotSupported("The requested database type `" . $type . "` is not currently supported.");
    }

    /**
     * @return array
     */
    public function getAvailableProviders()
    {
        return array_keys($this->config->getItems());
    }
}
