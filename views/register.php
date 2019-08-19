 <h1>Inscription</h1>
 <?php if (!empty($errors)) : ?>
 <div class="alert alert-danger">
     <?php foreach ($errors as $error) {
                echo $error . "<br>";
            }
            ?>
 </div>
 <?php endif; ?>

 <form class="form" action="" method="post">
     <div class="form-group">
         <input class="form-control" type="text" name="username" id="username" placeholder="Pseudo">
     </div>
     <div class="form-group">
         <input class="form-control" type="password" name="password" id="password" placeholder="Mot de passe">
     </div>
     <button class="btn btn-primary" type="submit">Créer mon compte</button>
 </form>