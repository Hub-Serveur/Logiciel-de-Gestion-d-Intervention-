<?php
// Auteur: Nathan DAMASSE
// Ce fichier récupère la liste des équipes à partir de la base de données et renvoie les données au format JSON.

include 'db_connect_StorAix.php'; // Inclusion du fichier de connexion à la base de données

header('Content-Type: application/json'); // S'assurer que la réponse est en JSON

try {
    // Récupération des équipes
    $stmt = $pdo->query("SELECT ID_Equipe, Nom_Equipe FROM Equipe");
    $equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Envoi des résultats en JSON
    echo json_encode(['equipes' => $equipes]);
} catch (PDOException $e) {
    // En cas d'erreur, envoi d'un message d'erreur en JSON
    echo json_encode(['error' => 'Erreur lors de la récupération des équipes: ' . $e->getMessage()]);
}
?>
