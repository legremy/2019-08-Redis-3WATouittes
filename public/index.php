<?php

require_once "../vendor/autoload.php";

use App\App;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;


define("APP_COOKIE_NAME", "3watouittes-auth");

$request = Request::createFromGlobals();

$routes = include '../config/routing/routes.php';

$context = new RequestContext();
$context->fromRequest($request);



$matcher = new UrlMatcher($routes, $context);

$app = new App($matcher);

$response = $app->handle($request);

$response->send();
