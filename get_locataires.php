<?php
// Auteur: Nathan DAMASSE
// Récupère les locataires de la base de données et renvoie les résultats en JSON.

header('Content-Type: application/json');

require_once 'db_connect_StorAix.php';

try {
    $stmt = $pdo->query("SELECT ID_Locataire, Nom, Prenom, TelephoneMobile, TelephoneFixe FROM Locataire");
    $locataires = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($locataires);
} catch (Exception $e) {
    error_log('Erreur: ' . $e->getMessage());
    echo json_encode(['error' => 'Erreur lors de la récupération des locataires: ' . $e->getMessage()]);
}
?>
