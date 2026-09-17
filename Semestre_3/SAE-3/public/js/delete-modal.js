function openDeleteModal(type, id, name) {
    const modal = document.getElementById('deleteModal');
    const message = document.getElementById('deleteMessage');
    const deleteId = document.getElementById('deleteId');
    
    // Message personnalisé
    let typeText = type === 'film' ? 'le film' : type === 'acteur' ? "l'acteur" : 'le réalisateur';
    message.textContent = `Êtes-vous sûr de vouloir supprimer ${typeText} "${name}" ?`;
    
    deleteId.value = id;

    modal.style.display = 'block';
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.style.display = 'none';
}

// Fermer en cliquant en dehors
window.onclick = function(event) {
    const modal = document.getElementById('deleteModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}
