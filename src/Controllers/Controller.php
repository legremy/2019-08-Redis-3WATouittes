<?php

namespace App\Controllers;

use App\Services\Auth;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Controller
{
    protected function render($route, array $params = [])
    {
        extract($params);
        ob_start();
        include sprintf('../views/%s.php', $route);
        $content_data = ob_get_clean();
        include("../views/template/base.php");
        return new Response(ob_get_clean());
    }

    protected function redirect($path)
    {
        return new RedirectResponse($path);
    }
}
