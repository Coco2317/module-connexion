<?php
$servername = "localhost";
$username = "root"; 
$password = "";   
$dbname = "moduleconnexion";

$conn = new mysqli($servername, $username, $password, $dbname); /*nouvelle connexion base de données*/

if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}
?>
