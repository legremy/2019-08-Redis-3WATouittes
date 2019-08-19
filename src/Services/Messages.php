<?php

namespace App\Services;

use App\Models\Message;

class Messages
{

    public static function addMessage($message, $userId)
    {
        $redis = RedisConnection::getInstance()->getClient();
        $postid = $redis->incr("next_post_id");
        $redis->hmset("post:$postid", "user_id", $userId, "time", time(), "body", str_replace("\n", " ", $message));
        $redis->lpush("posts:$userId", $postid);
        $redis->lpush("timeline", $postid);
    }

    public static function getTimeline()
    {
        $redis = RedisConnection::getInstance()->getClient();
        $timelineRange = $redis->lrange("timeline", 0, 50);
        foreach ($timelineRange as $id) {
            $timeline[] = new Message($id);
        }
        return $timeline ?? null;
    }

    public static function getUsersByCreationDate()
    {
        $redis = RedisConnection::getInstance()->getClient();
        $users = $redis->zrevrange("users_by_time", 0, 9);
        return $users;
    }
}
