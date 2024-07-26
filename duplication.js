// Fonction pour gérer la duplication d'interventions dans l'application
// Auteur: Nathan DAMASSE

// Ouvre l'overlay pour la duplication d'intervention
function openDuplicationOverlay(uniqueId) {
    document.getElementById('overlay-duplication').style.display = 'flex';
    document.getElementById('agenda-container').classList.add('blurred');

    // Chargement des équipes via fetch
    fetch('get_equipes.php')
        .then(response => response.json())
        .then(data => {
            const equipeSelect = document.getElementById('equipeDup');
            equipeSelect.innerHTML = ''; // Effacer les options précédentes
            data.equipes.forEach(equipe => {
                const option = document.createElement('option');
                option.value = equipe.ID_Equipe;
                option.textContent = equipe.Nom_Equipe;
                equipeSelect.appendChild(option);
            });
        })
        .catch(error => console.error('Erreur lors du chargement des équipes:', error));

    document.getElementById('formDuplicateIntervention').onsubmit = function(event) {
        event.preventDefault();
        duplicateChantier(uniqueId);
    };
}

// Ferme l'overlay de duplication
function closeDuplicationOverlay() {
    document.getElementById('overlay-duplication').style.display = 'none';
    document.getElementById('agenda-container').classList.remove('blurred');
}

// Duplique une intervention
function duplicateChantier(uniqueId) {
    const form = document.getElementById('formDuplicateIntervention');
    const formData = new FormData(form);
    formData.append('uniqueId', uniqueId);

    fetch('duplicate_chantier_bubble.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Intervention dupliquée avec succès');
            closeDuplicationOverlay();
            location.reload();
        } else {
            alert('Erreur: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Erreur lors de la duplication de l\'intervention:', error);
        alert('Erreur lors de la duplication de l\'intervention: ' + error);
    });
}
