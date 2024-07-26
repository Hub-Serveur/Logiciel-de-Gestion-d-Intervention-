// main_StorAix.js
document.addEventListener('DOMContentLoaded', function() {
    // Vos événements existants
    const btnSemainePrecedente = document.getElementById('btnSemainePrecedente');
    const btnSemaineSuivante = document.getElementById('btnSemaineSuivante');
    
    if (btnSemainePrecedente) {
        btnSemainePrecedente.addEventListener('click', function() {
            naviguerSemaine(-1);
        });
    }

    if (btnSemaineSuivante) {
        btnSemaineSuivante.addEventListener('click', function() {
            naviguerSemaine(1);
        });
    }

    // Ajustement de la taille du texte pour toutes les cellules d'info après le chargement de la page
    document.querySelectorAll('.info-cell').forEach(adjustTextSize);
});

window.addEventListener('resize', function() {
    // Ajustement de la taille du texte lorsque la fenêtre est redimensionnée
    document.querySelectorAll('.info-cell').forEach(adjustTextSize);
});

function naviguerSemaine(decalage) {
    // Votre fonction existante pour la navigation entre les semaines
    const params = new URLSearchParams(window.location.search);
    let decalageSemaine = parseInt(params.get('decalageSemaine') || "0");
    decalageSemaine += decalage;

    params.set('decalageSemaine', decalageSemaine);
    window.location.search = params.toString();
}

function adjustTextSize(element) {
    // Fonction pour ajuster la taille du texte
    const parent = element.parentNode; // On obtient le parent (td)
    element.style.fontSize = ""; // Réinitialise la taille de police pour obtenir la taille par défaut
    let fontSize = parseFloat(window.getComputedStyle(element).fontSize);

    while (element.scrollWidth > parent.offsetWidth && fontSize > 6) {
        fontSize -= 1;
        element.style.fontSize = `${fontSize}px`;
    }

    // Si la hauteur du texte dépasse la hauteur du parent, réduire encore la taille
    while (element.scrollHeight > parent.offsetHeight && fontSize > 6) {
        fontSize -= 1;
        element.style.fontSize = `${fontSize}px`;
    }
}

function changerVue(vue) {
    const params = new URLSearchParams(window.location.search);
    params.set('vue', vue);

    // Si la vue est "jour", supprimez également le décalage de la semaine
    if (vue === 'jour') {
        params.delete('decalageSemaine');
    }

    window.location.search = params.toString();
}

function openOverlay(idChantier, uniqueId, dateChantier, heureDebut, heureFin) {
    console.log('Opening overlay for chantier ID:', idChantier, 'Unique ID:', uniqueId);
    document.getElementById('overlay').style.display = 'flex';
    document.getElementById('agenda-container').classList.add('blurred');
    fetchDetails(idChantier, uniqueId, dateChantier, heureDebut, heureFin);
}

function closeOverlay() {
    document.getElementById('overlay').style.display = 'none';
    document.getElementById('agenda-container').classList.remove('blurred');
}

function formatPhoneNumber(number) {
    return number.replace(/(\d{2})(?=\d)/g, "$1 ");
}

function addIntervention() {
    const form = document.getElementById('formAddIntervention');
    const formData = new FormData(form);

    fetch('add_intervention.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Intervention ajoutée avec succès');
            location.reload();
        } else {
            alert('Erreur: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Erreur lors de l\'ajout de l\'intervention:', error);
        alert('Erreur lors de l\'ajout de l\'intervention: ' + error);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const btnSemainePrecedente = document.getElementById('btnSemainePrecedente');
    const btnSemaineSuivante = document.getElementById('btnSemaineSuivante');
    const searchFilter = document.getElementById('searchFilter');
    
    if (btnSemainePrecedente) {
        btnSemainePrecedente.addEventListener('click', function() {
            naviguerSemaine(-1);
        });
    }

    if (btnSemaineSuivante) {
        btnSemaineSuivante.addEventListener('click', function() {
            naviguerSemaine(1);
        });
    }

    if (searchFilter) {
        searchFilter.addEventListener('input', function() {
            const query = this.value;
            if (query.length >= 3) {
                fetchSuggestions(query);
            }
        });

        searchFilter.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                const query = this.value;
                if (query.length >= 3) {
                    fetchSearchResults(query);
                }
            }
        });
    }
    
    window.handleSearch = function() {
        const query = searchFilter.value;
        if (query.length >= 3) {
            fetchSearchResults(query);
        }
    };

    function closeSearchResultsOverlay() {
        document.getElementById('overlay-search-results').style.display = 'none';
        document.getElementById('agenda-container').classList.remove('blurred');
    }

    window.closeSearchResultsOverlay = closeSearchResultsOverlay;  // Pour rendre cette fonction accessible globalement
});

