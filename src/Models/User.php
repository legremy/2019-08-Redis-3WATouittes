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
        // Si l'id passé au constructeur est celui d'un utilisateur, on construit un objet le représentant
        if ($redis->hgetall("user:$id")) {
            $this->setId($id);
            $this->setUsername($redis->hget("user:$id", "username"));
            $this->setAuth($redis->hget("user:$id", "auth"));
        }
    }

    /**
     * Récupère la liste des messages postés par l'utilisateur
     *
     */
    public function getPosts()
    {
        // Connexion à Redis
        $redis = RedisConnection::getInstance()->getClient();
        // On récupère les ids de tous les posts de l'utilisateur courant. 
        // L'offset stop à -1 signifie qu'on souhaite récupérer l'ensemble des valeurs
        $posts = $redis->lrange("posts:$this->id", 0, -1);
        foreach ($posts as $post) $userPosts[] = new Message($post);

        return $userPosts ?? null;
    }

    /**
     * vérifie si l'utilisateur courant est suivi par un utilisateur $user
     *
     * @param User $user
     * @return boolean
     */
    public function isFollowedBy(User $user): bool
    {
        // Connexion à Redis
        $redis = RedisConnection::getInstance()->getClient();
        // Si l'id de $user se trouve dans la liste d'ids des followers de l'utilisateur courant ($this)... 
        if ($redis->zscore("followers:$this->id", $user->getId()))
            return true;
        return false;
    }

    /**
     * Abonne l'utilisateur courant à $user
     *
     * @param User $user
     */
    public function follow(User $user)
    {
        // Connexion à Redis
        $redis = RedisConnection::getInstance()->getClient();
        // On ajoute l'id de l'utilisateur courant à la liste des id des followers de $user
        $redis->zadd("followers:" . $user->getId(), time(), $this->id);
        // On ajoute l'id de $user à la liste des id des abonnements de l'utilisateur courant
        $redis->zadd("following:" . $this->id, time(), $user->getId());
    }

    /**
     * désabonne l'utilisateur courant de $user
     *
     * @param User $user
     */
    public function unfollow(User $user)
    {
        // Connexion à Redis
        $redis = RedisConnection::getInstance()->getClient();
        // On supprime l'id de l'utilisateur courant de la liste des id des followers de $user
        $redis->zrem("followers:" . $user->getId(), $this->id);
        // On supprime l'id de $user de la liste des id des abonnements de l'utilisateur courant
        $redis->zrem("following:" . $this->id, $user->getId());
    }

    /**
     * Récupère le nombre d'abonnements de l'utilisateur courant
     *
     */
    public function isFollowingNumber()
    {
        $redis = RedisConnection::getInstance()->getClient();
        return $redis->zcard("following:" . $this->id);
    }

    /**
     * Récupère le nombre d'abonnés de l'utilisateur courant
     *
     */
    public function isFollowedByNumber()
    {
        $redis = RedisConnection::getInstance()->getClient();
        return $redis->zcard("followers:" . $this->id);
    }

    // GETTERS ET SETTERS ========================================================================================

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
