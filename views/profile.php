<h1>Les derniers Touittes de <?= $requestedUser->getUsername(); ?></h1>
<?php if ($requestedUser->getId() !== $user->getId()) : ?>
<form method="post" action="">
    <?php if (!$isFollowed) : ?>
    <button class="btn btn-primary" value="follow" name="follow" id="follow">Suivre</button>
    <?php else : ?>
    <button class="btn btn-primary" value="unfollow" name="follow" id="follow">Ne plus suivre</button>
    <?php endif; ?>
</form>
<?php endif; ?>

<?php if (!empty($requestedUser->getPosts())) : ?>
<p>
    <?php foreach ($requestedUser->getPosts() as $post) : ?>
    <hr>
    <p>
        <?= $post->getBody(); ?>
        <br><small>Posté il y a <?= $post->getElapsedTime(); ?></small>
    </p>
    <?php endforeach; ?>
</p>
<?php else : ?>
<?= $requestedUser->getUsername(); ?> n'a pas encore touitté
<?php endif; ?>