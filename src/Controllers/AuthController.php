<?php

namespace App\Controllers;

use App\Services\Auth;
use App\Controllers\Controller;


/**
 * Gère les inscriptions et authentifications des utilisateurs
 * 
 * La gestion des inscriptions et des connexions se fait en deux temps.
 * La méthode AuthController::register est appelée via l'url (/register) et appelle elle-même
 * une méthode App\Models\Auth::loginHandler
 * De même, la méthode AuthController::welcome est appelée via l'url (/welcome) et appelle 
 * une méthode App\Models\Auth::loginHandler
 * C'est surtout une façon de découper le code afin que la partie Redis soit mieux visible. 
 * En effet tout le traitement sur la base de données se trouve dans les trois
 * méthodes : App\Models\Auth::registerHandler, App\Models\Auth::loginHandler et App\Models\Auth::login
 * 
 */
class AuthController extends Controller
{
    /**
     * Déconnexion de l'utilisateur
     * Route correspondante (/logout)
     */
    public function logout()
    {
        // Destruction du cookie d'authentification
        unset($_COOKIE[APP_COOKIE_NAME]);
        setcookie(APP_COOKIE_NAME, null, -1, '/');
        return $this->redirect('/');
    }

    /**
     * Gère l'affichage de la page d'accueil pour un utilisateur non connecté et le formulaire de connexion
     * Route correspondante (/)
     *
     * @param Symfony\Component\HttpFoundation\Request $request
     */
    public function welcome($request)
    {
        // Si l'utilisateur est authentifié on le redirige sur la homepage
        if (Auth::isLogged()) return $this->redirect("/home");

        // Si le formulaire a été soumis...
        if (!empty($request->request->all())) {
            // On traite les données, on les récupère... 
            $authentifier = new Auth();
            $result = $authentifier->loginHandler($request);
            // ...puis on les extrait (création des variables $errors et $userId)
            extract($result);
            // Si les données sont valides... 
            if ($userId && empty($errors)) {
                $authentifier->login($userId);
                return $this->redirect('/home');
            }
        }

        // Si le formulaire n'a pas été soumis ou si il contient des erreurs...
        return $this->render('welcome', [
            "errors" => $errors ?? []
        ]);
    }

    /**
     * Gère le formulaire d'inscription
     * Route correspondante (/register)
     *
     * @param Symfony\Component\HttpFoundation\Request $request
     */
    public function register($request)
    {
        // Si l'utilisateur est authentifié on le redirige sur la homepage
        if (Auth::isLogged()) return $this->redirect("/home");

        // Si le formulaire a été soumis...
        if (!empty($request->request->all())) {
            // On traite les données, on les récupère... 
            $authentifier = new Auth();
            $result = Auth::registerHandler($request);
            // ...puis on les extrait (création des variables $errors et $userId)
            extract($result);
            // Si les données sont valides... 
            if ($userId && empty($errors)) {
                Auth::login($userId);
                return $this->redirect('/home');
            }
        }

        // Si le formulaire n'a pas été soumis ou si il contient des erreurs...
        return $this->render('register', [
            "errors" => $errors ?? []
        ]);
    }
}
