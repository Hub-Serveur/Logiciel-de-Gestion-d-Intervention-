<?php
// Auteur: Nathan DAMASSE
// Ce fichier récupère tous les comptes utilisateurs et leurs rôles.

require_once 'db_connect_users.php';

header('Content-Type: application/json');

try {
    // Préparation de la requête pour récupérer les comptes utilisateurs et leurs rôles
    $stmt = $pdo_users->prepare('SELECT u.UserID, u.FirstName, u.LastName, u.Identifiant, u.PasswordPlain, r.RoleName, r.RoleID 
                                 FROM Users u 
                                 JOIN Roles r ON u.RoleID = r.RoleID');
    $stmt->execute();
    $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Envoi des résultats sous forme de JSON
    echo json_encode($accounts);
} catch (PDOException $e) {
    // En cas d'erreur, renvoi d'un message d'erreur en JSON
    echo json_encode(['error' => 'Erreur lors de la récupération des comptes: ' . $e->getMessage()]);
}
?>