function closeSearchResultsOverlay() {
    document.getElementById('overlay-search-results').style.display = 'none';
    document.getElementById('agenda-container').classList.remove('blurred');
}

function fetchClientSuggestions(query) {
    fetch(`get_client_suggestions.php?query=${query}`)
        .then(response => response.json())
        .then(data => {
            const suggestionsList = document.getElementById('clientSuggestions');
            suggestionsList.innerHTML = '';
            data.forEach(client => {
                const li = document.createElement('li');
                li.textContent = `${client.Nom} ${client.Prenom}`;
                li.addEventListener('click', () => {
                    document.getElementById('clientNom').value = client.Nom;
                    document.getElementById('clientPrenom').value = client.Prenom;
                    document.getElementById('clientEmail').value = client.Email;
                    document.getElementById('clientTelephoneMobile').value = client.TelephoneMobile;
                    document.getElementById('clientTelephoneFixe').value = client.TelephoneFixe;
                    suggestionsList.innerHTML = '';
                });
                suggestionsList.appendChild(li);
            });
        })
        .catch(error => console.error('Erreur lors du chargement des suggestions de clients:', error));
}

function fetchLocataireSuggestions(query) {
    fetch(`get_locataire_suggestions.php?query=${query}`)
        .then(response => response.json())
        .then(data => {
            const suggestionsList = document.getElementById('locataireSuggestions');
            suggestionsList.innerHTML = '';
            data.forEach(locataire => {
                const li = document.createElement('li');
                li.textContent = `${locataire.Nom} ${locataire.Prenom}`;
                li.addEventListener('click', () => {
                    document.getElementById('locataireNom').value = locataire.Nom;
                    document.getElementById('locatairePrenom').value = locataire.Prenom;
                    document.getElementById('locataireTelephoneMobile').value = locataire.TelephoneMobile;
                    document.getElementById('locataireTelephoneFixe').value = locataire.TelephoneFixe;
                    suggestionsList.innerHTML = '';
                });
                suggestionsList.appendChild(li);
            });
        })
        .catch(error => console.error('Erreur lors du chargement des suggestions de locataires:', error));
}

function editChantier() {
    document.getElementById('chantier-details').style.display = 'none';
    document.getElementById('chantier-edit').style.display = 'block';
}

const searchFilter = document.getElementById('searchFilter');
if (searchFilter) {
    searchFilter.addEventListener('input', function() {
        const query = this.value;
        if (query.length > 2) {
            fetch(`search_suggestions.php?query=${query}`)
                .then(response => response.json())
                .then(data => {
                    const suggestionsList = document.getElementById('suggestionsList');
                    suggestionsList.innerHTML = '';
                    data.forEach(item => {
                        const li = document.createElement('li');
                        li.textContent = `${item.type}: ${item.value}`;
                        li.addEventListener('click', () => {
                            fetchSearchResults(item.value, item.type);
                        });
                        suggestionsList.appendChild(li);
                    });
                })
                .catch(error => console.error('Erreur lors du chargement des suggestions:', error));
        }
    });
}

function fetchSearchResults(query, type) {
    fetch(`search_results.php?query=${query}&type=${type}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('search-results-content').innerHTML = html;
            document.getElementById('overlay-search-results').style.display = 'flex';
            document.getElementById('agenda-container').classList.add('blurred');
        })
        .catch(error => console.error('Erreur lors de la récupération des résultats de recherche:', error));
}

function closeSearchResultsOverlay() {
    document.getElementById('overlay-search-results').style.display = 'none';
    document.getElementById('agenda-container').classList.remove('blurred');
}
