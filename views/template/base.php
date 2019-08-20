<?php

use App\Services\Auth;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://bootswatch.com/4/flatly/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <title>Document</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <a class="navbar-brand" href="/">3WATouittes</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarText">
            <ul class="navbar-nav mr-auto">
                <?php if (isset($user) && Auth::isLogged()) : ?>
                <li class="nav-item">
                    <a class="nav-link" href="/home">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/timeline">Timeline</a>
                </li>
                <?php endif; ?>
            </ul>
            <?php if (isset($user) && Auth::isLogged()) : ?>
            <span class="navbar-text">
                Connecté en tant que <?= $user->getUsername() ?>
                <a class="btn btn-success" href="/logout">se déconnecter</a>
            </span>
            <?php endif; ?>
        </div>
    </nav>
    <div class="container">
        <?= $content_data; ?>
    </div>
    <hr>
    <div style="height:150px;background-color:#eee"></div>
</body>

</html>