<?php
// Auteur: Nathan DAMASSE
// Ce fichier recherche des clients dans la base de données en fonction d'un critère de recherche fourni.

include 'db_connect_StorAix.php';

header('Content-Type: application/json');

if (isset($_GET['query'])) {
    $query = $_GET['query'] . '%';

    try {
        // Préparer la requête pour rechercher les clients par nom
        $stmt = $pdo->prepare("
            SELECT ID_Client, Nom, Prenom, Email, TelephoneMobile, TelephoneFixe
            FROM Client
            WHERE Nom LIKE :query
            LIMIT 10
        ");
        $stmt->execute([':query' => $query]);
        $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Envoyer les résultats de la recherche en JSON
        echo json_encode($clients);
    } catch (PDOException $e) {
        // En cas d'erreur, envoyer un message d'erreur en JSON
        echo json_encode(['error' => 'Erreur lors de la récupération des clients: ' . $e->getMessage()]);
    }
} else {
    // Si aucun critère de recherche n'est fourni, envoyer un message d'erreur en JSON
    echo json_encode(['error' => 'Aucun critère de recherche fourni']);
}
?>
