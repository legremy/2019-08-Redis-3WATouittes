<?php

namespace App\Services;

class Users
{
    public static function getUserIdByUsername($username)
    {
        $redis = RedisConnection::getInstance()->getClient();
        return $redis->hget("users", $username);
    }
}
