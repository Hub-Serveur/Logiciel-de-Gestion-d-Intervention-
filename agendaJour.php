<?php
// Fichier pour afficher l'agenda du jour et gérer la navigation entre les jours
// Fait par Nathan DAMASSE

require_once 'fonctionAgendaJour.php';
require_once 'fonction_users.php';
session_start();

// Vérifie si l'utilisateur est connecté, sinon redirige vers la page de connexion
if (!isUserLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Récupération de la date à afficher, par défaut aujourd'hui
$dateDuJour = isset($_GET['date']) ? new DateTime($_GET['date']) : new DateTime();
$userInfo = getUserInfo($pdo_users, $_SESSION['user_id']); // Informations de l'utilisateur connecté
$userRole = getUserRole($pdo_users, $_SESSION['user_id']); // Rôle de l'utilisateur connecté

// Vérification des valeurs retournées
if (!$userInfo) {
    die('Erreur lors de la récupération des informations de l\'utilisateur.');
}

if (!$userRole) {
    die('Erreur lors de la récupération du rôle de l\'utilisateur.');
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Agenda du Jour</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="style_header.css">
    <link rel="stylesheet" href="settings.css">
    <link rel="stylesheet" href="signature.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pikaday/1.8.0/css/pikaday.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</head>
<body>
<header class="banniere">
    <a href="index.php">
        <img src="img/logo_storaix.png" alt="Logo StorAix" width="150" height="50">
    </a>
    <div class="user-menu">
        <span class="user-name" onclick="toggleDropdown()">
            <?php echo htmlspecialchars($userInfo['FirstName'] . ' ' . $userInfo['LastName']); ?>
        </span>
        <div id="dropdown" class="dropdown-content">
            <a href="logout.php">Déconnexion</a>
        </div>
    </div>
</header>
<input type="hidden" id="userRole" value="<?php echo htmlspecialchars($userRole); ?>">
<div class="agenda-container" id="agenda-container">
    <div class="week-navigation">
        <button id="btnJourPrecedent">&lt; Jour précédent</button>
        <div class="jour">
            Jour du <?php echo $dateDuJour->format('d/m/Y'); ?>
            <button id="btnDatePicker" class="calendar-button">
                <span class="material-symbols-outlined">calendar_today</span>
            </button>
        </div>
        <button id="btnJourSuivant">Jour suivant &gt;</button>
    </div>
    <div class="view-toggle">
        <?php if ($userRole != 'Intervenant'): ?>
            <button id="btnAjouterIntervention">Ajouter Intervention</button>
        <?php endif; ?>
        <a href="index.php"><button id="btnVueSemaine">Vue Semaine</button></a>
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Recherche...">
            <div id="overlay-search" class="overlay" style="display: none;">
                <div class="overlay-content">
                    <span class="close-button" onclick="closeSearchOverlay()">&times;</span>
                    <h1>Résultats de recherche</h1>
                    <div id="overlay-content-search"></div>
                </div>
            </div>
        </div>
        <?php if ($userRole == 'Administrateur'): ?>
            <button class="btn" id="btnSettings">
                <span class="material-symbols-outlined">settings</span> Paramètres
            </button>
        <?php endif; ?>
    </div>
    <div class="table-container">
        <?php genererAgendaJourner($dateDuJour, $pdo, $pdo_users, $_SESSION['user_id']); ?>
    </div>
</div>

<div id="overlay" class="overlay" onclick="closeOverlay()">
    <div class="overlay-content" onclick="event.stopPropagation()">
        <button class="close-button" onclick="closeOverlay()">×</button>
        <div id="popup-content">
            <!-- Les détails du chantier seront chargés ici -->
        </div>
    </div>
</div>

<div id="settingsOverlay" class="overlay">
    <div class="overlay-content">
        <button class="close-button" onclick="closeSettingsOverlay()">×</button>
        <div class="settings-container">
            <div class="settings-menu">
                <button onclick="showSection('teams')">Équipes</button>
                <button onclick="showSection('clients')">Clients</button>
                <button onclick="showSection('locataires')">Locataires</button>
                <button id="accountsButton" style="display: none;" onclick="showSection('accounts')">Comptes</button>
            </div>
            <div id="settings-details" class="settings-details">
                <!-- Les détails s'afficheront ici -->
            </div>
        </div>
    </div>
</div>

<div id="editTeamOverlay" class="overlay" style="display:none;">
    <div class="overlay-content">
        <form id="editTeamForm">
            <input type="hidden" id="teamIDInput" name="teamID">
            <div class="form-group">
                <label for="editTeamName">Nom de l'Équipe:</label>
                <input type="text" id="editTeamName" name="teamName" required>
            </div>
            <div class="form-group">
                <label for="editTeamColor">Couleur de l'Équipe:</label>
                <input type="color" id="editTeamColor" name="teamColor" required>
            </div>
            <div class="form-group">
                <label for="intervenantsDropdown">Intervenants:</label>
                <select id="intervenantsDropdown" name="intervenantsDropdown" multiple>
                    <!-- Intervenants will be loaded here -->
                </select>
            </div>
            <div class="button-group">
                <button type="submit" class="btn-save">Enregistrer</button>
                <button type="button" class="btn-delete" onclick="deleteTeam()">Supprimer</button>
            </div>
        </form>
    </div>
</div>

<div id="editClientOverlay" class="overlay" style="display:none;">
    <div class="overlay-content">
        <form id="editClientForm">
            <input type="hidden" id="clientIDInput" name="clientID">
            <div class="form-group">
                <label for="editClientName">Nom:</label>
                <input type="text" id="editClientName" name="clientName" required>
            </div>
            <div class="form-group">
                <label for="editClientFirstName">Prénom:</label>
                <input type="text" id="editClientFirstName" name="clientFirstName" required>
            </div>
            <div class="form-group">
                <label for="editClientEmail">Email:</label>
                <input type="email" id="editClientEmail" name="clientEmail" required>
            </div>
            <div class="form-group">
                <label for="editClientPhoneMobile">Téléphone mobile:</label>
                <input type="tel" id="editClientPhoneMobile" name="clientPhoneMobile" required>
            </div>
            <div class="form-group">
                <label for="editClientPhoneFixed">Téléphone fixe:</label>
                <input type="tel" id="editClientPhoneFixed" name="clientPhoneFixed" required>
            </div>
            <div class="button-group">
                <button type="submit" class="btn-save">Enregistrer</button>
                <button type="button" class="btn-delete" onclick="deleteClient()">Supprimer</button>
            </div>
        </form>
    </div>
</div>

<div id="editAccountOverlay" class="overlay" style="display:none;">
    <div class="overlay-content">
        <button class="close-button" onclick="closeEditAccountOverlay()">×</button>
        <form id="editAccountForm">
            <input type="hidden" id="accountIdInput" name="accountId">
            <div class="form-group">
                <label for="editAccountFirstName">Prénom:</label>
                <input type="text" id="editAccountFirstName" name="accountFirstName" required>
            </div>
            <div class="form-group">
                <label for="editAccountLastName">Nom:</label>
                <input type="text" id="editAccountLastName" name="accountLastName" required>
            </div>
            <div class="form-group">
                <label for="editAccountIdentifiant">Identifiant:</label>
                <input type="text" id="editAccountIdentifiant" name="accountIdentifiant" required>
            </div>
            <div class="form-group">
                <label for="editAccountPassword">Mot de passe:</label>
                <input type="text" id="editAccountPassword" name="accountPassword" required>
            </div>
            <div class="form-group">
                <label for="editAccountRole">Rôle:</label>
                <select id="editAccountRole" name="accountRole" required>
                    <option value="1">Intervenant</option>
                    <option value="2">Gestionnaire</option>
                    <option value="3">Administrateur</option>
                </select>
            </div>
            <div class="button-group">
                <button type="submit" class="btn-save">Enregistrer</button>
                <button type="button" class="btn-delete" onclick="deleteAccount()">Supprimer</button>
            </div>
        </form>
    </div>
</div>

<div id="overlay-duplication" class="overlay" style="display:none;" onclick="closeDuplicationOverlay()">
    <div class="overlay-content" onclick="event.stopPropagation()">
        <button class="close-button" onclick="closeDuplicationOverlay()">×</button>
        <div id="duplication-content">
            <h1>Dupliquer l'Intervention</h1>
            <form id="formDuplicateIntervention">
                <div class="form-group">
                    <label for="dateDup">Date de l'intervention :</label>
                    <input type="date" id="dateDup" name="date" required>
                </div>
                <div class="form-group">
                    <label for="startTimeDup">Heure de début :</label>
                    <input type="time" id="startTimeDup" name="startTime" required>
                </div>
                <div class="form-group">
                    <label for="endTimeDup">Heure de fin :</label>
                    <input type="time" id="endTimeDup" name="endTime" required>
                </div>
                <div class="form-group">
                    <label for="equipeDup">Équipe :</label>
                    <select id="equipeDup" name="equipe" required></select>
                </div>
                <button type="submit" class="btn-enregistrer">Dupliquer</button>
            </form>
        </div>
    </div>
</div>

<div id="overlay-search-results" class="overlay" onclick="closeSearchResultsOverlay()">
    <div class="overlay-content" onclick="event.stopPropagation()">
        <button class="close-button" onclick="closeSearchResultsOverlay()">×</button>
        <div id="search-results-content">
            <!-- Les résultats de recherche seront chargés ici -->
        </div>
    </div>
</div>

<div id="imageOverlay" class="overlay" onclick="closeImageOverlay()">
    <div class="overlay-content" onclick="event.stopPropagation()">
        <img id="overlayImage" src="" alt="Image du chantier">
        <a id="downloadLink" href="" download>Télécharger</a>
    </div>
</div>

<!-- Overlay pour la signature électronique -->
<div id="signaturePadOverlay" class="modal">
    <div class="modal-content-signature">
        <span class="close-button" onclick="document.getElementById('signaturePadOverlay').style.display='none'">&times;</span>
        <canvas id="signaturePadCanvas" width="400" height="200" style="border:1px solid #000;"></canvas>
        <button id="saveSignatureButton">Enregistrer</button>
        <button id="cancelSignatureButton">Annuler</button>
    </div>
</div>

<!-- Modal de confirmation -->
<div id="confirmationModal" class="modal">
    <div class="modal-content-signature-ask">
        <span class="close-button-signature" onclick="document.getElementById('confirmationModal').style.display='none'">&times;</span>
        <p>Voulez-vous ajouter une signature électronique ?</p>
        <button id="yesButton">Oui</button>
        <button id="noButton">Non</button>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pikaday/1.8.0/pikaday.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/fr.min.js"></script>
<script src="mains_chantier.js"></script>
<script src="openAddInterventionForm.js"></script>
<script src="fetchDetails.js"></script>
<script src="deleteChantier.js"></script>
<script src="duplication.js"></script>
<script src="setupDragAndDrop.js"></script>
<script src="update.js"></script>
<script src="searchAgenda.js"></script>
<script src="dropdown.js"></script>
<script src="settings.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/2.3.2/signature_pad.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    moment.locale('fr');
    
    // Initialise Pikaday date picker pour la vue jour
    var picker = new Pikaday({
        field: document.getElementById('btnDatePicker'),
        trigger: document.getElementById('btnDatePicker'),
        firstDay: 1,
        format: 'DD/MM/YYYY',
        i18n: {
            previousMonth: '',
            nextMonth: '',
            months: ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'],
            weekdays: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
            weekdaysShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam']
        },
        onSelect: function(date) {
            window.location.href = 'agendaJour.php?date=' + moment(date).format('YYYY-MM-DD');
        }
    });

    // Navigation pour le jour précédent
    document.getElementById('btnJourPrecedent').addEventListener('click', function() {
        const currentDate = new Date('<?php echo $dateDuJour->format('Y-m-d'); ?>');
        currentDate.setDate(currentDate.getDate() - 1);
        window.location.href = 'agendaJour.php?date=' + currentDate.toISOString().split('T')[0];
    });

    // Navigation pour le jour suivant
    document.getElementById('btnJourSuivant').addEventListener('click', function() {
        const currentDate = new Date('<?php echo $dateDuJour->format('Y-m-d'); ?>');
        currentDate.setDate(currentDate.getDate() + 1);
        window.location.href = 'agendaJour.php?date=' + currentDate.toISOString().split('T')[0];
    });

    // Afficher le date picker quand le bouton est cliqué
    document.getElementById('btnDatePicker').addEventListener('click', function() {
        picker.show();
    });
});


</script>
</body>
</html>
