<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo isset($title) ? $title : "Mon site"; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <nav>
        <a href="index.php">Accueil</a>
        <a href="inscription.php">Inscription</a>
        <a href="connexion.php">Connexion</a>
        <?php if (isset($_SESSION['login'])): ?>
            <a href="profil.php">Profil</a>
            <?php if ($_SESSION['login'] === 'admin'): ?>
                <a href="admin.php">Admin</a>
            <?php endif; ?>
            <a href="deconnexion.php">Déconnexion</a>
        <?php endif; ?>
    </nav>
</header>
<main>
