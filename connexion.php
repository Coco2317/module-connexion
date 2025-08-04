<?php
session_start();
require_once 'config.php';

$title = "Connexion";
$erreur = '';

// Si un message est stocké (après inscription), on l’affiche une seule fois
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = htmlspecialchars(trim($_POST['login']));
    $password = trim($_POST['password']);

    if (empty($login) || empty($password)) {
        $erreur = "Veuillez remplir tous les champs.";
    } else {
        $stmt = $conn->prepare("SELECT id, login, prenom, nom, password FROM utilisateurs WHERE login = ?");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                // Connexion réussie → on crée les variables de session
                $_SESSION['id'] = $user['id'];
                $_SESSION['login'] = $user['login'];
                $_SESSION['prenom'] = $user['prenom'];
                $_SESSION['nom'] = $user['nom'];

                // Redirection
                if ($user['login'] === 'admin') {
                    header("Location: admin.php");
                } else {
                    header("Location: profil.php");
                }
                exit;
            } else {
                $erreur = "Mot de passe incorrect.";
            }
        } else {
            $erreur = "Login introuvable.";
        }
    }
}
?>

<?php include 'header.php'; ?>

<h2>Connexion</h2>

<?php if (!empty($message)) : ?>
    <p style="color: green;"><?php echo $message; ?></p>
<?php endif; ?>

<?php if (!empty($erreur)) : ?>
    <p style="color: red;"><?php echo $erreur; ?></p>
<?php endif; ?>

<form method="post">
    <input type="text" name="login" placeholder="Login" value="<?php echo isset($login) ? $login : ''; ?>" required><br>
    <input type="password" name="password" placeholder="Mot de passe" required><br>
    <button type="submit">Se connecter</button>
</form>

<?php include 'footer.php'; ?>
