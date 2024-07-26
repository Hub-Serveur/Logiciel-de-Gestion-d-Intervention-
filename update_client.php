<?php
require_once 'db_connect_StorAix.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

$clientId = $data['ID_Client'];
$clientName = $data['Nom'];
$clientFirstName = $data['Prenom'];
$clientEmail = $data['Email'];
$clientPhoneMobile = $data['TelephoneMobile'];
$clientPhoneFixed = $data['TelephoneFixe'];

try {
    $stmt = $pdo->prepare('UPDATE Client SET Nom = ?, Prenom = ?, Email = ?, TelephoneMobile = ?, TelephoneFixe = ? WHERE ID_Client = ?');
    $stmt->execute([$clientName, $clientFirstName, $clientEmail, $clientPhoneMobile, $clientPhoneFixed, $clientId]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()]);
}
?>
