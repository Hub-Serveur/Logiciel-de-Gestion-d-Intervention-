<?php
// Auteur: Nathan DAMASSE
// Récupère les équipes et leurs intervenants de la base de données et renvoie les résultats en JSON.

require_once 'db_connect_StorAix.php';
header('Content-Type: application/json');

try {
    $stmt = $pdo->query('
        SELECT e.ID_Equipe, e.Nom_Equipe, e.Couleur, i.Nom AS IntervenantNom, i.Prenom AS IntervenantPrenom
        FROM Equipe e
        LEFT JOIN Intervenant_Equipe ie ON e.ID_Equipe = ie.ID_Equipe
        LEFT JOIN Intervenant i ON ie.ID_Intervenant = i.ID_Intervenant
    ');

    $teams = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $teamId = $row['ID_Equipe'];
        if (!isset($teams[$teamId])) {
            $teams[$teamId] = [
                'ID_Equipe' => $teamId,
                'Nom_Equipe' => $row['Nom_Equipe'],
                'Couleur' => $row['Couleur'],
                'Intervenants' => []
            ];
        }
        if ($row['IntervenantNom'] && $row['IntervenantPrenom']) {
            $teams[$teamId]['Intervenants'][] = $row['IntervenantPrenom'] . ' ' . $row['IntervenantNom'];
        }
    }

    echo json_encode(array_values($teams));
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erreur lors de la récupération des équipes: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['error' => 'Erreur inattendue: ' . $e->getMessage()]);
}
?>
