<?php
// Fichier pour l'authentification des utilisateurs
// Fait par Nathan DAMASSE

session_start();
require 'db_connect_users.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identifiant = $_POST['identifiant'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($identifiant) && !empty($password)) {
        $stmt = $pdo_users->prepare("SELECT UserID, PasswordHash, RoleID FROM Users WHERE Identifiant = :identifiant");
        $stmt->execute(['identifiant' => $identifiant]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Vérifie si l'utilisateur existe et si le mot de passe est correct
        if ($user && $user['PasswordHash'] === hash('sha256', $password)) {
            $_SESSION['user_id'] = $user['UserID'];
            $_SESSION['role_id'] = $user['RoleID'];
            header("Location: index.php");
            exit;
        } else {
            $_SESSION['error'] = "Identifiant ou mot de passe incorrect.";
            header("Location: login.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Veuillez remplir tous les champs.";
        header("Location: login.php");
        exit;
    }
} else {
    header("Location: login.php");
    exit;
}
?>
