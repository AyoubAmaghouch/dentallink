<?php
require_once __DIR__.'/../includes/auth.php';
if(!empty($_SESSION['id_admin'])) redirect('dashboard.php');
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    csrf_check();$email=trim((string)($_POST['email']??''));
    $q=$pdo->prepare('SELECT id_admin AS id,email,mot_de_passe FROM admin WHERE email=? LIMIT 1');$q->execute([$email]);$admin=$q->fetch();
    if($admin&&password_verify((string)($_POST['mot_de_passe']??''),$admin['mot_de_passe'])){session_regenerate_id(true);$_SESSION['id_admin']=(int)$admin['id'];$_SESSION['admin_email']=$admin['email'];redirect('dashboard.php');}
    $error='Identifiants administrateur incorrects.';
}
$pageTitle='Administration — Connexion';$assetPrefix='../';$pageStyles=['assets/css/admin.css'];require __DIR__.'/../includes/header.php';
?>
<main class="admin-main login-admin"><section class="admin-panel"><p class="admin-eyebrow">Espace sécurisé</p><h1>Administration DentalLink</h1><?php if($error):?><div class="admin-alert error" role="alert"><?=e($error)?></div><?php endif;?><form method="post" class="admin-form"><?=csrf_field()?><label>E-mail<input type="email" name="email" autocomplete="username" required></label><label>Mot de passe<input type="password" name="mot_de_passe" autocomplete="current-password" required></label><button class="admin-button admin-primary">Connexion</button></form></section></main></body></html>
