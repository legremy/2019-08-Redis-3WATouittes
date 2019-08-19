<h1 class="text-center">Bienvenue sur 3WATouittes</h1>
<hr>
<div class="row">

    <div class="col-9">
        <h2>C'est quoi?</h2>
        <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Modi eaque voluptatum laboriosam nesciunt deserunt amet dicta, voluptatibus repellendus dolore nobis, porro enim magnam? Porro assumenda ipsam ratione amet eligendi, esse atque rerum natus, maxime nulla distinctio corporis quis. Harum ratione cum, rem quasi voluptates voluptatum totam saepe! Expedita laudantium adipisci minus officia deleniti odit, tenetur aut nesciunt fugit perspiciatis porro iusto voluptatum recusandae blanditiis libero officiis enim! Ipsum inventore, eius voluptas expedita deserunt cum, necessitatibus impedit facere amet culpa vero illo obcaecati labore consequatur veritatis nemo? Dolorum, doloremque? Rem ipsam hic aut optio, repudiandae error architecto! Voluptate dolorem esse eligendi!</p>
        <p>Laboriosam possimus qui blanditiis a ratione itaque nam! Optio corrupti voluptatem labore error, et cumque iusto nulla illum eligendi repellat earum voluptatibus veritatis eum magni reprehenderit, itaque sequi ea, iste consectetur! Aut cumque inventore est doloribus illo et odio architecto, iure aliquam dolor voluptates eius fuga. Eos dolorum officia facere ipsam officiis id temporibus facilis deleniti doloribus, alias totam provident inventore voluptas rem assumenda quos at impedit, error dicta vel. Tempora voluptatum facilis voluptas aliquid vel reiciendis perspiciatis omnis. Suscipit ducimus vitae adipisci obcaecati, provident quos? Excepturi, magni voluptate quas quia veniam incidunt inventore mollitia alias enim expedita dolorem suscipit!</p>
        <p>Unde exercitationem nulla quam nobis ullam tenetur ipsam sequi totam rem. Eaque, delectus amet, temporibus facilis deserunt molestiae minima unde, fugiat atque quas nobis alias voluptate harum ea facere. Ipsa doloribus libero voluptates sequi neque atque accusantium voluptatem explicabo ea, labore nobis? Nisi, tempora? Molestiae quasi laborum repellendus dignissimos aut, iusto culpa eos quam itaque laudantium qui nobis accusantium sed enim architecto nam id ipsa sit aliquam repellat ea placeat dolorem totam. Maxime, optio tenetur. Eaque quam adipisci quae quaerat. Dolores alias iste, quae unde dicta consequuntur, corrupti iure et incidunt tempore deleniti magnam atque, similique a repellendus nisi eaque.</p>
        <p>Atque libero, non ipsum temporibus sint fugit natus ex laborum aliquid porro aut dicta eos animi nobis accusantium sunt, laudantium necessitatibus nihil voluptate tenetur ut neque molestiae odit ratione! Recusandae, reprehenderit tempora tempore aliquam, a culpa vitae dolorem tenetur exercitationem repellendus cum. Placeat, quos! Ratione voluptates illo dolores, quidem natus nobis? Dolorum nemo aliquam laboriosam excepturi quasi odio, sit cum ratione blanditiis porro ducimus dicta culpa reiciendis, quidem eius at optio. Molestias eius minima quasi voluptas cum facilis praesentium quis accusantium mollitia, cumque voluptate, ut possimus, dignissimos repudiandae! Laborum facere quae sapiente deleniti, delectus consequuntur. Nemo culpa consequatur porro ratione?</p>
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