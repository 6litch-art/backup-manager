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
    public function handles($type);

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
