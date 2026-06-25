<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "laboratoire_dentaire";

// Connexion à la base de données
$conn = mysqli_connect($host, $username, $password, $database);

// Vérifier la connexion
if (!$conn) {
    die("Erreur de connexion : " . mysqli_connect_error());
}

// Définir l'encodage UTF-8
mysqli_set_charset($conn, "utf8");

?>