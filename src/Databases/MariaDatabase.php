<?php

namespace Backup\Manager\Databases;

class MariaDatabase implements Database
{
    private array $config = [];
    private string $serverVersion = '0.0.0';
    private string $clientVersion = '0.0.0';
    private bool $supportsSslMode = false;

    public function handles(string $driver, ?string $serverVersion = null): bool
    {
        $isMysql = in_array(strtolower($driver ?? ''), ['mysql', 'pdo_mysql'], true);
        if (!$isMysql) {
            return false;
        }

        exec('mariadb-dump --version 2>&1', $output, $code);
        if ($code !== 0 || empty($output[0])) {
            return false;
        }

        if (str_contains(strtolower($serverVersion), 'mariadb')) {

            $this->clientVersion = $this->extractVersion($output[0]);
            $this->serverVersion = $serverVersion ?? $this->serverVersion;
            $this->supportsSslMode = version_compare($this->clientVersion, '10.2.0', '>=');
            return $this->isVersionCompatible($this->serverVersion);
        }

        return false;
    }

    public function isVersionCompatible(?string $serverVersion = null): bool
    {
        if ($serverVersion === null) {
            return true;
        }

        // Example: reject if server is MySQL and client is MariaDB
        if (stripos($serverVersion, 'mysql') !== false) {
            return false;
        }

        // Example: reject if server is MariaDB 10.2+ and client is older
        if (preg_match('/([0-9]+)\./', $serverVersion, $matches)) {
            $serverMajor = (int)$matches[1];
            $clientMajor = (int)explode('.', $this->serverVersion)[0];
            if ($serverMajor >= 10 && $clientMajor < 10) {
                return false;
            }
        }

        return true;
    }

    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    public function getDumpCommandLine($outputPath): string
    {
        $extras = ['--routines'];

        if (!empty($this->config['singleTransaction'])) {
            $extras[] = '--single-transaction';
        }

        $extras = array_merge($extras, $this->buildSslOptions());

        if (!empty($this->config['extraParams'])) {
            $extras[] = $this->config['extraParams'];
        }

        $params = $this->buildConnectionParams();
        $dumpBin = $this->config['dumpBinary'] ?? 'mariadb-dump';

        return sprintf(
            '%s %s %s %s > %s',
            escapeshellcmd($dumpBin),
            implode(' ', $extras),
            $params,
            escapeshellarg($this->config['dbname']),
            escapeshellarg($outputPath)
        );
    }

    public function getRestoreCommandLine($inputPath): string
    {
        $extras = $this->buildSslOptions();
        $params = $this->buildConnectionParams();
        $clientBin = $this->config['clientBinary'] ?? 'mariadb';

        return sprintf(
            '%s %s %s %s -e "source %s"',
            escapeshellcmd($clientBin),
            $params,
            implode(' ', $extras),
            escapeshellarg($this->config['dbname']),
            escapeshellarg($inputPath)
        );
    }

    private function buildSslOptions(): array
    {
        $options = [];

        if (!empty($this->config['sslmode']) && $this->supportsSslMode) {
            $options[] = is_bool($this->config['sslmode']) ? '--ssl' : '--skip-ssl';
        }

        if (!empty($this->config['sslca'])) {
            $options[] = '--ssl-ca=' . escapeshellarg($this->config['sslca']);
        }

        if (!empty($this->config['sslcert'])) {
            $options[] = '--ssl-cert=' . escapeshellarg($this->config['sslcert']);
        }

        if (!empty($this->config['sslkey'])) {
            $options[] = '--ssl-key=' . escapeshellarg($this->config['sslkey']);
        }

        return $options;
    }

    private function buildConnectionParams(): string
    {
        $parts = [];

        $map = [
            'host' => 'host',
            'port' => 'port',
            'user' => 'user',
            'pass' => 'password',
        ];

        foreach ($map as $key => $cli) {
            if (!empty($this->config[$key])) {
                $parts[] = sprintf('--%s=%s', $cli, escapeshellarg($this->config[$key]));
            }
        }

        if (!empty($this->config['socket'])) {
            $parts[] = '--socket=' . escapeshellarg($this->config['socket']);
        }

        if (!empty($this->config['defaultAuth'])) {
            $parts[] = '--default-auth=' . escapeshellarg($this->config['defaultAuth']);
        }

        if (!empty($this->config['ignoreTables'])) {
            $parts[] = $this->buildIgnoreTableParams();
        }

        return implode(' ', array_filter($parts));
    }

    private function buildIgnoreTableParams(): string
    {
        if (empty($this->config['ignoreTables']) || !is_array($this->config['ignoreTables'])) {
            return '';
        }

        $db = $this->config['dbname'];
        $commands = [];

        foreach ($this->config['ignoreTables'] as $table) {
            $commands[] = '--ignore-table=' . escapeshellarg($db . '.' . $table);
        }

        return implode(' ', $commands);
    }

    private function extractVersion(string $versionString): string
    {
        if (preg_match('/([0-9\.]+)-MariaDB/', $versionString, $matches)) {
            return $matches[1];
        }

        return '0.0.0';
    }
}