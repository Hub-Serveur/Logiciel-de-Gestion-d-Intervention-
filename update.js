function updateChantier(idChantier) {
    let formData = new FormData(document.getElementById('formEditChantier'));

    fetch('update_chantier.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Erreur lors de l\'envoi:', error);
        alert('Erreur lors de l\'envoi: ' + error);
    });
}


function updateStatutInterventionStyle(selectElement) {
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    selectElement.className = 'statut-select ' + selectedOption.className;
}

function updateStatutSAVStyle(selectElement) {
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    selectElement.className = 'statut-select ' + selectedOption.className;
}