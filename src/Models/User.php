<?php

namespace App\Models;

use App\Services\RedisConnection;

/**
 * Représente un utilisateur
 * 
 * (Ne pas confondre $id l'id utilisateur, présent dans la base Redis, et qui sert à cibler un utilisateur précis dans ladite base
 * et $auth, une chaîne unique persistée dans un cookie nommé... "auth" ;), également présente dans la base Redis mais servant à prouver qu'un utilisateur est authentifié)
 */
class User
{
    private $id;
    private $username;
    private $auth;

    public function __construct($id = null)
    {
        // Connexion à Redis
        $redis = RedisConnection::getInstance()->getClient();
        if ($redis->hgetall("user:$id")) {
            $this->setId($id);
            $this->setUsername($redis->hget("user:$id", "username"));
            $this->setAuth($redis->hget("user:$id", "auth"));
        }
    }

    public function getPosts()
    {
        $redis = RedisConnection::getInstance()->getClient();
        $posts = $redis->lrange("posts:$this->id", 0, -1);
        foreach ($posts as $post) $userPosts[] = new Message($post);

        return $userPosts ?? null;
    }

    public function isFollowedBy(User $user)
    {
        $redis = RedisConnection::getInstance()->getClient();
        if ($redis->zscore("followers:$this->id", $user->getId()))
            return true;
        return false;
    }
    public function follow(User $user)
    {
        $redis = RedisConnection::getInstance()->getClient();

        $redis->zadd("followers:" . $user->getId(), time(), $this->id);
        $redis->zadd("following:" . $this->id, time(), $user->getId());
    }

    public function unfollow(User $user)
    {
        $redis = RedisConnection::getInstance()->getClient();
        $redis->zrem("followers:" . $user->getId(), $this->id);
        $redis->zrem("following:" . $this->id, $user->getId());
    }

    public function isFollowingNumber()
    {
        $redis = RedisConnection::getInstance()->getClient();
        return $redis->zcard("following:" . $this->id);
    }

    public function isFollowedByNumber()
    {
        $redis = RedisConnection::getInstance()->getClient();
        return $redis->zcard("followers:" . $this->id);
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getAuth()
    {
        return $this->auth;
    }

    public function setAuth($auth)
    {
        $this->auth = $auth;
    }
}
