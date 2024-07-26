<?php
include 'db_connect_StorAix.php';

$query = $_GET['query'] ?? '';
error_log("Requête de recherche reçue: " . $query);  // Log la requête pour le diagnostic

$results = [];
if (!empty($query)) {
    $sql = "SELECT DISTINCT chantier.ID_Chantier as idChantier, chantier.Titre as chantier, equipe.Nom_Equipe as teamName, equipe.Couleur as teamColor, client.Nom as clientName, client.Prenom as clientPrenom,
            (SELECT intervention.Unique_Id FROM Horaire_Chantier intervention WHERE intervention.ID_Chantier = chantier.ID_Chantier ORDER BY intervention.Heure_Debut DESC LIMIT 1) as uniqueId,
            (SELECT intervention.Date_Travail FROM Horaire_Chantier intervention WHERE intervention.ID_Chantier = chantier.ID_Chantier ORDER BY intervention.Heure_Debut DESC LIMIT 1) as dateChantier,
            (SELECT intervention.Heure_Debut FROM Horaire_Chantier intervention WHERE intervention.ID_Chantier = chantier.ID_Chantier ORDER BY intervention.Heure_Debut DESC LIMIT 1) as heureDebut,
            (SELECT intervention.Heure_Fin FROM Horaire_Chantier intervention WHERE intervention.ID_Chantier = chantier.ID_Chantier ORDER BY intervention.Heure_Debut DESC LIMIT 1) as heureFin
            FROM Chantier chantier
            JOIN Equipe equipe ON chantier.ID_Equipe = equipe.ID_Equipe
            JOIN Client client ON chantier.ID_Client = client.ID_Client
            WHERE chantier.Titre LIKE ? OR client.Nom LIKE ? OR client.Prenom LIKE ? OR equipe.Nom_Equipe LIKE ?";
    $stmt = $pdo->prepare($sql);
    $searchTerm = '%' . $query . '%';
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $results[] = $row;
    }
    error_log("Nombre de résultats trouvés: " . count($results));  // Log le nombre de résultats pour le diagnostic
}

echo json_encode($results);
?>
