<?php
// Auteur: Nathan DAMASSE
// Ce fichier contient les fonctions pour gérer les interventions et l'agenda des chantiers dans la base de données StorAix.

require_once 'db_connect_StorAix.php';
require_once 'db_connect_users.php';
require_once 'fonction_users.php';

function getInterventionStatusColor($status) {
    switch ($status) {
        case 'En cours':
            return '#0096FF';
        case 'Cloturé':
            return '#2ECC40'; 
        case 'Facturé':
            return '#FFD700'; 
        case 'Inachevé':
            return '#ff0000'; 
        default:
            return '#000000'; 
    }
}

setlocale(LC_TIME, 'fr_FR.UTF-8', 'French_France.1252');

// Safe HTML to avoid deprecated warning with htmlspecialchars
function safeHtml($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

if (!function_exists('obtenirLundiDeLaSemaine')) {
    function obtenirLundiDeLaSemaineJour($decalage) {
        $lundi = new DateTime();
        $lundi->setISODate($lundi->format('o'), $lundi->format('W'));
        $lundi->modify('monday this week');
        $lundi->modify("$decalage weeks");
        return $lundi;
    }
}

if (!function_exists('obtenirInterventionsJour')) {
    function obtenirInterventionsJourner($dateDuJour, $pdo) {
        try {
            $stmt = $pdo->prepare('
                SELECT hc.Heure_Debut, hc.Heure_Fin, c.Titre, cl.Nom AS Nom_Client, cl.Prenom AS Prenom_Client, cl.Adresse AS Adresse_Client, cl.Ville AS Ville_Client, hc.ID_Equipe, c.StatutIntervention, e.Couleur, hc.ID_Chantier, hc.Unique_Id
                FROM Horaire_Chantier hc
                JOIN Chantier c ON hc.ID_Chantier = c.ID_Chantier
                JOIN Client cl ON c.ID_Client = cl.ID_Client
                JOIN Equipe e ON hc.ID_Equipe = e.ID_Equipe
                WHERE hc.Date_Travail = :date
            ');
            $stmt->execute(['date' => $dateDuJour->format('Y-m-d')]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Erreur lors de la récupération des interventions : " . $e->getMessage());
        }
    }
}

if (!function_exists('obtenirRoleUtilisateur')) {
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
}

if (!function_exists('obtenirEquipesUtilisateur')) {
    function obtenirEquipesUtilisateur($userID, $pdo_users, $pdo) {
        $user = getUserInfo($pdo_users, $userID);
        if (!$user) {
            echo "<pre>Utilisateur non trouvé pour l'ID : $userID</pre>";
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
            echo "<pre>Intervenant non trouvé pour les noms donnés : {$user['FirstName']} {$user['LastName']}</pre>";
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
}

if (!function_exists('obtenirEquipes')) {
    function obtenirEquipes($userID, $pdo_users, $pdo) {
        $role = obtenirRoleUtilisateur($userID, $pdo_users);
        if ($role == 'Administrateur') {
            try {
                $stmt = $pdo->query('SELECT * FROM Equipe');
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                die("Erreur lors de la récupération des équipes : " . $e->getMessage());
            }
        } else {
            return obtenirEquipesUtilisateur($userID, $pdo_users, $pdo);
        }
    }
}

function genererAgendaJourner($dateDuJour, $pdo, $pdo_users, $userID) {
    $interventions = obtenirInterventionsJourner($dateDuJour, $pdo);
    $equipes = obtenirEquipes($userID, $pdo_users, $pdo);

    echo '<table class="agenda-jour">';
    echo '<thead><tr><th>Heures</th>';
    foreach ($equipes as $equipe) {
        echo '<th>' . safeHtml($equipe['Nom_Equipe']) . '</th>';
    }
    echo '</tr></thead>';
    echo '<tbody>';

    for ($hour = 7; $hour < 19; $hour++) {
        $timeLabel = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00';
        echo '<tr>';
        echo '<td class="time-label">' . $timeLabel . '</td>';
        foreach ($equipes as $equipe) {
            echo '<td class="time-slot">';
            foreach ($interventions as $intervention) {
                $heureDebut = new DateTime($intervention['Heure_Debut']);
                $heureFin = new DateTime($intervention['Heure_Fin']);
                $currentHour = new DateTime($timeLabel . ':00');

                if ($heureDebut <= $currentHour && $heureFin > $currentHour && $intervention['ID_Equipe'] == $equipe['ID_Equipe']) {
                    $startMinutes = ($heureDebut->format('H') * 60) + $heureDebut->format('i');
                    $endMinutes = ($heureFin->format('H') * 60) + $heureFin->format('i');
                    $topOffset = (($startMinutes - $hour * 60) / 60) * 40; // 40px per hour
                    $height = (($endMinutes - $startMinutes) / 60) * 40; // 40px per hour

                    echo '<div class="intervention" onclick="openOverlay(' . $intervention['ID_Chantier'] . ', \'' . $intervention['Unique_Id'] . '\', \'' . $dateDuJour->format('Y-m-d') . '\', \'' . $intervention['Heure_Debut'] . '\', \'' . $intervention['Heure_Fin'] . '\')" style="top: ' . $topOffset . 'px; height: ' . $height . 'px; border-left-color: ' . safeHtml($equipe['Couleur']) . '; position: absolute; width: calc(100% - 10px); cursor: pointer;">';
                    echo '<span class="intervention-start-time">' . safeHtml($heureDebut->format('H:i')) . '</span>';
                    echo '<span class="intervention-title">' . safeHtml($intervention['Titre']) . '</span>';
                    if ($height >= 80) {
                        echo '<span class="intervention-client">' . safeHtml($intervention['Nom_Client']) . ' ' . safeHtml($intervention['Prenom_Client']) . '</span>';
                    }
                    if ($height >= 100) {
                        echo '<span class="intervention-ville">' . safeHtml($intervention['Ville_Client']) . '</span>';
                        echo '<span class="intervention-adresse">' . safeHtml($intervention['Adresse_Client']) . '</span>';
                    }
                    echo '<span class="intervention-end-time">' . safeHtml($heureFin->format('H:i')) . '</span>';
                    echo '<span class="intervention-pastille" style="background-color: ' . getInterventionStatusColor($intervention['StatutIntervention']) . '"></span>';
                    echo '</div>';
                }
            }
            echo '</td>';
        }
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
}

?>
