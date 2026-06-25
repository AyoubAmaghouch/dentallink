<?php
session_start();

if (isset($_SESSION['id_laboratoire'])) {
    header("Location: laboratoire/dashboard.php");
    exit();
}

require_once 'config/database.php';

$erreur = "";

if (isset($_POST['connexion'])) {

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];

    $sql = "SELECT * FROM laboratoire WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {

        $laboratoire = mysqli_fetch_assoc($result);

        if (password_verify($mot_de_passe, $laboratoire['mot_de_passe'])) {

            if ($laboratoire['statut'] == 'accepte') {

                $_SESSION['id_laboratoire'] = $laboratoire['id_laboratoire'];
                $_SESSION['nom_laboratoire'] = $laboratoire['nom_laboratoire'];

                header("Location: laboratoire/dashboard.php");
                exit();

            } elseif ($laboratoire['statut'] == 'en_attente') {

                $erreur = "Votre compte est en attente de validation.";

            } else {

                $erreur = "Votre compte a été refusé.";

            }

        } else {

            $erreur = "Mot de passe incorrect.";

        }

    } else {

        $erreur = "Adresse email introuvable.";

    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>

<h2>Connexion</h2>

<?php if (!empty($erreur)) { ?>
    <p style="color:red;"><?= $erreur; ?></p>
<?php } ?>

<form method="POST">

    <label>Email</label>
    <input type="email" name="email" required>

    <label>Mot de passe</label>
    <input type="password" name="mot_de_passe" required>

    <button type="submit" name="connexion">Se connecter</button>

</form>

<p>
    Vous n'avez pas de compte ?
    <a href="register.php">S'inscrire</a>
</p>

</body>
</html>