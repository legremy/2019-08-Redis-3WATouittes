<h1 class="text-center">La cabane de <?= $user->getUsername() ?></h1>
<hr>
<div class="row">

    <div class="col-8">
        <h2>Qu'ai-je envie de dire</h2>
        <form class="form" action="" method="post">
            <div class="form-group">
                <textarea class="form-control" name="message" id="message" maxlength="280" rows="4" style="resize:none"></textarea>
            </div>
            <div class="form-group text-right">
                <button class="btn btn-primary">Publier</button>
            </div>
        </form>
        <?php if (!empty($user->getPosts())) : ?>
        <?php foreach ($user->getPosts() as $post) : ?>
        <hr>
        <p>
            <small>Il y a <?= $post->getElapsedTime(); ?>, </small>
            <strong><a href="profile/<?= urlencode($user->getUsername()) ?>"><?= $user->getUsername() ?></a></strong> a dit :
            <br><?= $post->getBody(); ?>
        </p>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="col-4" style="border-left:1px solid #ccc;">
        <h2>Mon réseau</h2>
        <br><?= $user->isFollowingNumber(); ?> abonnement(s)
        <br><?= $user->isFollowedByNumber(); ?> abonné(s)
        </p>
    </div>

</div>