// assets/js/admin-edition-edit.js

document.addEventListener('DOMContentLoaded', function() {
    // Gestion de la suppression de l'édition
    const deleteBtn = document.getElementById('deleteBtn');
    const deleteForm = document.getElementById('deleteEditionForm');
    if (deleteBtn) {
        deleteBtn.addEventListener('click', function() {
            const name = this.getAttribute('data-name');
            
            if (confirm(`Voulez-vous vraiment supprimer l'édition "${name}" ?\n\n⚠️ Cette action est irréversible et supprimera toutes les données associées.`)) {
                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Suppression...';

                if (deleteForm) {
                    deleteForm.submit();
                }
            }
        });
    }
    
    // Pré-visualisation de l'image (identique à admin-add-edition.js)
    const fileInput = document.querySelector('input[name="image"]');
    const filePreview = document.getElementById('filePreview');
    
    if (fileInput && filePreview) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                if (!file.type.startsWith('image/')) {
                    alert('Veuillez sélectionner une image valide.');
                    fileInput.value = '';
                    return;
                }
                
                if (file.size > 5 * 1024 * 1024) {
                    alert('L\'image ne doit pas dépasser 5MB.');
                    fileInput.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    filePreview.innerHTML = `
                        <div class="preview-container">
                            <img src="${e.target.result}" alt="Preview" style="max-width: 200px; max-height: 150px; border-radius: 6px; margin-top: 10px;">
                            <button type="button" class="remove-preview" style="margin-top: 10px; background: #dc2626; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">
                                Supprimer la nouvelle image
                            </button>
                        </div>
                    `;
                    
                    const removeBtn = filePreview.querySelector('.remove-preview');
                    removeBtn.addEventListener('click', function() {
                        fileInput.value = '';
                        filePreview.innerHTML = '';
                    });
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Validation du formulaire
    const form = document.querySelector('.edition-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const errors = [];
            
            // Validation des dates (identique à admin-add-edition.js)
            const dateDebutCandidatures = document.querySelector('[name="date_debut_candidatures"]').value;
            const dateFinCandidatures = document.querySelector('[name="date_fin_candidatures"]').value;
            const dateDebut = document.querySelector('[name="date_debut"]').value;
            const dateFin = document.querySelector('[name="date_fin"]').value;
            
            if (dateDebutCandidatures && dateFinCandidatures && 
                new Date(dateDebutCandidatures) >= new Date(dateFinCandidatures)) {
                errors.push("La date de fin des candidatures doit être après la date de début.");
                isValid = false;
            }
            
            if (dateFinCandidatures && dateDebut && 
                new Date(dateFinCandidatures) >= new Date(dateDebut)) {
                errors.push("La date de début de l'édition doit être après la fin des candidatures.");
                isValid = false;
            }
            
            if (dateDebut && dateFin && 
                new Date(dateDebut) >= new Date(dateFin)) {
                errors.push("La date de fin de l'édition doit être après la date de début.");
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                alert(errors.join('\n'));
            }
        });
    }
});