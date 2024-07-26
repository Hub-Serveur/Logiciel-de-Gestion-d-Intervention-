<?php
require_once 'db_connect_users.php';
require_once 'db_connect_StorAix.php';

$data = json_decode(file_get_contents('php://input'), true);

$accountId = $data['id'];
$identifiant = $data['identifiant'];
$role = $data['role'];
$password = $data['password'];
$lastName = $data['lastName'];
$firstName = $data['firstName'];

try {
    // Begin a transaction
    $pdo_users->beginTransaction();
    $pdo->beginTransaction();

    if ($accountId) {
        // Update the user in the Users table in bdd_users
        $stmt = $pdo_users->prepare('UPDATE Users SET Identifiant = ?, RoleID = ?, PasswordHash = SHA2(?, 256), PasswordPlain = ?, LastName = ?, FirstName = ? WHERE UserID = ?');
        $stmt->execute([$identifiant, $role, $password, $password, $lastName, $firstName, $accountId]);

        // Update the intervenant in the Intervenant table in bdd_storAix
        $stmtStorAix = $pdo->prepare('UPDATE Intervenant SET Nom = ?, Prenom = ? WHERE Nom = ? AND Prenom = ?');
        $stmtStorAix->execute([$lastName, $firstName, $lastName, $firstName]);
    } else {
        // Insert a new user in the Users table in bdd_users
        $stmt = $pdo_users->prepare('INSERT INTO Users (Identifiant, RoleID, PasswordHash, PasswordPlain, LastName, FirstName) VALUES (?, ?, SHA2(?, 256), ?, ?, ?)');
        $stmt->execute([$identifiant, $role, $password, $password, $lastName, $firstName]);

        // Get the newly inserted user's ID
        $newUserId = $pdo_users->lastInsertId();

        // Insert a new intervenant in the Intervenant table in bdd_storAix
        $stmtStorAix = $pdo->prepare('INSERT INTO Intervenant (Nom, Prenom) VALUES (?, ?)');
        $stmtStorAix->execute([$lastName, $firstName]);
    }

    // Commit the transaction
    $pdo_users->commit();
    $pdo->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // Rollback the transaction if something failed
    $pdo_users->rollBack();
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
