<?php
// Fichier pour ajouter un nouvel utilisateur et intervenant dans les bases de données bdd_users et bdd_storAix
// Fait par Nathan DAMASSE

require_once 'db_connect_users.php';
require_once 'db_connect_StorAix.php';

$data = json_decode(file_get_contents('php://input'), true);

$identifiant = $data['identifiant'];
$role = $data['role'];
$password = $data['password'];
$lastName = $data['lastName'];
$firstName = $data['firstName'];

try {
    $pdo_users->beginTransaction();
    $pdo->beginTransaction();

    $stmt = $pdo_users->prepare('INSERT INTO Users (Identifiant, RoleID, PasswordHash, PasswordPlain, LastName, FirstName) VALUES (?, ?, SHA2(?, 256), ?, ?, ?)');
    $stmt->execute([$identifiant, $role, $password, $password, $lastName, $firstName]);

    $stmtStorAix = $pdo->prepare('INSERT INTO Intervenant (Nom, Prenom) VALUES (?, ?)');
    $stmtStorAix->execute([$lastName, $firstName]);

    $pdo_users->commit();
    $pdo->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo_users->rollBack();
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
