// Script pour gérer diverses actions liées aux interventions dans l'application
// Auteur: Nathan DAMASSE

document.addEventListener('DOMContentLoaded', function() {
    setupGalleryClicks();
    setupDownloadButtons();
});

// Configure les boutons de téléchargement et de duplication
function setupDownloadButtons() {
    document.querySelectorAll(".btn-download").forEach(button => {
        button.addEventListener("click", function(event) {
            event.stopPropagation();
            const idChantier = this.getAttribute('data-id-chantier');
            const uniqueId = this.getAttribute('data-unique-id');
            showConfirmationModal(idChantier, uniqueId);
        });
    });

    document.querySelectorAll(".btn-duplicate").forEach(button => {
        button.addEventListener("click", function(event) {
            event.stopPropagation();
            const uniqueId = this.getAttribute('data-unique-id');
            openDuplicationOverlay(uniqueId);
        });
    });
}

// Affiche la modal de confirmation pour la signature
function showConfirmationModal(idChantier, uniqueId) {
    const confirmationModal = document.getElementById("confirmationModal");
    confirmationModal.style.display = "flex";

    document.getElementById("yesButton").onclick = function() {
        confirmationModal.style.display = "none";
        showSignaturePad(idChantier, uniqueId);
    };

    document.getElementById("noButton").onclick = function() {
        confirmationModal.style.display = "none";
        downloadChantier(idChantier, uniqueId);
    };
}

// Affiche le pad de signature
function showSignaturePad(idChantier, uniqueId) {
    const signaturePadOverlay = document.getElementById("signaturePadOverlay");
    signaturePadOverlay.style.display = "flex";

    const signaturePadCanvas = document.getElementById("signaturePadCanvas");
    const context = signaturePadCanvas.getContext("2d");

    // Ajuster la taille du canvas
    signaturePadCanvas.width = window.innerWidth * 0.56;
    signaturePadCanvas.height = window.innerHeight * 0.7;

    // Initialiser SignaturePad avec les nouvelles dimensions
    const signaturePad = new SignaturePad(signaturePadCanvas, {
        minWidth: 0.6,
        maxWidth: 2.5,
        penColor: "black"
    });

    const saveButton = document.getElementById("saveSignatureButton");
    const cancelButton = document.getElementById("cancelSignatureButton");

    saveButton.onclick = function() {
        saveSignature(idChantier, uniqueId, signaturePad);
    };
    cancelButton.onclick = closeSignaturePad;

    // Ajuster le décalage du canvas pour corriger l'alignement
    function resizeCanvas() {
        const ratio =  Math.max(window.devicePixelRatio || 1, 1);
        signaturePadCanvas.width = signaturePadCanvas.offsetWidth * ratio;
        signaturePadCanvas.height = signaturePadCanvas.offsetHeight * ratio;
        signaturePadCanvas.getContext("2d").scale(ratio, ratio);
        signaturePad.clear(); // otherwise isEmpty() might return incorrect value
    }
    window.onresize = resizeCanvas;
    resizeCanvas();
}

// Enregistre la signature et télécharge le chantier
function saveSignature(idChantier, uniqueId, signaturePad) {
    if (signaturePad.isEmpty()) {
        alert("Veuillez ajouter une signature.");
    } else {
        const signatureImage = signaturePad.toDataURL('image/png');
        closeSignaturePad();
        downloadChantier(idChantier, uniqueId, signatureImage);
    }
}

// Ferme le pad de signature
function closeSignaturePad() {
    const signaturePadOverlay = document.getElementById("signaturePadOverlay");
    signaturePadOverlay.style.display = "none";
}

// Télécharge les détails du chantier
function downloadChantier(idChantier, uniqueId, signature = null) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'generate_intervention_pdf.php';
    form.target = '_blank';

    const idInput = document.createElement('input');
    idInput.type = 'hidden';
    idInput.name = 'id';
    idInput.value = idChantier;

    const uniqueIdInput = document.createElement('input');
    uniqueIdInput.type = 'hidden';
    uniqueIdInput.name = 'uniqueId';
    uniqueIdInput.value = uniqueId;

    const signatureInput = document.createElement('input');
    signatureInput.type = 'hidden';
    signatureInput.name = 'signature';
    signatureInput.value = signature;

    form.appendChild(idInput);
    form.appendChild(uniqueIdInput);
    if (signature) {
        form.appendChild(signatureInput);
    }

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

