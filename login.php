<?php
require_once __DIR__.'/includes/auth.php';require_once __DIR__.'/includes/schema.php';$schema=dental_schema($pdo);$labIdColumn=$schema->labId();
if(!empty($_SESSION['id_laboratoire'])) redirect('laboratoire/dashboard.php');$error='';$success=(string)($_SESSION['register_success']??'');unset($_SESSION['register_success']);if(isset($_GET['account_deleted']))$success='Votre compte laboratoire a été supprimé.';if($_SERVER['REQUEST_METHOD']==='POST'){csrf_check();$email=trim((string)($_POST['email']??''));$q=$pdo->prepare("SELECT `$labIdColumn` id,nom_laboratoire,mot_de_passe,statut FROM laboratoire WHERE email=?");$q->execute([$email]);$lab=$q->fetch();if($lab&&password_verify((string)($_POST['mot_de_passe']??''),$lab['mot_de_passe'])){if($schema->canonicalStatus($lab['statut'])==='valide'){session_regenerate_id(true);$_SESSION['id_laboratoire']=(int)$lab['id'];$_SESSION['nom_laboratoire']=$lab['nom_laboratoire'];redirect('laboratoire/dashboard.php');} $error=$schema->canonicalStatus($lab['statut'])==='en_attente'?'Votre inscription est en attente de validation.':'Votre inscription a été rejetée. Contactez l’administration.';}else{$error='Adresse e-mail ou mot de passe incorrect.';}}
?>
<?php $pageTitle='Connexion — DentalLink'; $pageStyles=['assets/css/login.css']; $bodyClass='login-page'; require __DIR__ . '/includes/header.php'; ?>
<main class="login-layout">
    <section class="login-visual" aria-labelledby="welcome-title">
        <a class="login-visual-brand" href="index.php" aria-label="DentalLink Morocco, accueil"><img src="assets/images/dental_link.png" alt="DentalLink Morocco"></a>
        <div class="login-visual-content">
            <p class="login-eyebrow">Votre réseau professionnel dentaire</p>
            <h1 id="welcome-title">Bienvenue sur DentalLink Morocco</h1>
            <p class="login-intro">Connectez-vous à votre espace professionnel et gérez votre laboratoire en toute simplicité.</p>
            <div class="login-benefits">
                <article class="login-benefit"><span class="login-benefit-mark" aria-hidden="true">01</span><div><h2>Profil professionnel</h2><p>Présentez votre laboratoire et vos services.</p></div></article>
                <article class="login-benefit"><span class="login-benefit-mark" aria-hidden="true">02</span><div><h2>Gestion des services</h2><p>Gérez vos services et vos réalisations en quelques clics.</p></div></article>
                <article class="login-benefit"><span class="login-benefit-mark" aria-hidden="true">03</span><div><h2>Visibilité au Maroc</h2><p>Développez votre réseau et touchez plus de professionnels.</p></div></article>
            </div>
        </div>
        <p class="login-visual-note">DentalLink Morocco <span>•</span> Le réseau des laboratoires dentaires</p>
    </section>
    <section class="login-panel" aria-labelledby="login-title">
        <div class="login-form-wrap">
            <a class="login-panel-brand" href="index.php" aria-label="DentalLink Morocco, accueil"><img src="assets/images/dental_link.png" alt="DentalLink Morocco"></a>
            <p class="login-panel-kicker">Espace laboratoire</p>
            <h1 id="login-title">Connexion laboratoire</h1>
            <p class="login-panel-subtitle">Accédez à votre espace professionnel</p>
            <?php if($success!==''):?><p class="login-notice" role="status"><?=e($success)?></p><?php elseif(isset($_GET['registered'])):?><p class="login-notice" role="status">Inscription reçue. Connectez-vous après validation administrative.</p><?php endif;?>
            <?php if($error!==''):?><p class="login-error" role="alert"><?=e($error)?></p><?php endif;?>
            <form method="post" class="login-form"><?=csrf_field()?>
                <label for="email">E-mail</label><input id="email" type="email" name="email" autocomplete="email" required>
                <label for="mot_de_passe">Mot de passe</label><input id="mot_de_passe" type="password" name="mot_de_passe" autocomplete="current-password" required>
                <button type="submit">Se connecter</button>
            </form>
            <p class="login-register">Vous n’avez pas encore de compte ? <a href="register.php">Créer un compte</a></p>
        </div>
        <p class="login-panel-footer">© <?=date('Y')?> DentalLink Morocco</p>
    </section>
</main>
</body>
</html>