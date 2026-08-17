<?php
require_once __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.html");
    exit();
}

$nom     = trim($_POST['nom'] ?? '');
$email   = trim($_POST['email'] ?? '');
$sujet   = trim($_POST['sujet'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($nom === '' || $email === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Merci de remplir correctement le formulaire (nom, email valide et message).");
}

$database = new Database();
$db = $database->getConnection();

$stmt = $db->prepare(
    "INSERT INTO messages (nom, email, sujet, message) VALUES (:nom, :email, :sujet, :message)"
);
$stmt->execute([
    ':nom'     => $nom,
    ':email'   => $email,
    ':sujet'   => $sujet,
    ':message' => $message,
]);

header("Location: essai.html?envoye=1");
exit();
