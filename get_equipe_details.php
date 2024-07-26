<?php
// Auteur: Nathan DAMASSE
// Ce fichier récupère les détails d'une équipe et ses intervenants à partir de la base de données et renvoie les données au format JSON.

include 'db_connect_StorAix.php'; // Inclusion du fichier de connexion à la base de données

header('Content-Type: application/json'); // S'assurer que la réponse est en JSON

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['nom'])) {
    $nomEquipe = $_GET['nom'];

    try {
        // Récupération des détails de l'équipe
        $stmt = $pdo->prepare("SELECT * FROM Equipe WHERE Nom_Equipe = :nom");
        $stmt->execute([':nom' => $nomEquipe]);
        $equipe = $stmt->fetch(PDO::FETCH_ASSOC);

        // Récupération des intervenants de l'équipe
        $stmt = $pdo->prepare("
            SELECT i.Nom, i.Prenom
            FROM Intervenant i
            JOIN Intervenant_Equipe ie ON i.ID_Intervenant = ie.ID_Intervenant
            WHERE ie.ID_Equipe = :idEquipe
        ");
        $stmt->execute([':idEquipe' => $equipe['ID_Equipe']]);
        $intervenants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Envoi des résultats en JSON
        echo json_encode([
            'EquipeTelephone' => $equipe['Telephone'],
            'intervenants' => $intervenants
        ]);
    } catch (PDOException $e) {
        // En cas d'erreur, envoi d'un message d'erreur en JSON
        echo json_encode(['error' => 'Erreur lors de la récupération des détails de l\'équipe: ' . $e->getMessage()]);
    }
} else {
    // En cas de données invalides, envoi d'un message d'erreur en JSON
    echo json_encode(['error' => 'Données invalides']);
}
?>
