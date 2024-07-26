<?php
// Suppression d'une équipe et de ses intervenants
// Fait par Nathan DAMASSE

require_once 'db_connect_StorAix.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['error' => 'Données JSON invalides']);
    exit;
}

$teamId = $data['ID_Equipe'];

try {
    // Supprimer les intervenants de l'équipe
    $stmt = $pdo->prepare('DELETE FROM Intervenant_Equipe WHERE ID_Equipe = ?');
    $stmt->execute([$teamId]);

    // Supprimer l'équipe
    $stmt = $pdo->prepare('DELETE FROM Equipe WHERE ID_Equipe = ?');
    $stmt->execute([$teamId]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erreur lors de la suppression: ' . $e->getMessage()]);
}
?>
