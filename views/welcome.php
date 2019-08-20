<style>
    .namespace {
        color: #8e29b0;
        font-weight: bolder;
    }

    .key {
        font-weight: bolder;
    }

    .value {
        color: chocolate;
    }
</style>
<h1 class="text-center">Bienvenue sur 3WATouittes</h1>
<hr>
<div class="row">

    <div class="col-9">
        <h2>C'est quoi ce machin? <small>(à lire attentivement)</small></h2>

        <p>
            3WATouittes, c'est ma tentative de création d'un Twitter-like, dont les données sont entièrement gérées en Redis.
            Elle est basée sur <a href="https://redis.io/topics/twitter-clone">ce tutoriel</a> dont le code source se trouve ici :
            <a href="https://github.com/antirez/retwis">https://github.com/antirez/retwis</a>. Ces deux pages contiennent un grand nombre d'infos intéressantes,
            sur les types de données notamment et comment les mettre en oeuvre dans un cas concret d'utilisation. Cependant, leur code est assez vieux maintenant
            et assez mal foutu en règle générale, ce qui le rend difficile à lire. Je me suis dit que ça vaudrait le coup d'essayer d'en refaire une implémentation
            en POO, et donc voilà!
        </p>
        <p>
            Petite précision, le but n'était pas de pondre un code parfait, je n'ai rien optimisé, rien sécurisé, XSS, CSRF etc. N'essayez pas de gruger les forms en insérant des données fancy-tarabiscotées, ils ne tiendront pas le coup ;). Des noms d'utilisateurs en chiffres et lettres sans espaces, des touittes sans caractères spéciaux, et tout le monde sera content!
        </p>
        <p>
            L'implémentation Objet est également loin d'être la meilleure, je voulais juste utiliser un peu Symfony au passage, dans un but de révisions, mais pas forcément tout le framework non plus, pour essayer de comprendre un peu ce qui se passe en coulisses parfois. J'ai donc juste installé le HttpFoundation et le Router (et Predis, évidemment), pour gagner un peu de temps, et j'ai essayé de coder vite fait le reste. Ce n'est vraiment pas le plus important. Pas de tests unitaires hein, bien évidemment.
        </p>
        <p>
            J'ai essayé de documenter les classes le plus clairement possible, et si quelque chose vous échappe, vous pourrez toujours me demander. Si vous trouvez que quelque chose est bizarre, et que vous vous demandez pourquoi je l'ai codé de cette façon, c'est sans doute parce que je l'ai codé n'importe comment et que c'est donc effectivement bizarre! J'ai quand même essayé de suivre une logique pour que la partie Redis soit plus simple à suivre. De fait, toutes les classes utilisant des appels à Redis sont soit les entités, soit contenues dans le dossier Services. Aucun contrôleur ne touche à Redis.
        </p>
        <p>
            Si vous avez une configuration spéciale de Redis, vous devrez modifier le constructeur de la classe <span class="namespace">App\Services\RedisConnection </span>, ma configuration est celle d'un serveur par défaut, en local et avec le port 6379.
        </p>
        <p>Dernière chose, par rapport au tuto initial, il y a une fonctionnalité que je n'ai pas codée, la possibilité d'avoir une pagination quand on affiche nos Touittes. C'est pas bien compliqué à mettre en place quand on a compris tout le reste.</p>
        <h3>Présentation du projet</h3>
        <p>
            Le plus important, je pense, dans ce projet, ce sont les structures des données telles qu'elles sont définies au sein de la base. Une fois qu'on a compris ça, on a fait 80% du taf. Et pour bien comprendre
            les choix faits, il faut d'abord comprendre ce que fait l'application. Elle permet donc à des utilisateurs connectés (dont la gestion, inscription/login/authentification, se fait via Redis) de poster des petits messages de 280 caractères max,
            d'accéder à une liste de leurs messages, à une liste des messages de tous les utilisateurs sous forme de timeline, à un profil utilisateur listant les messages de cet utilisateur précis, à une liste chronologique des derniers utilisateurs inscrits;
            elle permet également de suivre des utilisateurs et donc d'être suivis eux-mêmes.
        </p>
        <p>
            La structure choisie pour les données est importante car le système clé-valeurs proposé par Redis rend impossible un grand nombre d'opérations qu'on a l'habitude de faire en relationnel.
            Les jointures évidemment, et tout ce qui s'en rapproche, mais également des choses bien plus simples comme des WHERE ou la gestion d'un auto-increment. Un petit lien sympa à ce sujet : <a href="https://redislabs.com/blog/get-sql-like-experience-redis/">How to get SQL-like Experience with Redis?</a> (l'ensemble du site est une mine d'informations d'ailleurs).
        </p>
        <p>
            Je vais donc faire un petit résumé des clés<span style="color:#F39C12"><strong>*</strong></span> et leurs différents <a href="https://redis.io/topics/data-types-intro">types de données</a> utilisés dans le projet ainsi que leurs bénéfices.
        </p>
        <div class="alert alert-warning">* "clé" semble être le nom équivalent à ce que serait des documents en Mongo ou des tables en SQL, une clé regroupe un ensemble de données; ça peut parfois porter à confusion, quand on parle de Hashs notamment, les hashs étant en gros des tableaux associatifs, composés de "clés", donc... et de valeurs. Mais par la suite, je continuerai d'appeler "clés" ces ensembles de données en Redis. J'espère arriver à être assez clair.</div>
        <h3>Gestion des utilisateurs</h3>
        <p>
            Un utilisateur est représenté par une clé de type <a href="https://redis.io/commands#hash">HASH</a>. Il contient les infos suivantes: un pseudo (<span class="key">username</span>), un mot de passe (<span class="key">password</span>, non crypté, hein, la flemme...), et une chaîne secrète (<span class="key">auth</span>) qui sera également stockée dans un cookie et servira à l'authentification.
            <br>La clé se présente sous la forme suivante : <span class="key">user:{int}</span> où {int} représente un indentifiant entier unique.
            On aura donc des utilisateurs avec des clés du genre <code>user:1</code>, <code>user:13</code>, <code>user:25455</code>, etc.
            Il n'est pas possible de gérer ces identifiants à l'aide d'un auto-incrément, ma première idée aurait été de déléguer cette gestion à PHP mais un pattern reconnu en NoSql clé-valeurs
            semble être de créer une autre clé, nommée dans cette appli <code>next_user_id</code>, qu'on incrémentera à chaque création d'un nouvel utilisateur (il faut bien comprendre qu'une fois incrémentée, la valeur courante de ce next_user_id deviendra l'id du nouvel utilisateur créé). <br> Si à un instant t, la valeur de next_user_id est 15, et qu'on crée un nouvel utilisateur, cet utilisateur sera donc <code>user:16</code>.
            <br>La création se fera donc de la manière suivante:
        </p>
        <ul>
            <li><a href="https://redis.io/commands/incr">INCR</a> next_user_id</li>
            <li><a href="https://redis.io/commands/hmset">HMSET</a> <em>user:valeur_de_</em>next_user_id <span class="key">username</span> <span class="value">mon_pseudo</span> <span class="key">password</span> <span class="value">mon_password</span> <span class="key">auth</span> <span class="value">ma_chaine_secrete</span></li>
        </ul>
        <p>Ce code, transcrit en PHP, se trouve dans la classe <span class="namespace">App\Services\Auth::registerHandler</span></p>

        <p>
            Mais ce n'est pas tout en ce qui concerne les utilisateurs. Nous essayerons d'effectuer toutes les opérations impliquant un utilisateur via son identifiant (le "42" de <code>user:42</code> donc).
            Il serait d'une part assez complexe de devoir parser les clés de type <code>user:42</code> à chaque fois qu'on veut récupérer un id, mais nous aurons aussi besoin de lier l'identifiant à son username (rien que pour les opérations de login) et évidemment pas de 'SELECT id, username FROM user:16' possible...
            Nous allons donc créer une nouvelle clé de type HASH, appelée <span class="key">users</span> qui contiendra des couples <span class="key">username</span> => <span class="key">user_id</span>.
            <br> Pour à peu près les mêmes raisons, nous avons besoin d'un nouveau HASH, appelé <span class="key">auths</span>, qui liera la chaine d'authentification d'un utilisateur (i.e. la valeur du cookie) à son identifiant, ce qui donne des couples <span class="key">auth</span> => <span class="key">user_id</span>.
            <br> Enfin il nous faut pouvoir afficher la liste des utilisateurs dans l'ordre chronologique (inverse, pour être précis, mais ça ne change rien) de leur création. Pour celà, nous utiliserons une clé de type <a href="https://redis.io/commands#sorted_set">ZSET</a>, un SET ordonné, en gros une liste de valeurs uniques ayant chacune, un rang, un "poids" déterminé, ce qui permet de les classer.
            La synthaxe pour entrer des données dans une liste de type ZSET est la suivante : ZADD <span class="key">nom_de_la_clé</span> rang_de_la_donnée <span class="value">donnée</span>. L'astuce ici est d'utiliser l'heure de création (et plus exactement le timestamp de l'heure courante) comme "poids" de l'utilisateur.
            <br> Les opérations supplémentaires à effectuer à chaque création d'un utilisateur sont donc :
        </p>
        <ul>
            <li><a href="https://redis.io/commands/hset">HSET</a> <em>users</em> <span class="key">mon_pseudo</span> <span class="value">valeur_de_<em>next_user_id</em></span></li>
            <li>HSET <em>auths</em> <span class="key">ma_chaine_secrete</span> <span class="value">valeur_de_<em>next_user_id</em></span></li>
            <li><a href="https://redis.io/commands/zadd">ZADD</a> <em>users_by_time</em> un_timestamp <span class="value">mon_pseudo</span> </li>
        </ul>
        <p>Le code PHP de ces instructions se trouve également dans la classe <span class="namespace">App\Services\Auth::registerHandler</span></p>

        <div class="card card-body bg-light mb-4">
            <p>
                <br>Un exemple concret sera plus simple à suivre, je pense. Admettons qu'on veuille créer un nouvel utilisateur.
                <br>Son pseudo sera perceval. <br>Son mot de passe sera degalles. <br>Sa clé auth sera ab12cd34ef56gh78. <br>Imaginons qu'au moment de sa création, la clé <span class=" key">next_user_id</span> vaut 8, et le timestamp (donné par la fonction PHP time(), par ex) vaut 1566240123.
                <br>La création se fera donc de la manière suivante:
            </p>
            <ul>
                <li>INCR next_user_id</li>
                <li>HMSET <em>user:9</em> <span class="key">username</span> <span class="value">perceval</span> <span class="key">password</span> <span class="value">degalles</span> <span class="key">auth</span> <span class="value">ab12cd34ef56gh78</span></li>
                <li>HSET <em>users</em> <span class="key">perceval</span> <span class="value">9</span></li>
                <li>HSET <em>auths</em> <span class="key">ab12cd34ef56gh78</span> <span class="value">9</span></li>
                <li>ZADD <em>users_by_time</em> 1566240123 <span class="value">perceval</span> </li>
            </ul>
        </div>

        <p>
            Voilà! On n'en a pas encore tout à fait fini avec les utilisateurs. Il reste à gérer les abonnements(following)/abonnés(followers).
            <br> Ces deux fonctonnalités sont définies par deux ZSET contenant chacun une liste de user_ids (pourquoi ZSET, au lieu de SET normaux, pour pouvoir classer nos followers par ancienneté je pense... je suis pas un gros adepte de Twitter, je ne sais pas si ça a du sens. Mais c'était la consigne du tuto, donc bon...). Le principe est exactement le même que pour la liste ordonnée <span class="key">users_by_time</span> de tout à l'heure, un timestamp définit le "poids" de chaque id entré.
            <br>L'idée est la suivante. Je suis le <code>user:10</code>, il existera une clé <span class="key">following:10</span> contenant la liste ordonnée des id de tous les utilisateurs que je suis (mes abonnements).
            Il existera également une clé <span class="key">followers:10</span> contenant une liste ordonnée des id de tous les utilisateurs qui me suivent (mes abonnés).
            <br>Quand je suis sur le profil de l'utilisateur <code>user:2500</code>, si je clique sur le bouton <button class="btn btn-primary">Suivre</button>, deux choses se passent:
            <br>- le timestamp courant et 2500 sont ajoutés à la ZSET <span class="key">following:10</span>, ce qui reflète le fait que je suis une personne de plus.
            <br>- le timestamp courant et 10 (mon id) sont ajoutés à la ZSET <span class="key">followers:2500</span>, ce qui reflète le fait qu'une personne de plus suit <code>user:2500</code>.
            <br> Quand je clique sur <button class="btn btn-primary">Ne plus suivre</button> sur le profil de <code>user:352</code>, vous avez compris l'idée, on supprime 10 (mon id) du ZSET <span class="key">followers:352</span> et on supprime 352 du ZSET <span class="key">following:10</span>.
        </p>
        <p>Ces fonctionnalités sont gérées par l'entité <span class="namespace">User</span> au sein de deux méthodes: <span class="namespace">App\Models\User::follow</span> et <span class="namespace">App\Models\User::unfollow</span></p>
        <h3>Posts (ou Touittes, <small>ou Messages, comme je les ai appelés...</small>)</h3>
        <p>
            Après tout ça, la gestion des messages est étonnamment simple. Deux fonctionnalités seulement nous intéressent. L'affichage d'une liste de messages d'UN seul utilisateur (mes messages sur ma page d'accueil, la liste de messages d'un utilisateur lambda lorsque je suis sur son profil, ...) ainsi que la liste ordonnée de l'ensemble des messages postés depuis le Big-Bang, par tout le monde (pour la timeline).
        </p>
        <p>
            Une petite précison au sujet de la timeline, je pense que les auteurs du tuto voulaient implémenter une timeline qui ne m'affiche que les messages postés par moi et tous les utilisateurs auxquels je me suis abonné, ça parait logique... mais dans leur code, cette fonctionnalité n'existe pas (enfin c'est un peu plus compliqué que ça, mais si ça vous intéresse, on pourra en parler). Alors je ne l'ai pas codée non plus. Cependant, la logique à utiliser est très similaire à celle utilisée ailleurs dans l'appli, et c'est sans doute un bon exo, à placer dans la section "Pour aller plus loin...", d'essayer de l'implémenter.
        </p>
        <p>
            Comme pour les utilisateurs, chaque post sera un HASH représenté par une clé du genre post:{int}. Je vais passer plus vite là-dessus, c'est le même système qu'avant, exactement. On a donc une clé <span class="key">next_post_id</span> qu'on incrémentera avant chaque nouveau post, et des posts qui auront la forme suivante <code>post:452</code>, <code>post:88</code> etc. Chaque post sera composé de trois valeurs : <span class="key">user_id</span> (qui représente l'auteur), <span class="key">time</span> (date de publication, gérée ici, comme toutes les autres données temporelles, par un timestamp), et <span class="key">body</span> (le message en lui-même).
            <br> La timeline n'est qu'une <a href="https://redis.io/commands#list">LIST</a> d'id de posts (alors bizarrement, alors que l'ordre est important dans une timeline, ils ont utilisé une simple LIST cette fois-ci au lieu d'un ZSET. J'ai suivi leur choix, afin de rester le plus fidèle possible au tuto, mais après j'ai jamais dit que c'étaient les devs les plus futés du monde hein!).
            <br> Il nous faudra enfin une clé de type LIST qui représentera la liste des posts d'un utilisateur en particuler. Même remarque que ci-dessus, pourquoi ue liste et pas un SET ordonné? y'a peut-être une excellente raison mais je l'ai pas cherchée. Cette LIST aura la forme posts:id_user (<code>posts:42</code>, <code>posts:12</code>, etc.) et contiendrea donc une liste de post_ids.
        </p>
        <div class="card card-body bg-light mb-4">
            <p>
                Comme tout à l'heure, un exemple concret de création de post. Je suis le <code>user:10</code>, j'écris le message "3WA forever", la valeur du timestamp est 1566246543 et next_post_id a la valeur 25487 (le précédent message posté est donc <code>post:25487</code>).
            </p>
            <ul>
                <li>INCR next_post_id</li>
                <li>HMSET <em>post:25488</em> <span class="key">user_id</span> <span class="value">10</span> <span class="key">time</span> <span class="value">1566246543</span> <span class="key">body</span> <span class="value">3WA forever</span></li>
                <li><a href="https://redis.io/commands/lpush">LPUSH</a> <em>posts:10</em> <span class="value">25488</span></li>
                <li>LPUSH <em>timeline</em> <span class="value">25488</span></li>
            </ul>
        </div>
        <p>Je vous laisse chercher à quoi correspond LPUSH et comprendre pourquoi on l'utilise ici (par opposition à <a href="https://redis.io/commands/rpush">RPUSH</a>)</p>
        <p>Le code de création d'un nouveau message se trouve ici : <span class="namespace">App\Services\Messages::addMessage</span></p>
        <h3>Conclusion</h3>
        <p>
            Voilà pour la structure des données dans la base Redis, le reste ne consiste qu'à récupérer des données et à les afficher la plupart du temps. C'est assez simple en suivant le code et grâce à la PHPDoc des classes et méthodes. J'ai essayé de commenter la pluaprt des choses, j'ai pu en oublier par contre...
        </p>
        <p>
            Pour finir je rajoute simplement quelques commandes Redis que j'ai utilisées dans le projet:
            <ul>
                <li>
                    <a href="https://redis.io/commands/hget">HGET</a> <em>key</em> <span class="key">hashKey</span>: Récupère la valeur d'un HASH nommé "key", correspondant en gros à key["hashKey"] (c'est dans ces cas là que le terme clé devient confus).
                    <br>Utilisé un peu partout, comme par ex : <span class="namespace">App\Service\Auth::isLogged</span>
                </li>
                <li><a href="https://redis.io/commands/hgetall">HGETALL</a> <em>key</em>: récupère l'ensemble des valeurs contenues dans le HASH "key".
                    <br>Utilisé dans <span class="namespace">App\Models\User::__construct</span>
                </li>
                <li>
                    <a href="https://redis.io/commands/lrange">LRANGE</a> <em>key</em> start stop: Récupère les valeurs de la liste "key", de start à stop
                    <br> Utilisé dans <span class="namespace">App\Models\User::getPosts</span>
                </li>
                <li>
                    <a href="https://redis.io/commands/zscore">ZSCORE</a> <em>key</em> <span class="value">value</span>: vérifie si la valeur value existe dans le ZSET "key"
                    <br> Utilisé dans <span class="namespace">App\Models\User::isFollowedBy</span>
                </li>
                <li>
                    <a href="https://redis.io/commands/zadd">ZADD</a> <em>key</em> score <span class="value">value</span>: ajoute la valeur value et son score ("poids", rang, classement, ...) au ZSET "key"
                    <br> Utilisé dans <span class="namespace">App\Models\User::follow</span>
                </li>
                <li>
                    <a href="https://redis.io/commands/zrem">ZREM</a> <em>key</em> <span class="value">value</span>: supprime la valeur value du ZSET "key"
                    <br> Utilisé dans <span class="namespace">App\Models\User::unfollow</span>
                </li>
                <li>
                    <a href="https://redis.io/commands/zcard">ZCARD</a> <em>key</em>: renvoie le nombre de valeurs contenues dans le ZSET "key"
                    <br> Utilisé dans <span class="namespace">App\Models\User::isFollowingNumber</span>
                </li>
            </ul>
        </p>
    </div>


    <div class="col-3">
        <div class="row">
            <h2>Se connecter</h2>
            <?php if (!empty($errors)) : ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error) echo $error . "<br>"; ?>
            </div>
            <?php endif; ?>
            <form class="form" action="" method="post">
                <div class="form-group">
                    <input class="form-control" type="text" name="username" id="username" placeholder="Pseudo">
                </div>
                <div class="form-group">
                    <input class="form-control" type="password" name="password" id="password" placeholder="Mot de passe">
                </div>
                <button class="btn btn-primary" type="submit">Se connecter</button>
            </form>
        </div>
        <hr>
        <div class="row mb-4">
            <p>Pas encore inscrit? <a href="/register">Créez votre compte</a></p>
        </div>

    </div>

</div>