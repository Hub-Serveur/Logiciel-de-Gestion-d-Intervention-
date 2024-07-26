<?php
include 'db_connect_StorAix.php';

header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $required_fields = [
        'idChantier', 'idHoraire', 'heureDebut', 'heureFin', 'clientNom', 'clientPrenom',
        'clientEmail', 'clientTelephoneMobile', 'clientTelephoneFixe', 'noteInterventionClient',
        'noteInterventionEquipe', 'description', 'adresse', 'ville', 'codePostal', 'nombreColis',
        'statutIntervention'
    ];

    foreach ($required_fields as $field) {
        if (!isset($_POST[$field])) {
            echo json_encode(['error' => 'Champ manquant : ' . $field]);
            exit;
        }
    }

    $idChantier = $_POST['idChantier'];
    $idHoraire = $_POST['idHoraire'];
    $heureDebut = $_POST['heureDebut'];
    $heureFin = $_POST['heureFin'];
    $clientNom = $_POST['clientNom'];
    $clientPrenom = $_POST['clientPrenom'];
    $clientEmail = $_POST['clientEmail'];
    $clientTelephoneMobile = $_POST['clientTelephoneMobile'];
    $clientTelephoneFixe = $_POST['clientTelephoneFixe'];
    $noteInterventionClient = $_POST['noteInterventionClient'];
    $noteInterventionEquipe = $_POST['noteInterventionEquipe'];
    $description = $_POST['description'];
    $adresse = $_POST['adresse'];
    $ville = $_POST['ville'];
    $codePostal = $_POST['codePostal'];
    $nombreColis = $_POST['nombreColis'];
    $statutIntervention = $_POST['statutIntervention'];
    $locataireNom = isset($_POST['locataireNom']) ? $_POST['locataireNom'] : null;
    $locatairePrenom = isset($_POST['locatairePrenom']) ? $_POST['locatairePrenom'] : null;
    $locataireTelephoneMobile = isset($_POST['locataireTelephoneMobile']) ? $_POST['locataireTelephoneMobile'] : null;
    $locataireTelephoneFixe = isset($_POST['locataireTelephoneFixe']) ? $_POST['locataireTelephoneFixe'] : null;

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("
            UPDATE Chantier
            SET Description = :description, Adresse = :adresse, Ville = :ville, CodePostal = :codePostal,
                NombreColis = :nombreColis, NoteInterventionClient = :noteInterventionClient,
                NoteInterventionEquipe = :noteInterventionEquipe, StatutIntervention = :statutIntervention,
                Titre = :titre
            WHERE ID_Chantier = :idChantier
        ");
        
        $stmt->execute([
            ':description' => $description,
            ':adresse' => $adresse,
            ':ville' => $ville,
            ':codePostal' => $codePostal,
            ':nombreColis' => $nombreColis,
            ':noteInterventionClient' => $noteInterventionClient,
            ':noteInterventionEquipe' => $noteInterventionEquipe,
            ':statutIntervention' => $statutIntervention,
            ':titre' => $_POST['titre'],
            ':idChantier' => $idChantier
        ]);

        $stmt = $pdo->prepare("
            UPDATE Client
            SET Nom = :clientNom, Prenom = :clientPrenom, Email = :clientEmail,
                TelephoneMobile = :clientTelephoneMobile, TelephoneFixe = :clientTelephoneFixe
            WHERE ID_Client = (SELECT ID_Client FROM Chantier WHERE ID_Chantier = :idChantier)
        ");

        $stmt->execute([
            ':clientNom' => $clientNom,
            ':clientPrenom' => $clientPrenom,
            ':clientEmail' => $clientEmail,
            ':clientTelephoneMobile' => $clientTelephoneMobile,
            ':clientTelephoneFixe' => $clientTelephoneFixe,
            ':idChantier' => $idChantier
        ]);

        if ($locataireNom && $locatairePrenom) {
            $stmt = $pdo->prepare("
                UPDATE Locataire
                SET Nom = :locataireNom, Prenom = :locatairePrenom, TelephoneMobile = :locataireTelephoneMobile,
                    TelephoneFixe = :locataireTelephoneFixe
                WHERE ID_Client = (SELECT ID_Client FROM Chantier WHERE ID_Chantier = :idChantier)
            ");

            $stmt->execute([
                ':locataireNom' => $locataireNom,
                ':locatairePrenom' => $locatairePrenom,
                ':locataireTelephoneMobile' => $locataireTelephoneMobile,
                ':locataireTelephoneFixe' => $locataireTelephoneFixe,
                ':idChantier' => $idChantier
            ]);
        }

        $stmt = $pdo->prepare("
            UPDATE Horaire_Chantier
            SET Heure_Debut = :heureDebut, Heure_Fin = :heureFin
            WHERE Unique_Id = :idHoraire
        ");
        $stmt->execute([
            ':heureDebut' => $heureDebut,
            ':heureFin' => $heureFin,
            ':idHoraire' => $idHoraire
        ]);

        $pdo->commit();

        echo json_encode(['success' => 'Chantier mis à jour avec succès']);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['error' => 'Erreur lors de la mise à jour du chantier: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Données invalides ou méthode non autorisée']);
}
?>
