<?php
// Auteur: Nathan DAMASSE
// Ce fichier recherche les locataires dont le nom correspond à la requête et renvoie les résultats au format JSON.

include 'db_connect_StorAix.php'; // Inclusion du fichier de connexion à la base de données

$query = $_GET['query']; // Récupération de la requête de recherche

// Préparation et exécution de la requête de recherche
$stmt = $pdo->prepare("SELECT Nom, Prenom, TelephoneMobile, TelephoneFixe FROM Locataire WHERE Nom LIKE :query LIMIT 10");
$stmt->execute(['query' => "%$query%"]);
$locataires = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Envoi des résultats en JSON
echo json_encode($locataires);
?>
