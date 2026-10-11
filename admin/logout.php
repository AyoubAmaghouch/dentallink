<?php
require_once __DIR__.'/../includes/auth.php';unset($_SESSION['id_admin'],$_SESSION['admin_email']);session_regenerate_id(true);header('Location: login.php');exit;
