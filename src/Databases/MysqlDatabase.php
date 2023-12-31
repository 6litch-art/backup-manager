<?php

namespace Backup\Manager\Databases;

/**
 * Class MysqlDatabase.
 */
class MysqlDatabase implements Database
{
    private array $config;

    /**
     * @return bool
     */
    public function handles($type)
    {
        return 'mysql' == strtolower($type ?? '') || 'pdo_mysql' == strtolower($type ?? '');
    }

    /**
     * @param array $config
     * @return void|null
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getDumpCommandLine($inputPath)
    {
        // Check if column statistics option is available
        list($_, $ret) = [[], false];
        exec("mysqldump --column-statistics=0 --version 2> /dev/null", $_, $ret);
        $this->config["ignoreColumnStatistics"] ??= true;
        $this->config["ignoreColumnStatistics"] = ($ret == 0) && $this->config["ignoreColumnStatistics"];

        // Get default socket file
        $this->config["socket"] ??= ini_get("pdo_mysql.default_socket") ?? ini_get("mysqli.default_socket") ?? "";

        // Compute extra parameters
        $extras = [];
        if (array_key_exists('singleTransaction', $this->config) && true === $this->config['singleTransaction']) {
            $extras[] = '--single-transaction';
        }
        if (array_key_exists('ignoreTables', $this->config) && true === $this->config["ignoreTables"]) {
            $extras[] = $this->getIgnoreTableParameter();
        }
        if (array_key_exists('ignoreColumnStatistics', $this->config) && true === $this->config["ignoreColumnStatistics"]) {
            $extras[] = '--column-statistics=0';
        }
        if (array_key_exists('ssl', $this->config) && true === $this->config['ssl']) {
            $extras[] = '--ssl';
        }
        if (array_key_exists('socket', $this->config) && !empty($this->config["socket"])) {
            $extras[] = '--socket=' . $this->config["socket"];
        }
        if (array_key_exists('extraParams', $this->config) && $this->config['extraParams']) {
            $extras[] = $this->config['extraParams'];
        }

        // Prepare a "params" string from our config
        $params = '';
        $keys = ['host' => 'host', 'port' => 'port', 'user' => 'user', 'pass' => 'password'];
        foreach ($keys as $key => $mysqlParam) {
            if (!empty($this->config[$key])) {
                $params .= sprintf(' --%s=%s', $mysqlParam, escapeshellarg($this->config[$key]));
            }
        }

        $command = 'mysqldump --routines ' . implode(' ', $extras) . ' %s %s > %s';
        return sprintf($command, $params, escapeshellarg($this->config['database']), escapeshellarg($inputPath));
    }

    /**
     * @return string
     */
    public function getRestoreCommandLine($outputPath)
    {
        $extras = [];
        if (array_key_exists('ssl', $this->config) && true === $this->config['ssl']) {
            $extras[] = '--ssl';
        }

        // Prepare a "params" string from our config
        $params = '';
        $keys = ['host' => 'host', 'port' => 'port', 'user' => 'user', 'pass' => 'password'];
        foreach ($keys as $key => $mysqlParam) {
            if (!empty($this->config[$key])) {
                $params .= sprintf(' --%s=%s', $mysqlParam, escapeshellarg($this->config[$key]));
            }
        }

        return sprintf(
            'mysql%s ' . implode(' ', $extras) . ' %s -e "source %s"',
            $params,
            escapeshellarg($this->config['database']),
            $outputPath
        );
    }

    /**
     * @return string
     */
    public function getIgnoreTableParameter()
    {
        if (!is_array($this->config['ignoreTables']) || 0 === count($this->config['ignoreTables'])) {
            return '';
        }

        $db = $this->config['database'];
        $ignoreTables = array_map(function ($table) use ($db) {
            return $db . '.' . $table;
        }, $this->config['ignoreTables']);

        $commands = [];
        foreach ($ignoreTables as $ignoreTable) {
            $commands[] = sprintf(
                '--ignore-table=%s',
                escapeshellarg($ignoreTable)
            );
        }

        return implode(' ', $commands);
    }
}
