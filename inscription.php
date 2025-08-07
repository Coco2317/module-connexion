<?php
require_once 'config.php'; 

$erreur = '';   
$success = '';  

// ----------- PARTIE 2 : TRAITEMENT DU FORMULAIRE -----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupère et nettoie les données envoyées par le formulaire
    $login = htmlspecialchars(trim($_POST['login']));     
    $prenom = htmlspecialchars(trim($_POST['prenom']));   
    $nom = htmlspecialchars(trim($_POST['nom']));         
    $password = trim($_POST['password']);                 
    $conf_password = trim($_POST['conf_password']);       

    // Vérifie que tous les champs sont remplis
    if (empty($login) || empty($prenom) || empty($nom) || empty($password) || empty($conf_password)) {
        $erreur = "Tous les champs sont obligatoires.";
    }
    // Vérifie que les mots de passe correspondent
    elseif ($password !== $conf_password) {
        $erreur = "Les mots de passe ne correspondent pas.";
    }
    // Vérifie la longueur minimale du mot de passe
    elseif (strlen($password) < 4) {
        $erreur = "Le mot de passe doit contenir au moins 4 caractères.";
    }
    else {
        // Vérifie si le login est déjà utilisé dans la base
        $stmt = $conn->prepare("SELECT id FROM utilisateurs WHERE login = ?");
        $stmt->bind_param("s", $login);   // "s" = string, lie la variable $login au paramètre
        $stmt->execute();               
        $stmt->store_result();        

        if ($stmt->num_rows > 0) {
            // Login déjà pris
            $erreur = "Ce login est déjà utilisé.";
        } else {
            // Hachage sécurisé du mot de passe avant insertion dans la base
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Prépare la requête d'insertion d'un nouvel utilisateur
            $stmt = $conn->prepare("INSERT INTO utilisateurs (login, prenom, nom, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $login, $prenom, $nom, $hashed_password);

            if ($stmt->execute()) {
                // Insertion réussie
                $success = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
            } else {
                // Erreur lors de l'insertion
                $erreur = "Erreur lors de l'inscription. Réessayez.";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link rel="stylesheet" href="inscription.css">
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-link">↩ Retour à l'Accueil</a>
        <h2>Créer un compte</h2>

        <?php if ($erreur): ?>
            <p class="error"><?= htmlspecialchars($erreur) ?></p>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
            <div style="text-align: center; margin-top: 20px;">
                <a href="index.php">
                    <button>Se connecter</button>
                </a>
            </div>
        <?php else: ?>
            <form method="post" action="">
                <label>Login</label>
                <input type="text" name="login" required>

                <label>Prénom</label>
                <input type="text" name="prenom" required>

                <label>Nom</label>
                <input type="text" name="nom" required>

                <label>Mot de passe</label>
                <input type="password" name="password" required>

                <label>Confirmer le mot de passe</label>
                <input type="password" name="conf_password" required>

                <button type="submit">S'inscrire</button>
            </form>

            <p class="link">Déjà un compte ? <a href="index.php">Connectez-vous</a></p>
        <?php endif; ?>
    </div>
</body>
</html>
