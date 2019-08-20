<?php

namespace App\Services;

use App\Models\Message;

/**
 * Gère les opérations sur les messages
 */
class Messages
{

    /**
     * Ajoute un message à la base de données
     *
     * @param string $message
     * @param int $userId
     */
    public static function addMessage(string $message, int $userId)
    {
        // Connexion à Redis
        $redis = RedisConnection::getInstance()->getClient();
        // On incrémente la valeur de la clé next_post_id qui garantit que chaque id sera unique
        // Équivaut un peu à un AUTO_INCREMENT en SQL
        $postid = $redis->incr("next_post_id");
        // On ajoute le post dans un hash défini par une clé de type "post:12"
        $redis->hmset("post:$postid", "user_id", $userId, "time", time(), "body", str_replace("\n", " ", $message));
        // On ajoute l'id du message à la liste des ids des messages déjà postés par cet utilisateur
        $redis->lpush("posts:$userId", $postid);
        // On ajoute l'id du message à la timeline
        $redis->lpush("timeline", $postid);
    }

    /**
     * Récupération de la timeline
     *
     */
    public static function getTimeline()
    {
        // Connexion à Redis
        $redis = RedisConnection::getInstance()->getClient();
        // On récupère les 50 derniers posts
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
