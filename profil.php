<?php
session_start();
require_once 'config.php';

$title = "Mon profil";

// Vérification : l’utilisateur est connecté ?
if (!isset($_SESSION['id'])) {
    header("Location: connexion.php");
    exit;
}

$erreur = '';
$success = '';

// Récupération des infos actuelles
$id = $_SESSION['id'];
$stmt = $conn->prepare("SELECT login, prenom, nom FROM utilisateurs WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_login = htmlspecialchars(trim($_POST['login']));
    $new_prenom = htmlspecialchars(trim($_POST['prenom']));
    $new_nom = htmlspecialchars(trim($_POST['nom']));
    $new_password = trim($_POST['password']);
    $conf_password = trim($_POST['conf_password']);

    // Vérification des champs
    if (empty($new_login) || empty($new_prenom) || empty($new_nom)) {
        $erreur = "Les champs login, prénom et nom sont obligatoires.";
    } elseif (!empty($new_password) && strlen($new_password) < 8) {
        $erreur = "Le mot de passe doit contenir au moins 8 caractères.";
    } elseif (!empty($new_password) && $new_password !== $conf_password) {
        $erreur = "Les mots de passe ne correspondent pas.";
    } else {
        // Vérifie si le login a changé et s’il est déjà pris
        if ($new_login !== $user['login']) {
            $check = $conn->prepare("SELECT id FROM utilisateurs WHERE login = ? AND id != ?");
            $check->bind_param("si", $new_login, $id);
            $check->execute();
            $check->store_result();
            if ($check->num_rows > 0) {
                $erreur = "Ce login est déjà utilisé.";
            }
        }

        // Si pas d’erreurs jusque-là, on met à jour
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

            // Mettre à jour les infos de session
            $_SESSION['login'] = $new_login;
            $_SESSION['prenom'] = $new_prenom;
            $_SESSION['nom'] = $new_nom;

            $success = "Profil mis à jour avec succès.";
        }
    }
}
?>

<?php include 'header.php'; ?>

<h2>Mon profil</h2>

<?php if ($success): ?>
    <p style="color:green;"><?php echo $success; ?></p>
<?php endif; ?>

<?php if ($erreur): ?>
    <p style="color:red;"><?php echo $erreur; ?></p>
<?php endif; ?>

<form method="post">
    <input type="text" name="login" placeholder="Login" value="<?php echo isset($new_login) ? $new_login : $user['login']; ?>" required><br>
    <input type="text" name="prenom" placeholder="Prénom" value="<?php echo isset($new_prenom) ? $new_prenom : $user['prenom']; ?>" required><br>
    <input type="text" name="nom" placeholder="Nom" value="<?php echo isset($new_nom) ? $new_nom : $user['nom']; ?>" required><br>
    <input type="password" name="password" placeholder="Nouveau mot de passe (facultatif)"><br>
    <input type="password" name="conf_password" placeholder="Confirmer le mot de passe"><br>
    <button type="submit">Mettre à jour</button>
</form>

<?php include 'footer.php'; ?>
