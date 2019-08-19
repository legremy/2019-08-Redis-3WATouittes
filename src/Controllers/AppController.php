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
            $user = new User(Auth::currentUserId());
            if (!empty($request->request->all() && !empty($message = $request->request->all()["message"]))) {
                Messages::addMessage($message, $user->getId());
            }
            return $this->render("home", ["user" => $user]);
        }
        // Sinon, on le redirige sur la page d'accueil des utilisateurs non connectés
        return $this->redirect("/");
    }

    /**
     * Gère l'affichage de la timeline
     * Route correspondante (/timeline)
     */
    public function timeline()
    {
        if (Auth::isLogged()) {
            $user = new User(Auth::currentUserId());
            $recentUsers = Messages::getUsersByCreationDate();
            $timeline = Messages::getTimeline();
            return $this->render("timeline", [
                "user" => $user,
                'timeline' => $timeline,
                "recentUsers" => $recentUsers,
            ]);
        }
        // Sinon, on le redirige sur la page d'accueil des utilisateurs non connectés
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
        if (Auth::isLogged()) {
            $user = new User(Auth::currentUserId());
            $requestedUser = new User(Users::getUserIdByUsername(urldecode($request->attributes->get("user"))));

            if (!empty($request->request->all())) {

                if ($request->request->all()["follow"] == "unfollow") $user->unfollow($requestedUser);
                if ($request->request->all()["follow"] == "follow") $user->follow($requestedUser);
            }

            return $this->render("profile", [
                "user" => $user,
                "requestedUser" => $requestedUser,
                "isFollowed" => $requestedUser->isFollowedBy($user),
            ]);
        }
        return $this->redirect("/");
    }
}
