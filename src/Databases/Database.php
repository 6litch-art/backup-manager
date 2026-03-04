<?php

namespace Backup\Manager\Databases;

/**
 * Class Database.
 */
interface Database
{
    /**
     * @return bool
     */
    public function handles(string $driver, ?string $server_version = null): bool;

    /**
     * @return null
     */
    public function setConfig(array $config);

    /**
     * @return string
     */
    public function getDumpCommandLine($inputPath);

    /**
     * @return string
     */
    public function getRestoreCommandLine($outputPath);
}
