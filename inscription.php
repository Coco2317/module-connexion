<?php
session_start();
require_once 'config.php';

$title = "Inscription";
$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = htmlspecialchars(trim($_POST['login']));
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $nom = htmlspecialchars(trim($_POST['nom']));
    $password = trim($_POST['password']);
    $conf_password = trim($_POST['conf_password']);

    // Vérification des champs
    if (empty($login) || empty($prenom) || empty($nom) || empty($password) || empty($conf_password)) {
        $erreur = "Tous les champs sont obligatoires.";
    } elseif (strlen($password) < 8) {
        $erreur = "Le mot de passe doit contenir au moins 8 caractères.";
    } elseif ($password !== $conf_password) {
        $erreur = "Les mots de passe ne correspondent pas.";
    } else {
        // Vérifier si le login existe déjà
        $check = $conn->prepare("SELECT id FROM utilisateurs WHERE login = ?");
        $check->bind_param("s", $login);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $erreur = "Ce login est déjà utilisé.";
        } else {
            // Hachage du mot de passe et insertion
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $insert = $conn->prepare("INSERT INTO utilisateurs (login, prenom, nom, password) VALUES (?, ?, ?, ?)");
            $insert->bind_param("ssss", $login, $prenom, $nom, $hash);
            $insert->execute();

            $_SESSION['message'] = "Inscription réussie. Connectez-vous maintenant.";
            header("Location: connexion.php");
            exit;
        }
    }
}
?>

<?php include 'header.php'; ?>

<h2>Inscription</h2>

<?php if (!empty($erreur)): ?>
    <p style="color:red;"><?php echo $erreur; ?></p>
<?php endif; ?>

<form method="post">
    <input type="text" name="login" placeholder="Login" value="<?php echo isset($login) ? $login : ''; ?>" required><br>
    <input type="text" name="prenom" placeholder="Prénom" value="<?php echo isset($prenom) ? $prenom : ''; ?>" required><br>
    <input type="text" name="nom" placeholder="Nom" value="<?php echo isset($nom) ? $nom : ''; ?>" required><br>
    <input type="password" name="password" placeholder="Mot de passe (min 8 caractères)" required><br>
    <input type="password" name="conf_password" placeholder="Confirmer le mot de passe" required><br>
    <button type="submit">S'inscrire</button>
</form>

<?php include 'footer.php'; ?>
