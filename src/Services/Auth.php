<?php

namespace App\Services;

class Auth
{

    public static function isLogged()
    {

        $redis = RedisConnection::getInstance()->getClient();
        if (isset($_COOKIE[APP_COOKIE_NAME]) && ($redis->hget("auths", $_COOKIE[APP_COOKIE_NAME]))) {
            return true;
        }
        return false;
    }

    public static function currentUserId()
    {
        $redis = RedisConnection::getInstance()->getClient();
        return $redis->hget("auths", $_COOKIE[APP_COOKIE_NAME]);
    }

    /**
     * Connexion de l'utilisateur ayant l'id $userId
     *
     * @param int $userId
     */
    public function login(int $userId)
    {
        // Connexion à Redis
        $redis = RedisConnection::getInstance()->getClient();
        // Récupération de la chaine unique associée à un utilisateur
        $authSecret = $redis->hget("user:$userId", "auth");
        // Construction du cookie d'authentification
        setcookie(APP_COOKIE_NAME, $authSecret, time() + 3600 * 24 * 365, "/");
    }

    /**
     * Gestion du formulaire de connexion de l'utilisateur (Utilisation de Redis)
     *
     * @param Symfony\Component\HttpFoundation\Request $request
     */
    public function loginHandler($request)
    {
        $errors = [];

        // Connexion à Redis
        $redis = RedisConnection::getInstance()->getClient();

        // On extrait les données du formulaire sous forme de variables 
        // (création des variables $username et $password)
        extract($request->request->all());

        // Vérification des données (vérification légère, juste histoire de...)
        if (empty($username) || empty($password)) $errors[] = "Tous les champs sont obligatoires";
        // Tentative de récupération de l'id de l'utilisateur
        $userId = $redis->hget("users", $username);
        // Si on ne trouve pas cet utilisateur dans la base...
        if (!$userId) $errors[] = "Nom d'utilisateur erroné";
        // Si on trouve l'utilisateur mais que l'id récupéré ne correspond pas au mot de passe attendu...
        if ($userId && $redis->hget("user:$userId", "password") != $password) $errors[] = "Mot de passe incorrect";

        return ["userId" => $userId, "errors" => $errors];
    }

    /**
     * Gestion du formulaire d'inscription (Utilisation de Redis)
     *
     * @param Symfony\Component\HttpFoundation\Request $request
     */
    public static function registerHandler($request)
    {
        $errors = [];
        $userId = null;
        // Connexion à Redis
        $redis = RedisConnection::getInstance()->getClient();

        // On extrait les données du formulaire sous forme de variables 
        // (création des variables $username et $password)
        extract($request->request->all());

        // Vérification des données (vérification légère, juste histoire de...)
        if (empty($username) || empty($password)) $errors[] = "Tous les champs sont obligatoires";
        // Si le nom d'utilisateur existe déjà dans la base...
        if ($redis->hget("users", $username)) $errors[]  = "Ce nom d'utilisateur n'est pas disponible";

        // Si les données sont valides...
        if (empty($errors)) {
            // On incrémente la valeur de la clé nex_user_id qui garantit que chaque id sera unique
            // Équivaut un peu à un AUTO_INCREMENT en SQL
            $userId = $redis->incr("next_user_id");
            // On définit une chaine unique et impossible à deviner dont on se servira pour construire
            // le cookie "auth"
            $authSecret = md5(uniqid());
            // On ajoute au hash 'users' le nom et l'id de l'utilisateur créé
            $redis->hset("users", $username, $userId);
            // On ajoute au hash 'auths' la chaine secrete et l'id de l'utilisateur créé
            $redis->hset("auths", $authSecret, $userId);
            // On ajoute au SET ordonné 'users_by_time' le nom de l'utilisateur et la 'date' (timestamp) de création 
            $redis->zadd("users_by_time", time(), $username);
            // On ajoute enfin l'utilisateur dans un hash défini par une clé de type "user:45"
            $redis->hmset(
                "user:$userId",
                "username",
                $username,
                "password",
                $password,
                "auth",
                $authSecret
            );
            /*  Chaque utilisateur est un nouvel enregistrement dans Redis, 
                et est donc représenté à peu près par:
                    Une clé "user:10" (ou "user:11" ou "user:2000", ...),  
                    dont la valeur associée est de type hash (~tableau associatif) et contient les valeurs:
                        [
                            "username"=>"toto", 
                            "password"=>"titi", 
                            "auth"=>"wxfwd87gfg41s5gh58s78h"
                        ]
             */
        }
        return ["userId" => $userId, "errors" => $errors];
    }
}
