<?php
// Auteur: Nathan DAMASSE
// Ce fichier contient les fonctions pour gérer les interactions avec les bases de données StorAix et Users.

include 'db_connect_StorAix.php';
include 'db_connect_users.php';
include 'fonction_users.php';

setlocale(LC_TIME, 'fr_FR.UTF-8', 'French_France.1252');

// Fonction pour obtenir le lundi de la semaine à partir du décalage
function obtenirLundiDeLaSemaine($decalage) {
    $lundi = new DateTime();
    $lundi->setISODate($lundi->format('o'), $lundi->format('W'));
    $lundi->modify('monday this week');
    $lundi->modify("$decalage weeks");
    return $lundi;
}

// Fonction pour obtenir les interventions d'un jour spécifique
function obtenirInterventionsJour($dateDuJour) {
    global $pdo;
    try {
        $stmt = $pdo->prepare('SELECT hc.Heure_Debut, hc.Heure_Fin, c.Description, c.Titre, c.ID_Client, cl.Nom AS ClientNom, cl.Prenom AS ClientPrenom, cl.Adresse, e.Couleur, hc.StatutIntervention
                                   FROM Horaire_Chantier hc
                                   JOIN Chantier c ON hc.ID_Chantier = c.ID_Chantier
                                   JOIN Client cl ON c.ID_Client = cl.ID_Client
                                   JOIN Equipe e ON hc.ID_Equipe = e.ID_Equipe
                                   WHERE hc.Date_Travail = :date');
        $stmt->execute(['date' => $dateDuJour->format('Y-m-d')]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Erreur lors de la récupération des interventions : " . $e->getMessage());
    }
}

// Fonction pour obtenir les équipes de l'utilisateur connecté
function obtenirEquipesUtilisateur($userID, $pdo, $pdo_users) {
    $user = getUserInfo($pdo_users, $userID);

    if (!$user) {
        return [];
    }

    $stmt = $pdo->prepare("
        SELECT i.ID_Intervenant, i.Nom, i.Prenom
        FROM Intervenant i
        WHERE i.Nom = :lastName AND i.Prenom = :firstName
    ");
    $stmt->bindParam(':firstName', $user['FirstName'], PDO::PARAM_STR);
    $stmt->bindParam(':lastName', $user['LastName'], PDO::PARAM_STR);
    $stmt->execute();
    $intervenant = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$intervenant) {
        return [];
    }

    $equipeStmt = $pdo->prepare("
        SELECT e.ID_Equipe, e.Nom_Equipe, e.Couleur
        FROM Intervenant_Equipe ie
        JOIN Equipe e ON ie.ID_Equipe = e.ID_Equipe
        WHERE ie.ID_Intervenant = :intervenantID
    ");
    $equipeStmt->bindParam(':intervenantID', $intervenant['ID_Intervenant'], PDO::PARAM_INT);
    $equipeStmt->execute();
    return $equipeStmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour obtenir le rôle de l'utilisateur
function obtenirRoleUtilisateur($userID, $pdo_users) {
    $sql = "SELECT r.RoleName 
            FROM Roles r
            JOIN Users u ON r.RoleID = u.RoleID
            WHERE u.UserID = :userID";
    try {
        $stmt = $pdo_users->prepare($sql);
        $stmt->execute(['userID' => $userID]);
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        die("Erreur lors de la récupération du rôle de l'utilisateur : " . $e->getMessage());
    }
}

// Fonction pour générer l'agenda en fonction du décalage de semaine et de l'utilisateur
function genererAgenda($decalageSemaine, $userID, $pdo, $pdo_users) {
    $lundi = obtenirLundiDeLaSemaine($decalageSemaine);
    $role = obtenirRoleUtilisateur($userID, $pdo_users);
    
    if ($role == 'Intervenant') {
        $equipes = obtenirEquipesUtilisateur($userID, $pdo, $pdo_users);
    } else {
        $stmt = $pdo->prepare("SELECT ID_Equipe, Nom_Equipe, Couleur FROM Equipe ORDER BY ID_Equipe ASC");
        $stmt->execute();
        $equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $jours_fr = ['Monday' => 'Lundi', 'Tuesday' => 'Mardi', 'Wednesday' => 'Mercredi', 'Thursday' => 'Jeudi', 'Friday' => 'Vendredi'];

    echo '<table id="agenda" border="1" style="width:100%; border-collapse: collapse;">';
    echo '<thead style="background-color: #FF006A; color: white;"><tr><th style="text-align:center;">Équipe / Jour</th>';
    for ($i = 0; $i < 5; $i++) {
        $jour = $lundi->format('l');
        echo '<th>' . $jours_fr[$jour] . ' ' . $lundi->format('d/m') . '</th>';
        $lundi->modify('+1 day');
    }
    echo '</tr></thead><tbody>';

    foreach ($equipes as $equipe) {
        echo '<tr>';
        echo "<td style='text-align:center;'>{$equipe['Nom_Equipe']}</td>";
        $lundi->modify('-5 days');

        $earliestStart = null;
        for ($j = 0; $j < 5; $j++) {
            $dateCourante = clone $lundi;
            $dateCourante->modify("+$j days");
            $dateStr = $dateCourante->format('Y-m-d');

            $sql = "SELECT MIN(hc.Heure_Debut) AS earliest 
                    FROM Horaire_Chantier hc
                    WHERE hc.Date_Travail = :date AND hc.ID_Equipe = :id_equipe";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([':date' => $dateStr, ':id_equipe' => $equipe['ID_Equipe']]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['earliest']) {
                $earliestTime = new DateTime($result['earliest']);
                if (!$earliestStart || $earliestTime < $earliestStart) {
                    $earliestStart = $earliestTime;
                }
            }
        }

        for ($j = 0; $j < 5; $j++) {
            $dateCourante = clone $lundi;
            $dateCourante->modify("+$j days");
            $dateStr = $dateCourante->format('Y-m-d');

            $sql = "SELECT c.ID_Chantier, c.Titre, c.Adresse, c.Ville, hc.Heure_Debut, hc.Heure_Fin, hc.Unique_Id, c.StatutIntervention 
                    FROM Horaire_Chantier hc
                    JOIN Chantier c ON hc.ID_Chantier = c.ID_Chantier
                    WHERE hc.Date_Travail = :date AND hc.ID_Equipe = :id_equipe
                    ORDER BY hc.Heure_Debut ASC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([':date' => $dateStr, ':id_equipe' => $equipe['ID_Equipe']]);
            $infos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo '<td style="vertical-align: top; background-color: #fff; overflow: hidden; position: relative;">';
            foreach ($infos as $info) {
                $heureDebut = new DateTime($info['Heure_Debut']);
                $interval = $heureDebut->diff($earliestStart);
                $minutes = ($interval->h * 60) + $interval->i;
                $decalage = ($minutes / (13 * 60)) * 50;

                $pastilleClass = match ($info['StatutIntervention']) {
                    'En cours' => 'pastille-en-cours',
                    'Cloturé' => 'pastille-cloture',
                    'Facturé' => 'pastille-facture',
                    'Inachevé' => 'pastille-inacheve',
                    default => ''
                };

                echo "<div class='chantier' style='border-left: 5px solid {$equipe['Couleur']};' onclick='openOverlay({$info['ID_Chantier']}, {$info['Unique_Id']}, \"{$dateStr}\", \"{$info['Heure_Debut']}\", \"{$info['Heure_Fin']}\")'>" .
                     "<h3>{$info['Titre']}</h3>" .
                     "<p style='font-style: italic; font-size: smaller; color: #666;'>{$info['Adresse']}<br>{$info['Ville']}</p>" .
                     "<p style='font-size: smaller; color: #444;'> " . date('H:i', strtotime($info['Heure_Debut'])) . " - " . date('H:i', strtotime($info['Heure_Fin'])) . "</p>" .
                     "<div class='pastille {$pastilleClass}'></div>" .
                     "</div>";
            }
            echo '</td>';
        }
        echo '</tr>';
        $lundi->modify('+5 days');
    }
    echo '</tbody></table>';
}

// Fonction pour récupérer les détails d'un chantier à partir de l'ID
function getChantierDetails($pdo, $idChantier) {
    $stmt = $pdo->prepare('
        SELECT 
            c.Titre,
            c.Adresse,
            c.Ville,
            c.CodePostal,
            c.NoteInterventionClient,
            hc.Date_Travail,
            hc.Heure_Debut,
            hc.Heure_Fin,
            cl.Nom AS ClientNom,
            cl.Prenom AS ClientPrenom,
            cl.TelephoneMobile AS ClientTelephoneMobile,
            cl.Email AS ClientEmail,
            e.Nom_Equipe
        FROM Chantier c
        JOIN Horaire_Chantier hc ON c.ID_Chantier = hc.ID_Chantier
        JOIN Client cl ON c.ID_Client = cl.ID_Client
        JOIN Equipe e ON c.ID_Equipe = e.ID_Equipe
        WHERE c.ID_Chantier = :idChantier
    ');
    $stmt->execute(['idChantier' => $idChantier]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fonction pour récupérer les détails d'un chantier à partir de l'ID et de l'ID unique
function getChantierDetailsByUniqueId($pdo, $idChantier, $uniqueId) {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                c.*, 
                e.Nom_Equipe, 
                e.Telephone AS EquipeTelephone, 
                l.Nom AS LocataireNom, 
                l.Prenom AS LocatairePrenom, 
                l.TelephoneFixe AS LocataireTelephoneFixe, 
                l.TelephoneMobile AS LocataireTelephoneMobile, 
                h.Date_Travail,
                h.Heure_Debut, 
                h.Heure_Fin, 
                cl.Nom AS ClientNom, 
                cl.Prenom AS ClientPrenom, 
                cl.Email AS ClientEmail, 
                cl.TelephoneFixe AS ClientTelephoneFixe, 
                cl.TelephoneMobile AS ClientTelephoneMobile
            FROM Chantier c
            LEFT JOIN Equipe e ON c.ID_Equipe = e.ID_Equipe
            LEFT JOIN Locataire l ON c.ID_Client = l.ID_Client
            LEFT JOIN Horaire_Chantier h ON c.ID_Chantier = h.ID_Chantier
            LEFT JOIN Client cl ON c.ID_Client = cl.ID_Client
            WHERE c.ID_Chantier = :idChantier AND h.Unique_Id = :uniqueId
        ");
        $stmt->execute([':idChantier' => $idChantier, ':uniqueId' => $uniqueId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Erreur PDO: ' . $e->getMessage());
        return false;
    }
}

// Fonction pour obtenir les intervenants d'un chantier
function getIntervenantsByChantier($pdo, $idChantier) {
    try {
        $stmt = $pdo->prepare("
            SELECT i.Nom, i.Prenom
            FROM Intervenant i
            JOIN Intervenant_Equipe ie ON i.ID_Intervenant = ie.ID_Intervenant
            WHERE ie.ID_Equipe = (SELECT ID_Equipe FROM Chantier WHERE ID_Chantier = :idChantier)
        ");
        $stmt->execute([':idChantier' => $idChantier]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Erreur PDO: ' . $e->getMessage());
        return false;
    }
}
?>
