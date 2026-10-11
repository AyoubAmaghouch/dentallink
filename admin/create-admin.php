<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/../config/database.php';
$email = $argv[1] ?? '';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { fwrite(STDERR, "Usage: php admin/create-admin.php admin@example.com\n"); exit(2); }
fwrite(STDOUT, 'Mot de passe (10 caractères minimum) : ');
$password = trim((string) fgets(STDIN));
if (strlen($password) < 10) { fwrite(STDERR, "Mot de passe trop court.\n"); exit(2); }
try { $name = explode('@', $email, 2)[0]; $q = $pdo->prepare('INSERT INTO admin(nom,email,mot_de_passe) VALUES(?,?,?)'); $q->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]); fwrite(STDOUT, "Compte administrateur créé.\n"); }
catch (PDOException $e) { fwrite(STDERR, "Création impossible : table absente, adresse déjà utilisée ou connexion MySQL indisponible.\n"); exit(1); }
