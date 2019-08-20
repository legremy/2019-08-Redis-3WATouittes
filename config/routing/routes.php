<?php

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$routes = new RouteCollection();

// L'implémentation du router est très sommaire et n'a aucun intérêt à part celui de faire 
// fonctionner l'appli tant bien que mal (n'essayez pas de reproduire ça chez vous!)
$routesDefinition = [

    [
        "name" => "welcome",
        "uri" => "/",
        "controller" => "Auth",
        "method" => "welcome"
    ],
    [
        "name" => "register",
        "uri" => "/register",
        "controller" => "Auth",
        "method" => "register"
    ],

    [
        "name" => "logout",
        "uri" => "/logout",
        "controller" => "Auth",
        "method" => "logout"
    ],
    [
        "name" => "home",
        "uri" => "/home",
        "controller" => "App",
        "method" => "home"
    ],
    [
        "name" => "timeline",
        "uri" => "/timeline",
        "controller" => "App",
        "method" => "timeline"
    ],
    [
        "name" => "profile",
        "uri" => "/profile/{user}",
        "controller" => "App",
        "method" => "profile"
    ],

];

foreach ($routesDefinition as $route) {
    $controller = "App\Controllers\\" . $route["controller"] . "Controller";
    $routes->add($route["name"], new Route(
        $route["uri"],
        [
            '_controller' => [new $controller(), $route["method"]],
        ]
    ));
}


return $routes;
