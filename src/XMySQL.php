<?php

namespace XMySQL;

use XMySQL\Model\Server;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;
use RuntimeException;

class XMySQL
{
    protected $configFilename;
    protected $backupPath;
    protected $servers = [];

    private function __construct()
    {
    }

    public function getConfigFilename(): string
    {
        return $this->configFilename;
    }

    public static function fromEnv()
    {
        $self = new self();
        $self->configFilename = getenv('XMYSQL_CONFIG');

        if (!$self->configFilename || !file_exists($self->configFilename)) {
            throw new RuntimeException("XMYSQL_CONFIG configured incorrectly (check your .env)");
        }

        $yaml = file_get_contents($self->configFilename);
        $config = Yaml::parse($yaml);
        $self->backupPath = $config['backupPath'] ?? null;

        if (!is_dir($self->backupPath)) {
            throw new RuntimeException("backupPath is not a directory");
        }

        foreach ($config['servers'] ?? [] as $serverName => $serverConfig) {
            $server = Server::createFromConfig(
                $serverName,
                $serverConfig
            );
            $self->servers[$server->getName()] = $server;
        }
        return $self;
    }


    public function getServers(): array
    {
        return $this->servers;
    }

    public function getBackupPath(): string
    {
        return $this->backupPath;
    }
}
