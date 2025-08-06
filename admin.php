<?php
session_start();
require_once 'config.php';

$title = "Administration";

// Vérifie que l'utilisateur est connecté et est "admin"
if (!isset($_SESSION['login']) || $_SESSION['login'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Récupère tous les utilisateurs depuis la base
$result = $conn->query("SELECT id, login, prenom, nom FROM utilisateurs ORDER BY id ASC");

include 'header.php';
?>

<main>
    <h2>Administration</h2>
    <p>Liste de tous les utilisateurs :</p> 
    <br>
    
    <?php if ($result && $result->num_rows > 0): ?>
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
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['login']) ?></td>
                        <td><?= htmlspecialchars($row['prenom']) ?></td>
                        <td><?= htmlspecialchars($row['nom']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucun utilisateur trouvé dans la base de données.</p>
    <?php endif; ?>
</main>

<?php include 'footer.php'; ?>