// Récupère les détails du chantier
function fetchDetails(idChantier, uniqueId, dateChantier, heureDebut, heureFin) {
    console.log('Fetching details for chantier ID:', idChantier, 'Unique ID:', uniqueId);
    fetch(`get_chantier_details.php?id=${idChantier}&uniqueId=${uniqueId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error('Error in response data:', data.error);
                document.getElementById('popup-content').innerHTML = '<p>Erreur : ' + data.error + '</p>';
            } else {
                // Formatage des dates et heures
                const formatDate = (dateStr) => {
                    const date = new Date(dateStr);
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const year = date.getFullYear();
                    return `${day}/${month}/${year}`;
                };

                const formatTime = (timeStr) => {
                    const time = new Date(`1970-01-01T${timeStr}Z`);
                    const hours = String(time.getUTCHours()).padStart(2, '0');
                    const minutes = String(time.getUTCMinutes()).padStart(2, '0');
                    return `${hours}:${minutes}`;
                };

                const formatPhoneNumber = (number) => number ? number.replace(/(\d{2})(?=\d)/g, "$1 ") : 'Non spécifié';

                const getStatutInterventionClass = (statut) => {
                    switch (statut) {
                        case 'En cours':
                            return 'statut-en-cours';
                        case 'Cloturé':
                            return 'statut-cloture';
                        case 'Facturé':
                            return 'statut-facture';
                        case 'Inachevé':
                            return 'statut-inacheve';
                        default:
                            return '';
                    }
                };

                const formatNotes = (notes) => {
                    if (!notes) return 'Aucune note';
                    return notes
                        .split('\n')
                        .map(line => line ? `<p>${line}</p>` : '<br>')
                        .join('');
                };

                let locataireSection = '';
                if (data.chantier.LocataireNom) {
                    locataireSection = `
                        <div class="section">
                            <h2>Locataire</h2>
                            <p><strong>Nom:</strong> ${data.chantier.LocataireNom || 'Non spécifié'}</p>
                            <p><strong>Prénom:</strong> ${data.chantier.LocatairePrenom || 'Non spécifié'}</p>
                            <p><strong>Téléphone mobile:</strong> <a href="tel:${data.chantier.LocataireTelephoneMobile}">${formatPhoneNumber(data.chantier.LocataireTelephoneMobile)}</a></p>
                            <p><strong>Téléphone fixe:</strong> <a href="tel:${data.chantier.LocataireTelephoneFixe}">${formatPhoneNumber(data.chantier.LocataireTelephoneFixe)}</a></p>
                        </div>
                    `;
                }

                const userRole = document.getElementById('userRole').value;

                let buttonContainer = '';
                if (userRole !== 'Intervenant') {
                    buttonContainer = `
                        <div class="btn-container">
                            <button class="btn-modifier" type="button" onclick="editChantier()">
                                <span class="material-symbols-outlined">edit</span> Modifier
                            </button>
                            <button class="btn-delete" type="button" onclick="deleteChantier(${uniqueId})">
                                <span class="material-symbols-outlined">delete</span> Supprimer
                            </button>
                            <button class="btn-duplicate" type="button" data-unique-id="${uniqueId}">
                                <span class="material-symbols-outlined">content_copy</span> Dupliquer
                            </button>
                            <button class="btn-download" type="button" data-id-chantier="${idChantier}" data-unique-id="${uniqueId}">
                                <span class="material-symbols-outlined">file_download</span> Télécharger
                            </button>
                        </div>
                    `;
                } else {
                    buttonContainer = `
                        <div class="btn-container">
                            <button class="btn-modifier" type="button" onclick="editChantier()">
                                <span class="material-symbols-outlined">edit</span> Modifier
                            </button>
                        </div>
                    `;
                }

                // Remplir le contenu avec les détails du chantier
                let content = `
                    <h1>${data.chantier.Titre || 'Sans titre'}</h1>
                    <div id="chantier-details">
                        <div class="column-container">
                            <div class="column">
                                <div class="section">
                                    <h2>Intervention</h2>
                                    <p><strong>Date de l'intervention:</strong> ${formatDate(dateChantier)}</p>
                                    <p><strong>Heure de début:</strong> ${formatTime(heureDebut)}</p>
                                    <p><strong>Heure de fin:</strong> ${formatTime(heureFin)}</p>
                                </div>
                                <div class="section client-locataire">
                                    <div class="client">
                                        <h2>Client</h2>
                                        <p><strong>Nom:</strong> ${data.chantier.ClientNom || 'Non spécifié'}</p>
                                        <p><strong>Prénom:</strong> ${data.chantier.ClientPrenom || 'Non spécifié'}</p>
                                        <p><strong>Email:</strong> <a href="mailto:${data.chantier.ClientEmail}">${data.chantier.ClientEmail || 'Non spécifié'}</a></p>
                                        <p><strong>Téléphone mobile:</strong> <a href="tel:${data.chantier.ClientTelephoneMobile}">${formatPhoneNumber(data.chantier.ClientTelephoneMobile)}</a></p>
                                        <p><strong>Téléphone fixe:</strong> <a href="tel:${data.chantier.ClientTelephoneFixe}">${formatPhoneNumber(data.chantier.ClientTelephoneFixe)}</a></p>
                                    </div>
                                    ${locataireSection}
                                </div>
                                <div class="section">
                                    <h2>Remarques</h2>
                                    <p><strong>Notes d'interventions Client:</strong></p>
                                    <p>${formatNotes(data.chantier.NoteInterventionClient)}</p>
                                    <p><strong>Notes d'interventions Équipe:</strong></p>
                                    <p>${formatNotes(data.chantier.NoteInterventionEquipe)}</p>
                                </div>
                                ${buttonContainer}
                            </div>
                            <div class="column">
                                <div class="section">
                                    <h2>Informations Générales</h2>
                                    <p><strong>Description:</strong> ${data.chantier.Description || 'Non spécifiée'}</p>
                                    <p><strong>Adresse:</strong> <a href="https://www.google.com/maps/dir/?api=1&destination=${encodeURIComponent(data.chantier.Adresse + ', ' + data.chantier.Ville)}" target="_blank">${data.chantier.Adresse || 'Non spécifiée'}</a></p>
                                    <p><strong>Ville:</strong> ${data.chantier.Ville || 'Non spécifiée'}</p>
                                    <p><strong>Code Postal:</strong> ${data.chantier.CodePostal || 'Non spécifié'}</p>
                                    <p><strong>Nombre de colis:</strong> ${data.chantier.NombreColis || 'Non spécifié'}</p>
                                    <p><strong>Statut de l'intervention:</strong> <span class="statut ${getStatutInterventionClass(data.chantier.StatutIntervention)}">${data.chantier.StatutIntervention || 'Non spécifié'}</span></p>
                                </div>
                                <div class="section full-width">
                                    <h2>Équipe</h2>
                                    <p><strong>Nom:</strong> ${data.chantier.Nom_Equipe || 'Non spécifié'}</p>
                                    <p><strong>Téléphone de l'équipe:</strong> <a href="tel:${data.chantier.EquipeTelephone}">${formatPhoneNumber(data.chantier.EquipeTelephone || '')}</a></p>
                                    <div class="intervenants">
                                        ${data.intervenants.map(intervenant => `
                                            <span><strong>Nom:</strong> ${intervenant.Prenom} ${intervenant.Nom}</span>
                                        `).join(' ')}
                                    </div>
                                </div>
                                <div class="section full-width">
                                    <h2>Images du Chantier</h2>
                                    <div id="drop-area" class="drop-area" onclick="document.getElementById('fileElem').click();">
                                        <p>Glissez et déposez des images ici ou cliquez pour télécharger</p>
                                        <input type="file" id="fileElem" multiple accept="image/*" onchange="handleFiles(this.files)">
                                    </div>
                                    <div id="gallery">
                                        ${data.images.map(image => `
                                            <img src="${image.Image_Path}" class="gallery-img" alt="Image du chantier">
                                        `).join(' ')}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="chantier-edit" style="display: none;">
                        <form id="formEditChantier">
                            <input type="hidden" name="idChantier" value="${idChantier}">
                            <input type="hidden" name="idHoraire" value="${uniqueId}">
                            <div class="column-container">
                                <div class="column">
                                    <div class="section">
                                        <h2>Intervention</h2>
                                        <p><strong>Date de l'intervention:</strong> <input type="date" name="dateIntervention" value="${formatDate(dateChantier)}" disabled></p>
                                        <p><strong>Heure de début:</strong> ${userRole === 'Intervenant' ? `<input type="hidden" name="heureDebut" value="${formatTime(heureDebut)}">${formatTime(heureDebut)}` : `<input type="time" name="heureDebut" value="${formatTime(heureDebut)}">`}</p>
                                        <p><strong>Heure de fin:</strong> ${userRole === 'Intervenant' ? `<input type="hidden" name="heureFin" value="${formatTime(heureFin)}">${formatTime(heureFin)}` : `<input type="time" name="heureFin" value="${formatTime(heureFin)}">`}</p>
                                    </div>
                                    <div class="section client-locataire">
                                        <div class="client">
                                            <h2>Client</h2>
                                            <p><strong>Nom:</strong> ${userRole === 'Intervenant' ? `<input type="hidden" name="clientNom" value="${data.chantier.ClientNom}">${data.chantier.ClientNom}` : `<input type="text" name="clientNom" value="${data.chantier.ClientNom || ''}" id="clientNom">`}</p>
                                            <p><strong>Prénom:</strong> ${userRole === 'Intervenant' ? `<input type="hidden" name="clientPrenom" value="${data.chantier.ClientPrenom}">${data.chantier.ClientPrenom}` : `<input type="text" name="clientPrenom" value="${data.chantier.ClientPrenom || ''}" id="clientPrenom">`}</p>
                                            <p><strong>Email:</strong> ${userRole === 'Intervenant' ? `<input type="hidden" name="clientEmail" value="${data.chantier.ClientEmail}">${data.chantier.ClientEmail}` : `<input type="email" name="clientEmail" value="${data.chantier.ClientEmail || ''}" id="clientEmail">`}</p>
                                            <p><strong>Téléphone mobile:</strong> ${userRole === 'Intervenant' ? `<input type="hidden" name="clientTelephoneMobile" value="${data.chantier.ClientTelephoneMobile}">${formatPhoneNumber(data.chantier.ClientTelephoneMobile)}` : `<input type="tel" name="clientTelephoneMobile" value="${formatPhoneNumber(data.chantier.ClientTelephoneMobile || '')}" id="clientTelephoneMobile">`}</p>
                                            <p><strong>Téléphone fixe:</strong> ${userRole === 'Intervenant' ? `<input type="hidden" name="clientTelephoneFixe" value="${data.chantier.ClientTelephoneFixe}">${formatPhoneNumber(data.chantier.ClientTelephoneFixe)}` : `<input type="tel" name="clientTelephoneFixe" value="${formatPhoneNumber(data.chantier.ClientTelephoneFixe || '')}" id="clientTelephoneFixe">`}</p>
                                        </div>
                                        ${data.chantier.LocataireNom ? `
                                        <div class="locataire">
                                            <h2>Locataire</h2>
                                            <p><strong>Nom:</strong> ${userRole === 'Intervenant' ? `<input type="hidden" name="locataireNom" value="${data.chantier.LocataireNom}">${data.chantier.LocataireNom}` : `<input type="text" name="locataireNom" value="${data.chantier.LocataireNom || ''}" id="locataireNom">`}</p>
                                            <p><strong>Prénom:</strong> ${userRole === 'Intervenant' ? `<input type="hidden" name="locatairePrenom" value="${data.chantier.LocatairePrenom}">${data.chantier.LocatairePrenom}` : `<input type="text" name="locatairePrenom" value="${data.chantier.LocatairePrenom || ''}" id="locatairePrenom">`}</p>
                                            <p><strong>Téléphone mobile:</strong> ${userRole === 'Intervenant' ? `<input type="hidden" name="locataireTelephoneMobile" value="${data.chantier.LocataireTelephoneMobile}">${formatPhoneNumber(data.chantier.LocataireTelephoneMobile)}` : `<input type="tel" name="locataireTelephoneMobile" value="${formatPhoneNumber(data.chantier.LocataireTelephoneMobile || '')}" id="locataireTelephoneMobile">`}</p>
                                            <p><strong>Téléphone fixe:</strong> ${userRole === 'Intervenant' ? `<input type="hidden" name="locataireTelephoneFixe" value="${data.chantier.LocataireTelephoneFixe}">${formatPhoneNumber(data.chantier.LocataireTelephoneFixe)}` : `<input type="tel" name="locataireTelephoneFixe" value="${formatPhoneNumber(data.chantier.LocataireTelephoneFixe || '')}" id="locataireTelephoneFixe">`}</p>
                                        </div>
                                        ` : ''}
                                    </div>

                                    <div class="section">
                                        <h2>Remarques</h2>
                                        <p><strong>Notes d'interventions Client:</strong></p>
                                        <textarea name="noteInterventionClient" rows="3">${data.chantier.NoteInterventionClient || ''}</textarea>
                                        <p><strong>Notes d'interventions Équipe:</strong></p>
                                        <textarea name="noteInterventionEquipe" rows="3">${data.chantier.NoteInterventionEquipe || ''}</textarea>
                                    </div>
                                    <button class="btn-enregistrer" type="submit">
                                        <span class="material-symbols-outlined">save</span> Enregistrer
                                    </button>
                                </div>
                                <div class="column">
                                    <div class="section">
                                        <h2>Informations Générales</h2>
                                        <p><strong>Titre:</strong> ${userRole === 'Intervenant' ? `<input type="hidden" name="titre" value="${data.chantier.Titre}">${data.chantier.Titre}` : `<input type="text" name="titre" value="${data.chantier.Titre || ''}">`}</p>
                                        <p><strong>Description:</strong> ${userRole === 'Intervenant' ? `<input type="hidden" name="description" value="${data.chantier.Description}">${data.chantier.Description || 'Non spécifiée'}` : `<textarea name="description">${data.chantier.Description || ''}</textarea>`}</p>
                                        <p><strong>Adresse:</strong> ${userRole === 'Intervenant' ? `<input type="hidden" name="adresse" value="${data.chantier.Adresse}">${data.chantier.Adresse || 'Non spécifiée'}` : `<input type="text" name="adresse" value="${data.chantier.Adresse || ''}">`}</p>
                                        <p><strong>Ville:</strong> ${userRole === 'Intervenant' ? `<input type="hidden" name="ville" value="${data.chantier.Ville}">${data.chantier.Ville || 'Non spécifiée'}` : `<input type="text" name="ville" value="${data.chantier.Ville || ''}">`}</p>
                                        <p><strong>Code Postal:</strong> ${userRole === 'Intervenant' ? `<input type="hidden" name="codePostal" value="${data.chantier.CodePostal}">${data.chantier.CodePostal || 'Non spécifié'}` : `<input type="text" name="codePostal" value="${data.chantier.CodePostal || ''}">`}</p>
                                        <p><strong>Nombre de colis:</strong> ${userRole === 'Intervenant' ? `<input type="hidden" name="nombreColis" value="${data.chantier.NombreColis}">${data.chantier.NombreColis || 'Non spécifié'}` : `<input type="number" name="nombreColis" value="${data.chantier.NombreColis || ''}">`}</p>
                                        <p><strong>Statut de l'intervention:</strong>
                                            <select name="statutIntervention" class="statut-select" onchange="updateStatutInterventionStyle(this)">
                                                <option value="En cours" ${data.chantier.StatutIntervention === 'En cours' ? 'selected' : ''} class="statut-en-cours">En cours</option>
                                                <option value="Cloturé" ${data.chantier.StatutIntervention === 'Cloturé' ? 'selected' : ''} class="statut-cloture">Cloturé</option>
                                                <option value="Facturé" ${data.chantier.StatutIntervention === 'Facturé' ? 'selected' : ''} class="statut-facture">Facturé</option>
                                                <option value="Inachevé" ${data.chantier.StatutIntervention === 'Inachevé' ? 'selected' : ''} class="statut-inacheve">Inachevé</option>
                                            </select>
                                        </p>
                                    </div>

                                    <div class="section full-width">
                                        <h2>Équipe</h2>
                                        <p><strong>Nom:</strong> ${data.chantier.Nom_Equipe || 'Non spécifié'}</p>
                                        <p><strong>Téléphone de l'équipe:</strong> <a href="tel:${data.chantier.EquipeTelephone}">${formatPhoneNumber(data.chantier.EquipeTelephone || '')}</a></p>
                                        <div class="intervenants">
                                            ${data.intervenants.map(intervenant => `
                                                <span><strong>Nom:</strong> ${intervenant.Prenom} ${intervenant.Nom}</span>
                                            `).join(' ')}
                                        </div>
                                    </div>
                                    <div class="section full-width">
                                        <h2>Images du Chantier</h2>
                                        <div id="drop-area-edit" class="drop-area" onclick="document.getElementById('fileElemEdit').click();">
                                            <p>Glissez et déposez des images ici ou cliquez pour télécharger</p>
                                            <input type="file" id="fileElemEdit" multiple accept="image/*" onchange="handleFiles(this.files)">
                                        </div>
                                        <div id="gallery-edit">
                                            ${data.images.map(image => `
                                                <div class="img-container">
                                                    <img src="${image.Image_Path}" class="gallery-img" alt="Image du chantier">
                                                    <span class="delete-btn" onclick="deleteImage('${image.Image_Path}', ${idChantier}, this)">&times;</span>
                                                </div>
                                            `).join(' ')}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                `;
                document.getElementById('popup-content').innerHTML = content;

                document.getElementById('formEditChantier').addEventListener('submit', function(e) {
                    e.preventDefault();
                    updateChantier(idChantier);
                });

                setupDragAndDrop('drop-area', 'fileElem', idChantier);
                setupDragAndDrop('drop-area-edit', 'fileElemEdit', idChantier);
                setupGalleryClicks();
                setupDownloadButtons(); 
            }
        })
        .catch(error => {
            console.error('Erreur lors du chargement des détails du chantier:', error);
            document.getElementById('popup-content').innerHTML = '<p>Erreur lors du chargement des détails du chantier.</p>';
        });
}

// Supprime une image du chantier
function deleteImage(imageUrl, idChantier, element) {
    let url = 'delete_image.php';
    let formData = new FormData();
    formData.append('imageUrl', imageUrl);
    formData.append('idChantier', idChantier);

    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Image deleted:', imageUrl);
            element.parentElement.remove();
        } else {
            console.error('Delete failed:', data.error);
        }
    })
    .catch(() => {
        console.error('Delete failed');
    });
}

// Configure les clics sur la galerie
function setupGalleryClicks() {
    const images = document.querySelectorAll('.gallery-img');
    images.forEach(image => {
        image.addEventListener('click', function() {
            openImageOverlay(this.src);
        });
    });
}

// Ouvre une image en grand
function openImageOverlay(imageUrl) {
    const overlay = document.getElementById('imageOverlay');
    const overlayImage = document.getElementById('overlayImage');
    const downloadLink = document.getElementById('downloadLink');

    overlayImage.src = imageUrl;
    downloadLink.href = imageUrl; 

    overlay.style.display = 'flex';
    overlay.addEventListener('click', closeImageOverlay); 
}

// Ferme l'overlay de l'image
function closeImageOverlay(event) {
    if (event.target.id === 'imageOverlay') {
        const overlay = document.getElementById('imageOverlay');
        overlay.style.display = 'none';
    }
}

// Configure le drag and drop pour les images
function setupDragAndDrop(dropAreaId, fileInputId, idChantier) {
    let dropArea = document.getElementById(dropAreaId);

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });

    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, unhighlight, false);
    });

    dropArea.addEventListener('drop', handleDrop, false);

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function highlight() {
        dropArea.classList.add('highlight');
    }

    function unhighlight() {
        dropArea.classList.remove('highlight');
    }

    function handleDrop(e) {
        let dt = e.dataTransfer;
        let files = dt.files;

        handleFiles(files);
    }

    function handleFiles(files) {
        [...files].forEach(uploadFile);
        [...files].forEach(previewFile);
    }

    function uploadFile(file) {
        let url = 'upload_image.php';
        let formData = new FormData();
        formData.append('file', file);
        formData.append('idChantier', idChantier);

        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Image uploaded:', data.imagePath);
                fetchDetails(idChantier, uniqueId, dateChantier, heureDebut, heureFin);
            } else {
                console.error('Upload failed:', data.error);
            }
        })
        .catch(() => {
            console.error('Upload failed');
        });
    }

    function previewFile(file) {
        let reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onloadend = function() {
            let img = document.createElement('img');
            img.src = reader.result;
            img.classList.add('gallery-img');
            document.getElementById('gallery').appendChild(img);
            setupGalleryClicks();
        }
    }
}
