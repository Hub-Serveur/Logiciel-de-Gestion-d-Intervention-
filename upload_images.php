<?php
include 'db_connect_StorAix.php'; // Assurez-vous que ce fichier contient les paramètres de connexion à la base de données

// Vérifier si le formulaire a été soumis et si le fichier a bien été téléchargé
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file']) && isset($_POST['idChantier'])) {
    $idChantier = $_POST['idChantier'];
    $file = $_FILES['file'];
    $uploadDir = 'uploads/';

    // Créer le répertoire s'il n'existe pas
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Obtenir le nom du fichier et le chemin où il sera sauvegardé
    $fileName = basename($file['name']);
    $filePath = $uploadDir . $fileName;

    // Déplacer le fichier téléchargé vers son nouvel emplacement
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        // Vérifiez d'abord si le fichier existe déjà pour cet idChantier
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM Images_Chantier WHERE ID_Chantier = :idChantier AND Image_Path = :filePath");
        $stmtCheck->execute([':idChantier' => $idChantier, ':filePath' => $filePath]);
        $count = $stmtCheck->fetchColumn();

        if ($count == 0) {
            // Préparer et exécuter la requête SQL pour insérer les données dans la base
            $stmt = $pdo->prepare("INSERT INTO Images_Chantier (ID_Chantier, Image_Path) VALUES (:idChantier, :filePath)");
            $stmt->execute([':idChantier' => $idChantier, ':filePath' => $filePath]);
            echo json_encode(['success' => true, 'filePath' => $filePath]);
        } else {
            echo json_encode(['success' => false, 'error' => 'File already exists']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'File upload failed']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request, file or ID_Chantier missing']);
}
?>
