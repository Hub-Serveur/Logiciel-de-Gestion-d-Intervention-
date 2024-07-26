// Fonction pour supprimer une bulle de chantier en utilisant l'ID unique.
// Auteur: Nathan DAMASSE

function deleteChantier(uniqueId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette bulle du chantier ?')) {
        fetch('delete_chantier_bubble.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ uniqueId: uniqueId }) // Envoyer l'ID unique au serveur
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok'); // Gérer les erreurs de réseau
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('Bulle du chantier supprimée avec succès');
                location.reload(); // Recharger la page après suppression
            } else {
                alert('Erreur: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Erreur lors de la suppression:', error); // Afficher les erreurs dans la console
            alert('Erreur lors de la suppression: ' + error);
        });
    }
}
