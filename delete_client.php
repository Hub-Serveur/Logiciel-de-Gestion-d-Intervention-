<?php
// Suppression d'un client dans la base de données
// Fait par Nathan DAMASSE

require_once 'db_connect_StorAix.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

$clientId = $data['ID_Client'];

try {
    $stmt = $pdo->prepare('DELETE FROM Client WHERE ID_Client = ?');
    $stmt->execute([$clientId]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erreur lors de la suppression: ' . $e->getMessage()]);
}
?>
