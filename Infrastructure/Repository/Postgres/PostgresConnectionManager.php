<?php

namespace Infrastructure\Repository\Postgres;

class PostgresConnectionManager
{
    protected ?\PDO $connection;

    public function __construct()
    {
        //$this->connection = new \PDO('jdbc:postgresql://localhost:5432/net', 'postgres', 'nopassword');
        $this->connection = new \PDO('pgsql:host=172.10.1.30;port=5432;dbname=net', 'postgres', 'nopassword');
    }

    public function __destruct()
    {
        $this->connection = null;
    }
}
