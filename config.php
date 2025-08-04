<?php
$conn = new mysqli('localhost', 'root', '', 'moduleconnexion');
if ($conn->connect_error) {
    die('Erreur de connexion : ' . $conn->connect_error);
}
?>
