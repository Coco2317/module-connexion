<?php
session_start();
require_once 'config.php';

$title = "Administration";

// Vérifie si l’utilisateur est connecté et s’il est bien "admin"
if (!isset($_SESSION['login']) || $_SESSION['login'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Récupère tous les utilisateurs
$result = $conn->query("SELECT id, login, prenom, nom FROM utilisateurs ORDER BY id ASC");
?>

<?php include 'header.php'; ?>

<h2>Administration</h2>
<p>Liste de tous les utilisateurs :</p>

<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>ID</th>
            <th>Login</th>
            <th>Prénom</th>
            <th>Nom</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['login']); ?></td>
                <td><?php echo htmlspecialchars($row['prenom']); ?></td>
                <td><?php echo htmlspecialchars($row['nom']); ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>
