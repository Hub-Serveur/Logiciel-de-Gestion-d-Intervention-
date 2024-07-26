// Fonction pour basculer l'affichage du menu déroulant
// Auteur: Nathan DAMASSE
function toggleDropdown() {
    document.getElementById("dropdown").classList.toggle("show");
}

// Fermer le dropdown si l'utilisateur clique en dehors de celui-ci
window.onclick = function(event) {
    if (!event.target.matches('.user-name')) {
        var dropdowns = document.getElementsByClassName("dropdown-content");
        for (var i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('show')) {
                openDropdown.classList.remove('show');
            }
        }
    }
}
