<?php
// Auteur: Nathan DAMASSE
// Ce fichier récupère les détails d'un chantier, les intervenants, les images et les équipes associées à un chantier donné.

include 'db_connect_StorAix.php';

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_GET['id']) && isset($_GET['uniqueId'])) {
    $idChantier = $_GET['id'];
    $uniqueId = $_GET['uniqueId'];

    try {
        // Préparer la requête pour récupérer les détails du chantier
        $stmt = $pdo->prepare("
            SELECT 
                c.*, 
                e.Nom_Equipe, 
                e.Telephone AS EquipeTelephone, 
                l.Nom AS LocataireNom, 
                l.Prenom AS LocatairePrenom, 
                l.TelephoneFixe AS LocataireTelephoneFixe, 
                l.TelephoneMobile AS LocataireTelephoneMobile, 
                h.Date_Travail,
                h.Heure_Debut, 
                h.Heure_Fin, 
                cl.Nom AS ClientNom, 
                cl.Prenom AS ClientPrenom, 
                cl.Email AS ClientEmail, 
                cl.TelephoneFixe AS ClientTelephoneFixe, 
                cl.TelephoneMobile AS ClientTelephoneMobile
            FROM Chantier c
            LEFT JOIN Equipe e ON c.ID_Equipe = e.ID_Equipe
            LEFT JOIN Locataire l ON c.ID_Client = l.ID_Client
            LEFT JOIN Horaire_Chantier h ON c.ID_Chantier = h.ID_Chantier
            LEFT JOIN Client cl ON c.ID_Client = cl.ID_Client
            WHERE c.ID_Chantier = :idChantier AND h.Unique_Id = :uniqueId
        ");
        $stmt->execute([':idChantier' => $idChantier, ':uniqueId' => $uniqueId]);
        $chantier = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($chantier) {
            // Journalisation des données du chantier
            error_log('Données du chantier: ' . print_r($chantier, true));

            // Préparer la requête pour récupérer les intervenants
            $stmt = $pdo->prepare("
                SELECT i.Nom, i.Prenom
                FROM Intervenant i
                JOIN Intervenant_Equipe ie ON i.ID_Intervenant = ie.ID_Intervenant
                WHERE ie.ID_Equipe = (SELECT ID_Equipe FROM Chantier WHERE ID_Chantier = :idChantier)
            ");
            $stmt->execute([':idChantier' => $idChantier]);
            $intervenants = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Journalisation des intervenants
            error_log('Intervenants: ' . print_r($intervenants, true));

            // Préparer la requête pour récupérer les images
            $stmt = $pdo->prepare("
                SELECT Image_Path
                FROM Images_Chantier
                WHERE ID_Chantier = :idChantier
            ");
            $stmt->execute([':idChantier' => $idChantier]);
            $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Journalisation des images
            error_log('Images: ' . print_r($images, true));

            // Préparer la requête pour récupérer les équipes
            $stmt = $pdo->prepare("SELECT Nom_Equipe FROM Equipe");
            $stmt->execute();
            $equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Journalisation des équipes
            error_log('Équipes: ' . print_r($equipes, true));

            // Envoi de la réponse en JSON
            echo json_encode([
                'chantier' => $chantier,
                'intervenants' => $intervenants,
                'images' => $images,
                'equipes' => $equipes
            ]);
        } else {
            echo json_encode(['error' => 'Chantier non trouvé']);
        }
    } catch (PDOException $e) {
        // Gestion des erreurs PDO et envoi d'une réponse en JSON
        error_log('Erreur PDO: ' . $e->getMessage());
        echo json_encode(['error' => 'Erreur lors de la récupération des détails du chantier: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'ID du chantier ou ID unique non fourni']);
}
?>
