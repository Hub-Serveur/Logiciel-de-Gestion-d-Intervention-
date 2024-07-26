document.addEventListener('DOMContentLoaded', function() {
    const settingsButton = document.getElementById('btnSettings');
    const settingsOverlay = document.getElementById('settingsOverlay');
    const editTeamOverlay = document.getElementById('editTeamOverlay');
    const editClientOverlay = document.getElementById('editClientOverlay');
    const editAccountOverlay = document.getElementById('editAccountOverlay');
    const editTeamForm = document.getElementById('editTeamForm');
    const editClientForm = document.getElementById('editClientForm');
    const editAccountForm = document.getElementById('editAccountForm');
    const userRole = document.getElementById('userRole').value;

    settingsButton.addEventListener('click', function() {
        settingsOverlay.style.display = 'flex';
        document.getElementById('agenda-container').classList.add('blurred');
        loadSettingsContent();
        showSection('teams');
    });

    settingsOverlay.addEventListener('click', function(event) {
        if (event.target === settingsOverlay) {
            closeSettingsOverlay();
        }
    });

    settingsOverlay.children[0].addEventListener('click', function(event) {
        event.stopPropagation();
    });

    editAccountForm.addEventListener('submit', function(event) {
        event.preventDefault();
        submitAccountChanges();
    });

    editTeamForm.addEventListener('submit', function(event) {
        event.preventDefault();
        submitTeamChanges();
    });

    editClientForm.addEventListener('submit', function(event) {
        event.preventDefault();
        submitClientChanges();
    });

    function submitAccountChanges() {
        const accountId = document.getElementById('accountIdInput').value;
        const identifiant = document.getElementById('editAccountIdentifiant').value;
        const role = document.getElementById('editAccountRole').value;
        const password = document.getElementById('editAccountPassword').value;
        const lastName = document.getElementById('editAccountLastName').value;
        const firstName = document.getElementById('editAccountFirstName').value;

        const url = 'update_account.php';
        const payload = { id: accountId, identifiant: identifiant, role: role, password: password, lastName: lastName, firstName: firstName };

        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(response => response.text())
        .then(text => {
            try {
                const data = JSON.parse(text);
                if (data.success) {
                    alert('Mise à jour réussie');
                    document.getElementById('editAccountOverlay').style.display = 'none';
                    fetchAccounts(document.getElementById('settings-details'));
                } else {
                    alert('Erreur lors de la mise à jour: ' + data.error);
                }
            } catch (error) {
                alert('Erreur lors de la mise à jour: ' + error.message + '\n\n' + text);
            }
        })
        .catch(error => {
            alert('Erreur lors de la mise à jour: ' + error.message);
        });
    }

    window.deleteAccount = function() {
        const accountId = document.getElementById('accountIdInput').value;
        const lastName = document.getElementById('editAccountLastName').value;
        const firstName = document.getElementById('editAccountFirstName').value;

        if (!accountId) {
            alert('Erreur : ID du compte non trouvé.');
            return;
        }

        if (confirm('Êtes-vous sûr de vouloir supprimer ce compte ? Cette action est irréversible.')) {
            fetch('delete_account.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: accountId, lastName: lastName, firstName: firstName })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Compte supprimé avec succès');
                    editAccountOverlay.style.display = 'none';
                    fetchAccounts(document.getElementById('settings-details'));
                } else {
                    alert('Erreur lors de la suppression: ' + data.error);
                }
            })
            .catch(error => {
                alert('Erreur lors de la suppression: ' + error.message);
            });
        }
    };

    window.deleteTeam = function() {
        const teamId = document.getElementById('editTeamName').dataset.teamId;
        if (!teamId) {
            alert('Erreur : ID de l\'équipe non trouvé.');
            return;
        }

        if (confirm('Êtes-vous sûr de vouloir supprimer cette équipe ? Cette action est irréversible.')) {
            fetch('delete_team.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ID_Equipe: teamId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Équipe supprimée avec succès');
                    editTeamOverlay.style.display = 'none';
                    fetchTeams(document.getElementById('settings-details'));
                } else {
                    alert('Erreur lors de la suppression: ' + data.error);
                }
            })
            .catch(error => {
                alert('Erreur lors de la suppression: ' + error.message);
            });
        }
    };

    window.deleteClient = function() {
        const clientId = document.getElementById('editClientName').dataset.clientId;
        if (!clientId) {
            alert('Erreur : ID du client non trouvé.');
            return;
        }

        if (confirm('Êtes-vous sûr de vouloir supprimer ce client ? Cette action est irréversible.')) {
            fetch('delete_client.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ID_Client: clientId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Client supprimé avec succès');
                    editClientOverlay.style.display = 'none';
                    fetchClients(document.getElementById('settings-details'));
                } else {
                    alert('Erreur lors de la suppression: ' + data.error);
                }
            })
            .catch(error => {
                alert('Erreur lors de la suppression: ' + error.message);
            });
        }
    };

    function submitTeamChanges() {
        const teamId = document.getElementById('editTeamName').dataset.teamId;
        const teamName = document.getElementById('editTeamName').value;
        const teamColor = document.getElementById('editTeamColor').value;
        const intervenants = Array.from(document.getElementById('intervenantsDropdown').selectedOptions).map(option => option.value);

        const url = teamId ? 'update_team.php' : 'add_team.php';
        const payload = { Nom_Equipe: teamName, Couleur: teamColor, Intervenants: intervenants };
        if (teamId) payload.ID_Equipe = teamId;

        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Mise à jour réussie');
                editTeamOverlay.style.display = 'none';
                fetchTeams(document.getElementById('settings-details'));
            } else {
                alert('Erreur lors de la mise à jour: ' + data.error);
            }
        })
        .catch(error => {
            alert('Erreur lors de la mise à jour: ' + error.message);
        });
    }

    function submitClientChanges() {
        const clientId = document.getElementById('editClientName').dataset.clientId;
        const clientName = document.getElementById('editClientName').value;
        const clientFirstName = document.getElementById('editClientFirstName').value;
        const clientEmail = document.getElementById('editClientEmail').value;
        const clientPhoneMobile = document.getElementById('editClientPhoneMobile').value;
        const clientPhoneFixed = document.getElementById('editClientPhoneFixed').value;

        const url = 'update_client.php';
        const payload = { ID_Client: clientId, Nom: clientName, Prenom: clientFirstName, Email: clientEmail, TelephoneMobile: clientPhoneMobile, TelephoneFixe: clientPhoneFixed };

        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Mise à jour réussie');
                editClientOverlay.style.display = 'none';
                fetchClients(document.getElementById('settings-details'));
            } else {
                alert('Erreur lors de la mise à jour: ' + data.error);
            }
        })
        .catch(error => {
            alert('Erreur lors de la mise à jour: ' + error.message);
        });
    }

    function loadSettingsContent() {
        const contentDiv = settingsOverlay.querySelector('.overlay-content');
        contentDiv.innerHTML = `
            <div class="settings-sidebar">
                <ul class="settings-menu">
                    <li><button onclick="showSection('teams')">Équipes</button></li>
                    <li><button onclick="showSection('clients')">Clients</button></li>
                    ${userRole == 'Administrateur' ? '<li><button onclick="showSection(\'accounts\')">Comptes</button></li>' : ''}
                </ul>
            </div>
            <div id="settings-details" class="settings-details"></div>
        `;
        applyStyles();
    }

    function applyStyles() {
        document.querySelector('.settings-sidebar').style.cssText = `
            width: 200px;
            height: 100%;
            background-color: #143446;
            box-shadow: 2px 0 15px rgba(0,0,0,0.5), 0 2px 15px rgba(0,0,0,0.2);
            padding: 20px;
            border-radius: 15px 0 0 15px;
        `;
        document.querySelector('.settings-details').style.cssText = `
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            border-radius: 0 15px 15px 0;
            box-shadow: -2px 0 10px rgba(0, 0, 0, 0.2);
            background-color: #f9f9f9;
        `;
    }

    window.showSection = function(section) {
        const detailsDiv = document.getElementById('settings-details');
        switch(section) {
            case 'teams':
                fetchTeams(detailsDiv);
                break;
            case 'clients':
                fetchClients(detailsDiv);
                break;
            case 'accounts':
                fetchAccounts(detailsDiv);
                break;
            default:
                detailsDiv.innerHTML = '<p>Section inconnue.</p>';
        }
    };

    function fetchTeams(container) {
        fetch('get_teams.php')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    container.innerHTML = `<p>${data.error}</p>`;
                } else {
                    container.innerHTML = `<div class="header-with-button">
                        <h1>Gestion des Équipes</h1>
                        <button id="btnAddTeam" class="btn-add">Ajouter une équipe</button>
                    </div>`;
                    data.forEach(team => {
                        const teamDiv = document.createElement('div');
                        teamDiv.classList.add('team');
                        teamDiv.style.borderLeft = `10px solid ${team.Couleur}`;
                        teamDiv.innerHTML = `
                            <h2>${team.Nom_Equipe}</h2>
                            <h3>Intervenants:</h3>
                            <ul>${team.Intervenants.map(intervenant => `<li>${intervenant}</li>`).join('')}</ul>
                        `;
                        teamDiv.addEventListener('click', () => openEditTeamOverlay(team));
                        container.appendChild(teamDiv);
                    });

                    document.getElementById('btnAddTeam').addEventListener('click', openAddTeamOverlay);
                }
            })
            .catch(error => {
                container.innerHTML = `<p>Erreur lors du chargement des équipes: ${error.message}</p>`;
            });
    }

    function fetchClients(container) {
        fetch('get_clients.php')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    container.innerHTML = `<p>${data.error}</p>`;
                } else {
                    container.innerHTML = `<div class="header-with-button">
                        <h1>Gestion des Clients</h1>
                    </div><div class="clients-grid">`;
                    data.forEach(client => {
                        const clientDiv = document.createElement('div');
                        clientDiv.classList.add('client');
                        clientDiv.innerHTML = `
                            <h2>${client.Prenom} ${client.Nom}</h2>
                        `;
                        clientDiv.addEventListener('click', () => openEditClientOverlay(client));
                        container.querySelector('.clients-grid').appendChild(clientDiv);
                    });
                }
            })
            .catch(error => {
                container.innerHTML = `<p>Erreur lors du chargement des clients: ${error.message}</p>`;
            });
    }

    function fetchAccounts(container) {
        fetch('get_accounts.php')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    container.innerHTML = `<p>${data.error}</p>`;
                } else {
                    container.innerHTML = `<div class="header-with-button">
                        <h1>Gestion des Comptes</h1>
                        <button id="btnAddAccount" class="btn-add">Ajouter un compte</button>
                    </div><div class="account-list">`;
                    data.forEach(account => {
                        const accountDiv = document.createElement('div');
                        accountDiv.classList.add('account');
                        accountDiv.innerHTML = `
                            <p>Nom: ${account.LastName}</p>
                            <p>Prénom: ${account.FirstName}</p>
                            <p>Identifiant: ${account.Identifiant}</p>
                            <p>Rôle: ${account.RoleName}</p>
                        `;
                        accountDiv.addEventListener('click', () => openEditAccountOverlay(account));
                        container.querySelector('.account-list').appendChild(accountDiv);
                    });

                    document.getElementById('btnAddAccount').addEventListener('click', openAddAccountOverlay);
                }
            })
            .catch(error => {
                container.innerHTML = `<p>Erreur lors du chargement des comptes: ${error.message}</p>`;
            });
    }

    function openEditTeamOverlay(team) {
        const teamNameInput = editTeamOverlay.querySelector('#editTeamName');
        const teamColorInput = editTeamOverlay.querySelector('#editTeamColor');
        const intervenantsDropdown = editTeamOverlay.querySelector('#intervenantsDropdown');

        teamNameInput.value = team.Nom_Equipe;
        teamColorInput.value = team.Couleur;
        teamNameInput.dataset.teamId = team.ID_Equipe;

        fetch('get_intervenants.php')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    intervenantsDropdown.innerHTML = `<option>${data.error}</option>`;
                } else {
                    intervenantsDropdown.innerHTML = data.map(intervenant => {
                        const selected = team.Intervenants.includes(`${intervenant.Prenom} ${intervenant.Nom}`) ? 'selected' : '';
                        return `<option value="${intervenant.ID_Intervenant}" ${selected}>${intervenant.Prenom} ${intervenant.Nom}</option>`;
                    }).join('');
                }
            })
            .catch(error => {
                intervenantsDropdown.innerHTML = `<option>Erreur lors du chargement des intervenants: ${error.message}</option>`;
            });

        editTeamOverlay.style.display = 'flex';
    }

    function openAddTeamOverlay() {
        const teamNameInput = editTeamOverlay.querySelector('#editTeamName');
        const teamColorInput = editTeamOverlay.querySelector('#editTeamColor');
        const intervenantsDropdown = editTeamOverlay.querySelector('#intervenantsDropdown');

        teamNameInput.value = '';
        teamColorInput.value = '#000000';
        teamNameInput.dataset.teamId = '';

        fetch('get_intervenants.php')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    intervenantsDropdown.innerHTML = `<option>${data.error}</option>`;
                } else {
                    intervenantsDropdown.innerHTML = data.map(intervenant => {
                        return `<option value="${intervenant.ID_Intervenant}">${intervenant.Prenom} ${intervenant.Nom}</option>`;
                    }).join('');
                }
            })
            .catch(error => {
                intervenantsDropdown.innerHTML = `<option>Erreur lors du chargement des intervenants: ${error.message}</option>`;
            });

        editTeamOverlay.style.display = 'flex';
    }

    function openEditClientOverlay(client) {
        const clientNameInput = editClientOverlay.querySelector('#editClientName');
        const clientFirstNameInput = editClientOverlay.querySelector('#editClientFirstName');
        const clientEmailInput = editClientOverlay.querySelector('#editClientEmail');
        const clientPhoneMobileInput = editClientOverlay.querySelector('#editClientPhoneMobile');
        const clientPhoneFixedInput = editClientOverlay.querySelector('#editClientPhoneFixed');

        clientNameInput.value = client.Nom;
        clientFirstNameInput.value = client.Prenom;
        clientEmailInput.value = client.Email;
        clientPhoneMobileInput.value = client.TelephoneMobile;
        clientPhoneFixedInput.value = client.TelephoneFixe;
        clientNameInput.dataset.clientId = client.ID_Client;

        editClientOverlay.style.display = 'flex';
    }

    function openEditAccountOverlay(account) {
        document.getElementById('accountIdInput').value = account.UserID;
        document.getElementById('editAccountFirstName').value = account.FirstName;
        document.getElementById('editAccountLastName').value = account.LastName;
        document.getElementById('editAccountIdentifiant').value = account.Identifiant;
        document.getElementById('editAccountPassword').value = account.PasswordPlain || ''; // Display the plain password
        document.getElementById('editAccountRole').value = account.RoleID;

        editAccountOverlay.style.display = 'flex';
    }

    function openAddAccountOverlay() {
        document.getElementById('accountIdInput').value = '';
        document.getElementById('editAccountFirstName').value = '';
        document.getElementById('editAccountLastName').value = '';
        document.getElementById('editAccountIdentifiant').value = '';
        document.getElementById('editAccountPassword').value = '';
        document.getElementById('editAccountRole').value = '1';

        editAccountOverlay.style.display = 'flex';
    }

    function closeSettingsOverlay() {
        settingsOverlay.style.display = 'none';
        document.getElementById('agenda-container').classList.remove('blurred');
        location.reload(); // Refresh the agenda when closing the settings overlay
    }

    // Close edit team overlay by clicking outside
    editTeamOverlay.addEventListener('click', function(event) {
        if (event.target === editTeamOverlay) {
            editTeamOverlay.style.display = 'none';
        }
    });

    // Close edit client overlay by clicking outside
    editClientOverlay.addEventListener('click', function(event) {
        if (event.target === editClientOverlay) {
            editClientOverlay.style.display = 'none';
        }
    });

    // Close edit account overlay by clicking outside
    editAccountOverlay.addEventListener('click', function(event) {
        if (event.target === editAccountOverlay) {
            editAccountOverlay.style.display = 'none';
        }
    });

    const closeEditTeamButton = document.getElementById('closeEditTeamButton');
    closeEditTeamButton.addEventListener('click', function() {
        editTeamOverlay.style.display = 'none';
    });

    const closeEditClientButton = document.getElementById('closeEditClientButton');
    closeEditClientButton.addEventListener('click', function() {
        editClientOverlay.style.display = 'none';
    });

    const closeEditAccountButton = document.getElementById('closeEditAccountButton');
    closeEditAccountButton.addEventListener('click', function() {
        editAccountOverlay.style.display = 'none';
    });
});
