<?php
session_start();

// Stocker le message dans une variable temporaire
$message = "Vous avez été déconnecté.";

// Détruire la session actuelle
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}
session_destroy();

// Démarrer une nouvelle session pour stocker le message
session_start();
$_SESSION['message'] = $message;
session_write_close();

header("Location: index.php");
exit;
