<?php

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$routes = new RouteCollection();

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
