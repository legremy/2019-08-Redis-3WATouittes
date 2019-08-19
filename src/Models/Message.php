<?php

namespace App\Models;

use App\Services\RedisConnection;

class Message
{

    private $id;
    private $userId;
    private $time;
    private $body;

    public function __construct($id)
    {
        $redis = RedisConnection::getInstance()->getClient();
        $post = $redis->hgetall("post:$id");

        if (empty($post)) return false;
        $this->id = $id;
        $this->userId = $post["user_id"];
        $this->time = $post["time"];
        $this->body = $post["body"];
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function getUsername()
    {
        $redis = RedisConnection::getInstance()->getClient();
        return $redis->hget("user:" . $this->userId, "username");
    }

    public function getTime()
    {
        return $this->time;
    }
    public function getElapsedTime()
    {
        $d = time() - $this->time;
        if ($d < 60) return "$d secondes";
        if ($d < 3600) {
            $m = (int) ($d / 60);
            return "$m minute" . ($m > 1 ? "s" : "");
        }
        if ($d < 3600 * 24) {
            $h = (int) ($d / 3600);
            return "$h heure" . ($h > 1 ? "s" : "");
        }
        $d = (int) ($d / (3600 * 24));
        return "$d jour" . ($d > 1 ? "s" : "");
    }

    public function setTime($time)
    {
        $this->time = $time;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }
}
