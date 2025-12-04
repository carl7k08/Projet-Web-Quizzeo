<?php

$host = 'localhost';
$dbname = 'quizzeo_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion SQL : " . $e->getMessage());
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

define('RECAPTCHA_SITE_KEY', '6LfgLiEsAAAAABk5jpBum8hlhidwpwlXtBEZC4ZW'); 
define('RECAPTCHA_SECRET_KEY', '6LfgLiEsAAAAAKZ4Sl5zfknHThq_jheFWS-jP6xC');
?>