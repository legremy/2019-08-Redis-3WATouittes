<?php

namespace App\Models;

use App\Services\RedisConnection;

/**
 * Entié représentant un Touitte, un message
 */
class Message
{

    private $id;
    private $userId;
    private $time;
    private $body;

    public function __construct($id)
    {
        // Connexion à Redis
        $redis = RedisConnection::getInstance()->getClient();
        // On tente de récupérer toutes les données d'un hypthétique post:$id
        $post = $redis->hgetall("post:$id");

        // Si ce post n'existe pas
        if (empty($post)) return false;

        // S'il existe, on crée un message qui le représente
        $this->id = $id;
        $this->userId = $post["user_id"];
        $this->time = $post["time"];
        $this->body = $post["body"];
    }

    /**
     * Renvoie le pseudo de l'auteur d'un message (j'aurais du l'appeler getAuthor...)
     */
    public function getUsername()
    {
        $redis = RedisConnection::getInstance()->getClient();
        return $redis->hget("user:" . $this->userId, "username");
    }

    /**
     * Renvoie le temps écoulé depuis la mise en ligne du message
     */
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

    // GETTERS ET SETTERS basiques ========================================================================================

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

    public function getTime()
    {
        return $this->time;
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
