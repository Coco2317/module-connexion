  <?php
  require_once 'config.php'; 

  $erreur = ''; 

  // ----------- PARTIE 2 : TRAITEMENT DU FORMULAIRE DE CONNEXION -----------
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      // Récupère les données du formulaire en les nettoyant
      $login = htmlspecialchars(trim($_POST['login']));
      $password = trim($_POST['password']);

      // Vérifie si les champs sont vides
      if (empty($login) || empty($password)) {
          $erreur = "Veuillez remplir tous les champs.";
      } else {
          // Prépare une requête pour chercher l'utilisateur avec ce login
          $stmt = $conn->prepare("SELECT id, login, prenom, nom, password FROM utilisateurs WHERE login = ?");
          if ($stmt) {
              $stmt->bind_param("s", $login); 
              $stmt->execute(); 
              $result = $stmt->get_result(); // 

              // Vérifie si un seul utilisateur correspond
              if ($result && $result->num_rows === 1) {
                  $user = $result->fetch_assoc(); // On récupère les infos de l’utilisateur

                  // Vérifie si le mot de passe est bon
                  if (password_verify($password, $user['password'])) {
                      // Le mot de passe est correct : on connecte l'utilisateur
                      $_SESSION['id'] = $user['id'];
                      $_SESSION['login'] = $user['login'];
                      $_SESSION['prenom'] = $user['prenom'];
                      $_SESSION['nom'] = $user['nom'];

                      // Si l'utilisateur est "admin", on l’envoie vers admin.php
                      if ($user['login'] === 'admin') {
                          header("Location: admin.php");
                      } else {
                          header("Location: profil.php");
                      }
                      exit; // On arrête le script ici
                  } else {
                      // Mauvais mot de passe
                      $erreur = "Mot de passe incorrect.";
                  }
              } else {
                  // Aucun utilisateur trouvé avec ce login
                  $erreur = "Login introuvable.";
              }
          } else {
              // La requête SQL n'a pas pu être préparée
              $erreur = "Erreur lors de la préparation de la requête.";
          }
      }
  }
  ?>


  <!DOCTYPE html>
  <html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Connexion - PilatesFlow</title>
    <link rel="stylesheet" href="connexion.css" />
  </head>
  <body>

    <div class="container">

      <div class="left-side"></div>

      <div class="right-side">
        <div class="logo">
    <img src="assets/symbole.jpg" alt="Logo Pilates" />
  </div>
        <h1>Connexion</h1>
        <p class="description">Bienvenue, connectez-vous pour accéder à votre espace Pilates personnalisé.</p>

        <?php if (!empty($erreur)): ?>
    <p class="error"><?php echo $erreur; ?></p>
  <?php endif; ?>


        <form method="post" action="">
          <input type="text" name="login" placeholder="Login" required />
          <input type="password" name="password" placeholder="Mot de passe" required />
          <button type="submit" class="btn">Se connecter</button>
        </form>

        <a href="inscription.php" class="register-link">Pas encore de compte ? Inscrivez-vous</a>
      </div>

    </div>

  </body>
  </html>
