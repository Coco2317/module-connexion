<?php
// ===================================================
// PARTIE 1 - Connexion, session, base de données
// ===================================================
session_start();
require_once 'config.php';

if (!isset($_SESSION['id'])) {
    header("Location: connexion.php");
    exit;
}

// ===================================================
// PARTIE 2 - Initialisation des variables
// ===================================================
$erreur = '';
$success = '';

$id = $_SESSION['id']; 

// ===================================================
// PARTIE 3 - Récupération des infos actuelles de l'utilisateur
// ===================================================

// Prépare une requête pour récupérer les infos du profil depuis la base
$stmt = $conn->prepare("SELECT login, prenom, nom FROM utilisateurs WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();// On récupère les données sous forme de tableau associatif

// ===================================================
// PARTIE 4 - Traitement du formulaire (lors de la soumission en POST)
// ===================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des nouvelles données du formulaire, avec nettoyage (trim + sécurité HTML)
    $new_login = htmlspecialchars(trim($_POST['login']));
    $new_prenom = htmlspecialchars(trim($_POST['prenom']));
    $new_nom = htmlspecialchars(trim($_POST['nom']));
    $new_password = trim($_POST['password']);
    $conf_password = trim($_POST['conf_password']);

// =============================================
    // Vérifications des champs du formulaire
    // =============================================

    if (empty($new_login) || empty($new_prenom) || empty($new_nom)) {
        $erreur = "Les champs login, prénom et nom sont obligatoires.";
    } elseif (!empty($new_password) && strlen($new_password) < 4) {
        $erreur = "Le mot de passe doit contenir au moins 4 caractères.";
    } elseif (!empty($new_password) && $new_password !== $conf_password) {
        $erreur = "Les mots de passe ne correspondent pas.";
    } else {
        if ($new_login !== $user['login']) {
            $check = $conn->prepare("SELECT id FROM utilisateurs WHERE login = ? AND id != ?");
            $check->bind_param("si", $new_login, $id);
            $check->execute();
            $check->store_result();
            if ($check->num_rows > 0) {
                $erreur = "Ce login est déjà utilisé.";
            }
        }

        if (empty($erreur)) {
            if (!empty($new_password)) {
                $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                $update = $conn->prepare("UPDATE utilisateurs SET login = ?, prenom = ?, nom = ?, password = ? WHERE id = ?");
                $update->bind_param("ssssi", $new_login, $new_prenom, $new_nom, $hashed, $id);
            } else {
                $update = $conn->prepare("UPDATE utilisateurs SET login = ?, prenom = ?, nom = ? WHERE id = ?");
                $update->bind_param("sssi", $new_login, $new_prenom, $new_nom, $id);
            }
            $update->execute();

            $_SESSION['login'] = $new_login;
            $_SESSION['prenom'] = $new_prenom;
            $_SESSION['nom'] = $new_nom;

            $success = "Profil mis à jour avec succès.";
            
            $user['login'] = $new_login;
            $user['prenom'] = $new_prenom;
            $user['nom'] = $new_nom;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mon Profil</title>
    <link rel="stylesheet" href="profil.css" />
</head>
<body>

<header>
    <a href="index.php">Accueil</a>
    <a href="deconnexion.php">Déconnexion</a>
</header>

<main>
    <h2>Mon profil</h2>

    <?php if ($success): ?>
        <p class="success"><?php echo $success; ?></p>
    <?php endif; ?>

    <?php if ($erreur): ?>
        <p class="error"><?php echo $erreur; ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <label>Login :
            <input type="text" name="login" value="<?php echo htmlspecialchars($user['login']); ?>" required />
        </label>

        <label>Prénom :
            <input type="text" name="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>" required />
        </label>

        <label>Nom :
            <input type="text" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required />
        </label>

        <label>Nouveau mot de passe (optionnel) :
            <input type="password" name="password" />
        </label>

        <label>Confirmer mot de passe :
            <input type="password" name="conf_password" />
        </label>

        <button type="submit">Mettre à jour</button>
    </form>
</main>

</body>
</html>
