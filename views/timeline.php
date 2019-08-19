<h1 class="text-center">Chronologie</h1>
<hr>
<h2>Les 10 derniers utilisateurs enregistrés</h2>
<p>
    Cette fonctionnalité a pour but de démontrer une utilisation du type de données Redis <code>SET ordonné (ZSET)</code>.
    <br>Elle liste les 10 derniers utilisateurs créés, du plus récent au plus ancien.
</p>
<p>
    <?php foreach ($recentUsers as $recentUser) : ?>
    (<strong><a href="/profile/<?= urlencode($recentUser) ?>"><?= $recentUser ?></a></strong>)
    <?php endforeach; ?>
</p>

<h2>Les 50 derniers messages postés</h2>
<?php if (!empty($timeline)) : ?>

<?php foreach ($timeline as $message) : ?>
<hr>
<p>
    <strong><a href="/profile/<?= urlencode($message->getUsername()) ?>"><?= $message->getUsername() ?></a></strong>:
    <?= $message->getBody() ?>
    <br>Publié il y a <?= $message->getElapsedTime() ?>
</p>

<?php endforeach; ?>
<?php else : ?>
<p>Il n'y a aucun message à montrer</p>
<?php endif; ?>