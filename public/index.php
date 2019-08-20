<?php

require_once "../vendor/autoload.php";

use App\App;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;


// Définition d'une constante pour le nom du cookie qui servira à l'authentification, juste pour que
// ce soit plus simple à changer en cas de besoin. Tout les reste du code de cette page est typique SF et n'a pas grand intérêt
define("APP_COOKIE_NAME", "3watouittes-auth");

$request = Request::createFromGlobals();

$routes = include '../config/routing/routes.php';

$context = new RequestContext();
$context->fromRequest($request);

$matcher = new UrlMatcher($routes, $context);

$app = new App($matcher);

$response = $app->handle($request);

$response->send();
