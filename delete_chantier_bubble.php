<?php
// Suppression d'une bulle de chantier
// Fait par Nathan DAMASSE

include 'db_connect_StorAix.php';

header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['uniqueId'])) {
        $uniqueId = $data['uniqueId'];

        try {
            $pdo->beginTransaction();

            // Suppression de l'horaire du chantier
            $stmt = $pdo->prepare("DELETE FROM Horaire_Chantier WHERE Unique_Id = :uniqueId");
            $stmt->execute([':uniqueId' => $uniqueId]);

            $pdo->commit();
            echo json_encode(['success' => 'Bulle du chantier supprimée avec succès']);
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo json_encode(['error' => 'Erreur lors de la suppression de la bulle du chantier: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['error' => 'ID unique non fourni']);
    }
} else {
    echo json_encode(['error' => 'Requête invalide']);
}
?>
