<?php
// Auteur: Nathan DAMASSE
// Ce fichier récupère tous les clients de la base de données et renvoie les données au format JSON.

require_once 'db_connect_StorAix.php';

header('Content-Type: application/json');

try {
    // Préparer et exécuter la requête pour obtenir tous les clients
    $stmt = $pdo->query('SELECT * FROM Client');
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Envoyer les résultats en JSON
    echo json_encode($clients);
} catch (PDOException $e) {
    // En cas d'erreur, envoyer un message d'erreur en JSON
    echo json_encode(['error' => 'Erreur lors de la récupération des clients: ' . $e->getMessage()]);
}
?>
