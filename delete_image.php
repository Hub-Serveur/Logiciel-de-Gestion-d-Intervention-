<?php
// Suppression d'une image de chantier
// Fait par Nathan DAMASSE

include 'db_connect_StorAix.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['imageUrl']) && isset($_POST['idChantier'])) {
    $imageUrl = $_POST['imageUrl'];
    $idChantier = $_POST['idChantier'];

    // Supprimer l'image du répertoire
    if (file_exists($imageUrl)) {
        unlink($imageUrl);
    }

    // Supprimer l'entrée de l'image de la base de données
    $stmt = $pdo->prepare("DELETE FROM Images_Chantier WHERE ID_Chantier = :idChantier AND Image_Path = :imageUrl");
    $stmt->execute([':idChantier' => $idChantier, ':imageUrl' => $imageUrl]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Image not found in database']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>
