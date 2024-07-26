<?php
require_once 'db_connect_StorAix.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

$teamId = $data['ID_Equipe'];
$teamName = $data['Nom_Equipe'];
$teamColor = $data['Couleur'];
$intervenants = $data['Intervenants'];

try {
    // Mise à jour de l'équipe
    $stmt = $pdo->prepare('UPDATE Equipe SET Nom_Equipe = ?, Couleur = ? WHERE ID_Equipe = ?');
    $stmt->execute([$teamName, $teamColor, $teamId]);

    // Suppression des anciens intervenants de l'équipe
    $stmt = $pdo->prepare('DELETE FROM Intervenant_Equipe WHERE ID_Equipe = ?');
    $stmt->execute([$teamId]);

    // Ajout des nouveaux intervenants à l'équipe
    $stmt = $pdo->prepare('INSERT INTO Intervenant_Equipe (ID_Equipe, ID_Intervenant) VALUES (?, ?)');
    foreach ($intervenants as $intervenantId) {
        $stmt->execute([$teamId, $intervenantId]);
    }

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()]);
}
?>
