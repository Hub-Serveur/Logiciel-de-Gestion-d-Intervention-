<?php
// Fichier pour ajouter une nouvelle équipe avec ses intervenants dans la base de données bdd_storAix
// Fait par Nathan DAMASSE

require_once 'db_connect_StorAix.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['error' => 'JSON invalide']);
    exit;
}

$teamName = $data['Nom_Equipe'];
$teamColor = $data['Couleur'];
$intervenants = $data['Intervenants'];

try {
    // Insertion de la nouvelle équipe
    $stmt = $pdo->prepare('INSERT INTO Equipe (Nom_Equipe, Couleur) VALUES (?, ?)');
    $stmt->execute([$teamName, $teamColor]);

    // Récupération de l'ID de l'équipe insérée
    $teamId = $pdo->lastInsertId();

    // Ajout des intervenants à l'équipe
    $stmt = $pdo->prepare('INSERT INTO Intervenant_Equipe (ID_Equipe, ID_Intervenant) VALUES (?, ?)');
    foreach ($intervenants as $intervenantId) {
        $stmt->execute([$teamId, $intervenantId]);
    }

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erreur lors de l\'ajout: ' . $e->getMessage()]);
}
?>
