<?php

namespace App\Controllers;

use App\Models\User;
use App\Services\Auth;
use App\Services\Users;
use App\Services\Messages;

class AppController extends Controller
{

    /**
     * Gère l'affichage de la page d'accueil des utilisateurs authentifiés
     * Route correspondante (/home)
     */
    public function home($request)
    {
        // Si l'utilisateur est connecté, il accède à la page d'accueil du réseau social...
        if (Auth::isLogged()) {
            // Création de l'utilisateur courant
            $user = new User(Auth::currentUserId());
            // Gestion de l'ajout d'un touitte en base de données, si besoin (i.e. si le formulaire a été rempli)
            if (!empty($request->request->all() && !empty($message = $request->request->get("message")))) {
                Messages::addMessage($message, $user->getId());
            }

            return $this->render("home", ["user" => $user]);
        }
        // ...sinon, on le redirige sur la page d'accueil des utilisateurs non connectés
        return $this->redirect("/");
    }

    /**
     * Gère l'affichage de la timeline
     * Route correspondante (/timeline)
     */
    public function timeline()
    {
        // Si l'utilisateur est connecté...
        if (Auth::isLogged()) {
            // Création de l'utilisateur courant
            $user = new User(Auth::currentUserId());
            // Récupération des dix derniers utilisateurs créés
            $recentUsers = Messages::getUsersByCreationDate();
            // Récupération de la timeline
            $timeline = Messages::getTimeline();

            return $this->render("timeline", [
                "user" => $user,
                'timeline' => $timeline,
                "recentUsers" => $recentUsers,
            ]);
        }
        // ...sinon, on le redirige sur la page d'accueil des utilisateurs non connectés
        return $this->redirect("/");
    }

    /**
     * Gère l'affichage d'un profil utilisateur
     * Route correspondante (/profile/{username})
     * 
     * @param Symfony\Component\HttpFoundation\Request $request
     */
    public function profile($request)
    {
        // Si l'utilisateur est connecté...
        if (Auth::isLogged()) {
            // Création de l'utilisateur courant
            $user = new User(Auth::currentUserId());
            // Création de l'utilisateur dont le profil va être affiché
            $requestedUser = new User(Users::getUserIdByUsername(urldecode($request->attributes->get("user"))));
            // Gestion du bouton (Suivre)/(Ne plus suivre)

            if (!empty($request->request->all())) {
                if ($request->request->get("follow") == "unfollow") $user->unfollow($requestedUser);
                if ($request->request->get("follow") == "follow") $user->follow($requestedUser);
            }

            return $this->render("profile", [
                "user" => $user,
                "requestedUser" => $requestedUser,
                "isFollowed" => $requestedUser->isFollowedBy($user),
            ]);
        }
        // ...sinon, on le redirige sur la page d'accueil des utilisateurs non connectés
        return $this->redirect("/");
    }
}
