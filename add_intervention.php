<?php
// Fichier pour ajouter une nouvelle intervention, client et locataire dans la base de données bdd_storAix
// Fait par Nathan DAMASSE

include 'db_connect_StorAix.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = !empty($_POST['titre']) ? $_POST['titre'] : null;
    $description = !empty($_POST['description']) ? $_POST['description'] : null;
    $date = !empty($_POST['date']) ? $_POST['date'] : null;
    $heureDebut = !empty($_POST['heureDebut']) ? $_POST['heureDebut'] : null;
    $heureFin = !empty($_POST['heureFin']) ? $_POST['heureFin'] : null;
    $equipe = !empty($_POST['equipe']) ? $_POST['equipe'] : null;
    $adresse = !empty($_POST['adresse']) ? $_POST['adresse'] : null;
    $ville = !empty($_POST['ville']) ? $_POST['ville'] : null;
    $codePostal = !empty($_POST['codePostal']) ? $_POST['codePostal'] : null;
    $nombreColis = !empty($_POST['nombreColis']) ? $_POST['nombreColis'] : null;
    $statutIntervention = !empty($_POST['statutIntervention']) ? $_POST['statutIntervention'] : null;
    $clientNom = !empty($_POST['clientNom']) ? $_POST['clientNom'] : null;
    $clientPrenom = !empty($_POST['clientPrenom']) ? $_POST['clientPrenom'] : null;
    $clientEmail = !empty($_POST['clientEmail']) ? $_POST['clientEmail'] : null;
    $clientTelephoneMobile = !empty($_POST['clientTelephoneMobile']) ? $_POST['clientTelephoneMobile'] : null;
    $clientTelephoneFixe = !empty($_POST['clientTelephoneFixe']) ? $_POST['clientTelephoneFixe'] : null;
    $locataireNom = !empty($_POST['locataireNom']) ? $_POST['locataireNom'] : null;
    $locatairePrenom = !empty($_POST['locatairePrenom']) ? $_POST['locatairePrenom'] : null;
    $locataireTelephoneMobile = !empty($_POST['locataireTelephoneMobile']) ? $_POST['locataireTelephoneMobile'] : null;
    $locataireTelephoneFixe = !empty($_POST['locataireTelephoneFixe']) ? $_POST['locataireTelephoneFixe'] : null;
    $noteInterventionClient = !empty($_POST['noteInterventionClient']) ? $_POST['noteInterventionClient'] : null;
    $noteInterventionEquipe = !empty($_POST['noteInterventionEquipe']) ? $_POST['noteInterventionEquipe'] : null;

    try {
        $pdo->beginTransaction();

        // Insertion du client
        $stmt = $pdo->prepare("
            INSERT INTO Client (Nom, Prenom, Email, TelephoneMobile, TelephoneFixe)
            VALUES (:nom, :prenom, :email, :telephoneMobile, :telephoneFixe)
        ");
        $stmt->execute([
            ':nom' => $clientNom,
            ':prenom' => $clientPrenom,
            ':email' => $clientEmail,
            ':telephoneMobile' => $clientTelephoneMobile,
            ':telephoneFixe' => $clientTelephoneFixe
        ]);

        $clientId = $pdo->lastInsertId();

        // Insertion du chantier
        $stmt = $pdo->prepare("
            INSERT INTO Chantier (Titre, Description, Adresse, Ville, CodePostal, NombreColis, NoteInterventionClient, NoteInterventionEquipe, StatutIntervention, ID_Client, ID_Equipe)
            VALUES (:titre, :description, :adresse, :ville, :codePostal, :nombreColis, :noteInterventionClient, :noteInterventionEquipe, :statutIntervention, :clientId, :equipe)
        ");
        $stmt->execute([
            ':titre' => $titre,
            ':description' => $description,
            ':adresse' => $adresse,
            ':ville' => $ville,
            ':codePostal' => $codePostal,
            ':nombreColis' => $nombreColis,
            ':noteInterventionClient' => $noteInterventionClient,
            ':noteInterventionEquipe' => $noteInterventionEquipe,
            ':statutIntervention' => $statutIntervention,
            ':clientId' => $clientId,
            ':equipe' => $equipe
        ]);

        $chantierId = $pdo->lastInsertId();

        // Insertion de l'horaire du chantier
        $stmt = $pdo->prepare("
            INSERT INTO Horaire_Chantier (ID_Chantier, ID_Equipe, Date_Travail, Heure_Debut, Heure_Fin)
            VALUES (:chantierId, :equipe, :date, :heureDebut, :heureFin)
        ");
        $stmt->execute([
            ':chantierId' => $chantierId,
            ':equipe' => $equipe,
            ':date' => $date,
            ':heureDebut' => $heureDebut,
            ':heureFin' => $heureFin
        ]);

        // Insertion du locataire s'il existe
        if ($locataireNom || $locatairePrenom || $locataireTelephoneMobile || $locataireTelephoneFixe) {
            $stmt = $pdo->prepare("
                INSERT INTO Locataire (Nom, Prenom, TelephoneMobile, TelephoneFixe, ID_Client)
                VALUES (:nom, :prenom, :telephoneMobile, :telephoneFixe, :clientId)
            ");
            $stmt->execute([
                ':nom' => $locataireNom,
                ':prenom' => $locatairePrenom,
                ':telephoneMobile' => $locataireTelephoneMobile,
                ':telephoneFixe' => $locataireTelephoneFixe,
                ':clientId' => $clientId
            ]);
        }

        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['error' => 'Erreur lors de l\'ajout de l\'intervention: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Méthode de requête invalide']);
}
?>
