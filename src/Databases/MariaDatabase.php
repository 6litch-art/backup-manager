<?php namespace Backup\Manager\Databases;

/**
 * Class MariaDatabase
 * - Uses MariaDB client binaries by default: `mariadb-dump` and `mariadb`
 * - Config keys supported:
 *   host, port, user, pass, database, ssl (bool), singleTransaction (bool),
 *   ignoreTables (array), extraParams (string), socket (string),
 *   defaultAuth (string, e.g. "mysql_native_password"),
 *   dumpBinary (string), clientBinary (string)
 */

class MariaDatabase implements Database
{
    /** @var array */
    private $config;

    public function handles($type)
    {
        $isMysql = 'mysql' == strtolower($type ?? '') || 'pdo_mysql' == strtolower($type ?? '');
        if (!$isMysql) return false;

        list($_, $ret) = [[], false];
        exec("mysqldump --version", $_, $ret);
        $mysqldump = $_[0] ?? '';
        
        if($isMysql && \str_contains($mysqldump, "MariaDB"))
            return true;

        return false;
    }

    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    public function getDumpCommandLine($outputPath)
    {
        $extras = [];

        // Common safe flags for MariaDB
        if (!empty($this->config['singleTransaction'])) {
            $extras[] = '--single-transaction';
        }
        // MariaDB supports --routines as well
        $extras[] = '--routines';

        if (!empty($this->config['ssl'])) {
            $extras[] = '--ssl';
        }

        if (!empty($this->config['extraParams'])) {
            $extras[] = $this->config['extraParams'];
        }

        // Build connection params
        $params = $this->buildConnectionParams();

        // Binary: prefer mariadb-dump; allow override
        $dumpBin = !empty($this->config['dumpBinary']) ? $this->config['dumpBinary'] : 'mariadb-dump';

        $command = sprintf(
            '%s %s %s %s > %s',
            escapeshellcmd($dumpBin),
            implode(' ', $extras),
            $params,
            escapeshellarg($this->config['database']),
            escapeshellarg($outputPath)
        );

        return $command;
    }

    public function getRestoreCommandLine($inputPath)
    {
        $extras = [];

        if (!empty($this->config['ssl'])) {
            $extras[] = '--ssl';
        }

        $params = $this->buildConnectionParams();

        // Binary: prefer mariadb; allow override
        $clientBin = !empty($this->config['clientBinary']) ? $this->config['clientBinary'] : 'mariadb';

        // Use -e "source file.sql" for portability
        $command = sprintf(
            '%s %s %s %s -e "source %s"',
            escapeshellcmd($clientBin),
            $params,
            implode(' ', $extras),
            escapeshellarg($this->config['database']),
            escapeshellarg($inputPath)
        );

        return $command;
    }

    /**
     * Build connection params shared by dump and restore.
     */
    private function buildConnectionParams()
    {
        $parts = [];

        // Host/Port/User/Password
        $map = ['host' => 'host', 'port' => 'port', 'user' => 'user', 'pass' => 'password'];
        foreach ($map as $key => $cli) {
            if (!empty($this->config[$key])) {
                $parts[] = sprintf('--%s=%s', $cli, escapeshellarg($this->config[$key]));
            }
        }

        // Socket (takes precedence over host/port if provided by the client)
        if (!empty($this->config['socket'])) {
            $parts[] = sprintf('--socket=%s', escapeshellarg($this->config['socket']));
        }

        // Optional auth plugin (useful if server user uses mysql_native_password)
        if (!empty($this->config['defaultAuth'])) {
            $parts[] = sprintf('--default-auth=%s', escapeshellarg($this->config['defaultAuth']));
        }

        // Ignore tables for dump
        if (!empty($this->config['ignoreTables'])) {
            $parts[] = $this->getIgnoreTableParameter();
        }

        return implode(' ', array_filter($parts));
    }

    /**
     * @return string
     */
    public function getIgnoreTableParameter()
    {
        if (empty($this->config['ignoreTables']) || !is_array($this->config['ignoreTables'])) {
            return '';
        }

        $db = $this->config['database'];
        $commands = [];

        foreach ($this->config['ignoreTables'] as $table) {
            // Format: --ignore-table=db.table
            $fq = $db . '.' . $table;
            $commands[] = sprintf('--ignore-table=%s', escapeshellarg($fq));
        }

        return implode(' ', $commands);
    }
}
