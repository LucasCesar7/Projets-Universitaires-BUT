// assets/js/admin-editions.js

document.addEventListener('DOMContentLoaded', function() {
    // Fonction de confirmation de suppression
    window.confirmDelete = function(id, name) {
        return confirm(`Voulez-vous vraiment supprimer l'édition "${name}" ?\n\n⚠️ Cette action supprimera également toutes les catégories et candidatures associées.`);
    };
});