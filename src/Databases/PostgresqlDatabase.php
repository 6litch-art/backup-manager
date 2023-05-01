<?php

namespace Backup\Manager\Databases;

/**
 * Class PostgresqlDatabase.
 */
class PostgresqlDatabase implements Database
{
    private array $config;

    /**
     * @return bool
     */
    public function handles($type)
    {
        return in_array(strtolower($type ?? ''), ['postgresql', 'postgres', 'pgsql']);
    }

    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getDumpCommandLine($inputPath)
    {
        return sprintf(
            'PGPASSWORD=%s pg_dump --clean --host=%s --port=%s --username=%s %s -f %s',
            escapeshellarg($this->config['pass']),
            escapeshellarg($this->config['host']),
            escapeshellarg($this->config['port']),
            escapeshellarg($this->config['user']),
            escapeshellarg($this->config['database']),
            escapeshellarg($inputPath)
        );
    }

    /**
     * @return string
     */
    public function getRestoreCommandLine($outputPath)
    {
        return sprintf(
            'PGPASSWORD=%s psql --host=%s --port=%s --user=%s %s -f %s',
            escapeshellarg($this->config['pass']),
            escapeshellarg($this->config['host']),
            escapeshellarg($this->config['port']),
            escapeshellarg($this->config['user']),
            escapeshellarg($this->config['database']),
            escapeshellarg($outputPath)
        );
    }
}
