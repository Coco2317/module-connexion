<?php
session_start();

if (isset($_SESSION['message'])) {
    echo '<p style="color: green;">' . htmlspecialchars($_SESSION['message']) . '</p>';
    unset($_SESSION['message']);
}

$title = "Accueil";
include 'header.php';
?>

<h2>Bienvenue sur le site de connexion</h2>
<p>Ceci est un petit site PHP permettant de s'inscrire, se connecter et gérer son profil.</p>

<?php include 'footer.php'; ?>
