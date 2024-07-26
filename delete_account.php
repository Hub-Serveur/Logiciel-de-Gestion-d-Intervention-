<?php
// Suppression d'un compte utilisateur et des données associées
// Fait par Nathan DAMASSE

require_once 'db_connect_users.php';
require_once 'db_connect_StorAix.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'], $data['lastName'], $data['firstName'])) {
    echo json_encode(['success' => false, 'error' => 'Paramètres manquants']);
    exit();
}

$userId = $data['id'];
$lastName = $data['lastName'];
$firstName = $data['firstName'];

try {
    $pdo_users->beginTransaction();

    // Suppression dans la base de données bdd_users
    $stmtUsers = $pdo_users->prepare("DELETE FROM Users WHERE UserID = ?");
    $stmtUsers->execute([$userId]);

    // Suppression dans la base de données bdd_StorAix si l'utilisateur est un intervenant
    $stmtIntervenant = $pdo->prepare("DELETE FROM Intervenant WHERE Nom = ? AND Prenom = ?");
    $stmtIntervenant->execute([$lastName, $firstName]);

    $pdo_users->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo_users->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
