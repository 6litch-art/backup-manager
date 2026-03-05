<?php

namespace Backup\Manager\Databases;

class MysqlDatabase implements Database
{
    private array $config = [];
    private string $clientVersion = '0.0.0';
    private string $serverVersion = '0.0.0';
    private bool $supportsSslMode = false;
    private bool $supportsColumnStats = false;

    public function handles(string $driver, ?string $serverVersion = null): bool
    {
        $isMysql = in_array(strtolower($driver ?? ''), ['mysql', 'pdo_mysql'], true);
        if (!$isMysql) {
            return false;
        }

        exec('mysqldump --version 2>&1', $output, $code);
        if ($code !== 0 || empty($output[0])) {
            return false;
        }

        if (!str_contains(strtolower($serverVersion), 'mariadb')) {

            $this->clientVersion = $this->extractVersion($output[0]);
            $this->serverVersion = $serverVersion ?? $this->serverVersion;
            $this->supportsSslMode = version_compare($this->clientVersion, '8', '>=');
            return $this->isVersionCompatible($this->serverVersion);
        }

        return false;
    }

    public function isVersionCompatible(?string $serverVersion = null): bool
    {
        if ($serverVersion === null) {
            return true;
        }

        // Reject if server is MariaDB and client is MySQL
        if (stripos($serverVersion, 'mariadb') !== false) {
            return false;
        }

        // Reject if server is MySQL 8+ and client is older than 8
        if (preg_match('/([0-9]+)\./', $serverVersion, $matches)) {
            $serverMajor = (int)$matches[1];
            $clientMajor = (int)explode('.', $this->clientVersion)[0];
            if ($serverMajor >= 8 && $clientMajor < 8) {
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

        if ($this->supportsColumnStats) {
            $extras[] = '--column-statistics=0';
        }

        $extras = array_merge($extras, $this->buildSslOptions());

        if (!empty($this->config['ignoreTables'])) {
            $extras[] = $this->getIgnoreTableParameter();
        }

        if (!empty($this->config['extraParams'])) {
            $extras[] = $this->config['extraParams'];
        }

        $params = $this->buildConnectionParams();

        return sprintf(
            'mysqldump %s %s %s > %s',
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

        return sprintf(
            'mysql %s %s %s -e "source %s"',
            implode(' ', $extras),
            $params,
            escapeshellarg($this->config['dbname']),
            escapeshellarg($inputPath)
        );
    }

    private function buildSslOptions(): array
    {
        $options = [];

        if (!empty($this->config['sslmode']) && $this->supportsSslMode) {
            $options[] = '--ssl-mode=' . escapeshellarg($this->config['sslmode']);
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
            'password' => 'password',
        ];

        foreach ($map as $key => $cli) {
            if (!empty($this->config[$key])) {
                $parts[] = sprintf('--%s=%s', $cli, escapeshellarg($this->config[$key]));
            }
        }

        if (!empty($this->config['socket'])) {
            $parts[] = '--socket=' . escapeshellarg($this->config['socket']);
        }

        return implode(' ', array_filter($parts));
    }

    public function getIgnoreTableParameter(): string
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

    private function extractVersion(string $string): string
    {
        if (preg_match('/Distrib ([0-9\.]+)/', $string, $matches)) {
            return $matches[1];
        }

        if (preg_match('/Ver ([0-9\.]+)/', $string, $matches)) {
            return $matches[1];
        }

        return '0.0.0';
    }
}