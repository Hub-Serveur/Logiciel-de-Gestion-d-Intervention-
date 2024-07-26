document.getElementById("btnAjouterIntervention").addEventListener("click", function() {
    openAddInterventionForm();
});

function openAddInterventionForm() {
    document.getElementById('overlay').style.display = 'flex';
    document.getElementById('agenda-container').classList.add('blurred');

    document.getElementById('popup-content').innerHTML = `
        <h1>Ajouter une Intervention</h1>
        <form id="formAddIntervention">
            <div class="column-container">
                <div class="column">
                    <div class="section">
                        <h2>Intervention</h2>
                        <div class="form-group">
                            <label for="date">Date:</label>
                            <input type="date" id="date" name="date" required>
                        </div>
                        <div class="form-group">
                            <label for="heureDebut">Heure de Début:</label>
                            <input type="time" id="heureDebut" name="heureDebut" required>
                        </div>
                        <div class="form-group">
                            <label for="heureFin">Heure de Fin:</label>
                            <input type="time" id="heureFin" name="heureFin" required>
                        </div>
                    </div>
                    <div class="section client-locataire">
                        <div class="client">
                            <h2>Client</h2>
                            <div class="form-group">
                                <label for="clientNom">Nom:</label>
                                <input type="text" id="clientNom" name="clientNom">
                                <ul id="clientSuggestions" class="suggestions-list"></ul>
                            </div>
                            <div class="form-group">
                                <label for="clientPrenom">Prénom:</label>
                                <input type="text" id="clientPrenom" name="clientPrenom">
                            </div>
                            <div class="form-group">
                                <label for="clientEmail">Email:</label>
                                <input type="email" id="clientEmail" name="clientEmail">
                            </div>
                            <div class="form-group">
                                <label for="clientTelephoneMobile">Téléphone mobile:</label>
                                <input type="tel" id="clientTelephoneMobile" name="clientTelephoneMobile">
                            </div>
                            <div class="form-group">
                                <label for="clientTelephoneFixe">Téléphone fixe:</label>
                                <input type="tel" id="clientTelephoneFixe" name="clientTelephoneFixe">
                            </div>
                        </div>
                        <div class="locataire">
                            <h2>Locataire</h2>
                            <div class="form-group">
                                <label for="locataireNom">Nom:</label>
                                <input type="text" id="locataireNom" name="locataireNom">
                                <ul id="locataireSuggestions" class="suggestions-list"></ul>
                            </div>
                            <div class="form-group">
                                <label for="locatairePrenom">Prénom:</label>
                                <input type="text" id="locatairePrenom" name="locatairePrenom">
                            </div>
                            <div class="form-group">
                                <label for="locataireTelephoneMobile">Téléphone mobile:</label>
                                <input type="tel" id="locataireTelephoneMobile" name="locataireTelephoneMobile">
                            </div>
                            <div class="form-group">
                                <label for="locataireTelephoneFixe">Téléphone fixe:</label>
                                <input type="tel" id="locataireTelephoneFixe" name="locataireTelephoneFixe">
                            </div>
                        </div>
                    </div>
                    <div class="section">
                        <h2>Remarques</h2>
                        <div class="form-group">
                            <label for="noteInterventionClient">Notes d'interventions Client:</label>
                            <textarea id="noteInterventionClient" name="noteInterventionClient" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="noteInterventionEquipe">Note d'interventions Équipe:</label>
                            <textarea id="noteInterventionEquipe" name="noteInterventionEquipe" rows="3"></textarea>
                        </div>
                    </div>
                    <button class="btn-enregistrer" type="submit">
                        <span class="material-symbols-outlined">save</span> Ajouter
                    </button>
                </div>
                <div class="column">
                    <div class="section">
                        <h2>Informations Générales</h2>
                        <div class="form-group">
                            <label for="titre">Titre:</label>
                            <input type="text" id="titre" name="titre">
                        </div>
                        <div class="form-group">
                            <label for="description">Description:</label>
                            <textarea id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="adresse">Adresse:</label>
                            <input type="text" id="adresse" name="adresse">
                        </div>
                        <div class="form-group">
                            <label for="ville">Ville:</label>
                            <input type="text" id="ville" name="ville">
                        </div>
                        <div class="form-group">
                            <label for="codePostal">Code Postal:</label>
                            <input type="text" id="codePostal" name="codePostal">
                        </div>
                        <div class="form-group">
                            <label for="nombreColis">Nombre de colis:</label>
                            <input type="number" id="nombreColis" name="nombreColis">
                        </div>
                        <div class="form-group">
                            <label for="statutIntervention">Statut de l'intervention:</label>
                            <select id="statutIntervention" name="statutIntervention" class="statut-select" onchange="updateStatutInterventionStyle(this)">
                                <option value="En cours" class="statut-en-cours">En cours</option>
                                <option value="Cloturé" class="statut-cloture">Cloturé</option>
                                <option value="Facturé" class="statut-facture">Facturé</option>
                                <option value="Inachevé" class="statut-inacheve">Inachevé</option>
                            </select>
                        </div>
                    </div>
                    <div class="section full-width">
                        <h2>Équipe</h2>
                        <div class="form-group">
                            <label for="equipe">Équipe:</label>
                            <select id="equipe" name="equipe">
                                <!-- Options pour les équipes, à remplir dynamiquement -->
                            </select>
                        </div>
                    </div>
                    <div class="section full-width">
                        <h2>Images du Chantier</h2>
                        <div id="drop-area-add" class="drop-area" onclick="document.getElementById('fileElemAdd').click();">
                            <p>Glissez et déposez des images ici ou cliquez pour télécharger</p>
                            <input type="file" id="fileElemAdd" multiple accept="image/*" onchange="handleFiles(this.files)">
                        </div>
                        <div id="gallery-add"></div>
                    </div>
                </div>
            </div>
        </form>
    `;

    fetch('get_equipes.php')
        .then(response => response.json())
        .then(data => {
            const equipeSelect = document.getElementById('equipe');
            equipeSelect.innerHTML = ''; // Clear previous options
            data.equipes.forEach(equipe => {
                const option = document.createElement('option');
                option.value = equipe.ID_Equipe;
                option.textContent = equipe.Nom_Equipe;
                equipeSelect.appendChild(option);
            });
        })
        .catch(error => console.error('Erreur lors du chargement des équipes:', error));

    document.getElementById('formAddIntervention').addEventListener('submit', function(event) {
        event.preventDefault();
        addIntervention();
    });

    document.getElementById('clientNom').addEventListener('input', function() {
        fetchClientSuggestions(this.value);
    });

    document.getElementById('locataireNom').addEventListener('input', function() {
        fetchLocataireSuggestions(this.value);
    });
}
