document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    console.log('Search Input initialized:', searchInput);  // Confirmer que l'input est bien capté

    searchInput.addEventListener('keyup', function(event) {
        console.log('Key Pressed:', event.key);  // Loguer la touche pressée
        if (event.key === 'Enter') {
            console.log('Enter key pressed, triggering searchAgenda.');
            searchAgenda();
        }
    });

    const searchOverlay = document.getElementById('overlay-search');
    searchOverlay.addEventListener('click', function(event) {
        console.log('Overlay background clicked, closing overlay.');
        if (event.target === this) {
            closeSearchOverlay();
        }
    });

    const searchContent = document.getElementById('overlay-content-search');
    searchContent.addEventListener('click', function(event) {
        console.log('Click within search content, stopping propagation.');
        event.stopPropagation();
    });
});

function searchAgenda() {
    const input = document.getElementById('searchInput').value.trim();
    console.log('Search value:', input);  // Loguer la valeur saisie
    if (input === '') {
        alert('Veuillez entrer un terme de recherche.');
        return;
    }

    fetch(`searchAgenda.php?query=${encodeURIComponent(input)}`)
        .then(response => {
            console.log('Fetch response received');
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Data received:', data);  // Loguer les données reçues
            if (data.length === 0) {
                document.getElementById('overlay-content-search').innerHTML = '<p>Aucun résultat trouvé.</p>';
            } else {
                renderSearchResults(data);
            }
            document.getElementById('overlay-search').style.display = 'flex';  // Assurer que l'overlay est visible
        })
        .catch(error => {
            console.error('Error fetching search results:', error);
            document.getElementById('overlay-content-search').innerHTML = `<p>Erreur lors de la recherche: ${error.message}</p>`;
        });
}


function renderSearchResults(results) {
    const container = document.getElementById('overlay-content-search');
    container.innerHTML = '';

    results.forEach(result => {
        const tile = document.createElement('div');
        tile.className = 'search-result-tile';
        tile.innerHTML = `
            <strong>${result.chantier}</strong>
            <p style="color: ${result.teamColor};">${result.teamName}</p>
            <p>${result.clientName} ${result.clientPrenom}</p>
        `;

        tile.addEventListener('click', () => {
            console.log('Tile clicked, opening overlay.');
            openOverlay(result.idChantier, result.uniqueId, result.dateChantier, result.heureDebut, result.heureFin);
            closeSearchOverlay();
        });

        container.appendChild(tile);
    });
}

function closeSearchOverlay() {
    console.log('Closing search overlay.');
    document.getElementById('overlay-search').style.display = 'none';
}

function openOverlay(idChantier, uniqueId, dateChantier, heureDebut, heureFin) {
    console.log('Opening overlay for chantier ID:', idChantier, 'Unique ID:', uniqueId);
    document.getElementById('overlay').style.display = 'flex';
    document.getElementById('agenda-container').classList.add('blurred');
    fetchDetails(idChantier, uniqueId, dateChantier, heureDebut, heureFin);
}
