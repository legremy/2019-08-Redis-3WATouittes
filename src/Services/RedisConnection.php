<?php

namespace App\Services;

use Predis\Client;

class RedisConnection
{

    private static $instance = null;
    private $connection;

    private function __construct()
    {

        return $this->connection = new Client;
    }

    public static function getInstance()
    {
        if (self::$instance == null) self::$instance = new RedisConnection();
        return self::$instance;
    }

    public function getClient()
    {
        return $this->connection;
    }
}
