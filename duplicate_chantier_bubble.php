<?php
include 'db_connect_StorAix.php'; // Connexion à la base de données

header('Content-Type: application/json'); // Réponse en JSON

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$response = array();

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (!isset($_POST['uniqueId']) || !isset($_POST['equipe']) || !isset($_POST['date']) || !isset($_POST['startTime']) || !isset($_POST['endTime'])) {
            throw new Exception('Paramètres manquants.');
        }

        $uniqueId = $_POST['uniqueId'];
        $idEquipe = $_POST['equipe'];
        $dateTravail = $_POST['date'];
        $heureDebut = $_POST['startTime'];
        $heureFin = $_POST['endTime'];

        // Début de la transaction
        $pdo->beginTransaction();

        // Récupérer les détails de l'horaire du chantier à dupliquer
        $stmt = $pdo->prepare("SELECT * FROM Horaire_Chantier WHERE Unique_Id = :uniqueId");
        $stmt->execute([':uniqueId' => $uniqueId]);
        $horaire = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$horaire) {
            throw new Exception('Horaire du chantier non trouvé.');
        }

        // Insérer une nouvelle entrée dans la table Horaire_Chantier avec les détails fournis
        $stmt = $pdo->prepare("
            INSERT INTO Horaire_Chantier (ID_Chantier, ID_Equipe, Date_Travail, Heure_Debut, Heure_Fin)
            VALUES (:idChantier, :idEquipe, :dateTravail, :heureDebut, :heureFin)
        ");
        $stmt->execute([
            ':idChantier' => $horaire['ID_Chantier'],
            ':idEquipe' => $idEquipe,
            ':dateTravail' => $dateTravail,
            ':heureDebut' => $heureDebut,
            ':heureFin' => $heureFin
        ]);

        // Validation de la transaction
        $pdo->commit();

        $response['success'] = 'Bulle du chantier dupliquée avec succès';
    } else {
        throw new Exception('Requête invalide.');
    }
} catch (PDOException $e) {
    // En cas d'erreur, annuler la transaction
    $pdo->rollBack();
    $response['error'] = 'Erreur lors de la duplication de la bulle du chantier: ' . $e->getMessage();
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
?>
