<?php

namespace XMySQL\Model;

use PDO;

class Server
{
    protected $name;
    protected $host;
    protected $username;
    protected string $password;
    protected $exclude; // array of dbname exclude patterns

    public static function createFromConfig(string $name, array $config): self
    {
        $self = new self();
        $self->name = $name;
        $self->host = $config['host'];
        $self->username = $config['username'];
        $self->password = $config['password'];
        $self->exclude = $config['exclude'] ?? [];
        return $self;
    }
    
    public function getName(): string
    {
        return $this->name;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getDsn(): string
    {
        return 'mysql:host=' . $this->host;
    }

    private $pdo;

    public function getPdo(): PDO
    {
        if (!$this->pdo) {
            $this->pdo = new PDO(
                $this->getDsn(),
                $this->getUsername(),
                $this->getPassword(),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]
            );
        }
        return $this->pdo;
    }

    public function getDatabases(): array
    {
        $pdo = $this->getPdo();
        $stmt = $pdo->prepare("SHOW DATABASES");
        $stmt->execute([]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $databases = [];
        foreach ($rows as $row) {
            $name = $row['Database'];
            $databases[$name] = [
                'name' => $name,
            ];
        }

        // filter excluded patterns
        foreach ($this->exclude as $pattern) {

            foreach ($databases as $key => $item) {
                if (fnmatch($pattern, $key)) {
                    unset($databases[$key]);
                }
            }
        }

        return $databases;
    }
}
