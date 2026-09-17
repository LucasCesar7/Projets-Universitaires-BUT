document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modal-vote-confirm');
    const closeBtn = document.getElementById('close-vote-modal');
    const voteButtons = document.querySelectorAll('.btn-vote');
    
    const modalType = document.getElementById('modal-type');
    const modalId = document.getElementById('modal-id');
    const modalElementName = document.getElementById('modal-element-name');
    const modalCategorieSelect = document.getElementById('modal-categorie-select');
    const modalQuestion = document.getElementById('modal-vote-question');

    // Ouvrir la modal au clic sur "Voter"
    voteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const type = this.dataset.type;
            const id = this.dataset.id;
            const name = this.dataset.name;
            const categories = this.dataset.categorie || '';
            
            // Remplir les champs cachés
            modalType.value = type;
            modalId.value = id;
            modalElementName.textContent = name;
            
            // Charger les catégories disponibles pour cet élément
            loadCategories(type, id, categories);
            
            // Afficher la modal
            modal.style.display = 'block';
        });
    });

    // Fermer la modal
    closeBtn.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });

    // Fonction pour charger les catégories
    function loadCategories(type, id, categories) {
        // Vider le select CORRECTEMENT
        modalCategorieSelect.innerHTML = '<option value="">-- Sélectionnez une catégorie --</option>';
        
        // Faire une requête AJAX pour récupérer les catégories disponibles
        fetch(`/sae-3-festivote-tas-cesar/app/views/vote/get-categories.php?type=${type}&id=${id}`)
            .then(response => response.json())
            .then(data => {
                console.log('Réponse reçue:', data); // DEBUG
                
                if (data.success && data.categories.length > 0) {
                    data.categories.forEach(cat => {
                        const option = document.createElement('option');
                        option.value = cat.id_categorie;
                        option.textContent = cat.libelle;
                        option.disabled = cat.already_voted; // Désactiver si déjà voté
                        if (cat.already_voted) {
                            option.textContent += ' (Déjà voté)';
                        }
                        modalCategorieSelect.appendChild(option);
                    });
                } else {
                    modalCategorieSelect.innerHTML = '<option value="">Aucune catégorie disponible</option>';
                }
            })
            .catch(error => {
                console.error('Erreur lors du chargement des catégories:', error);
                modalCategorieSelect.innerHTML = '<option value="">Erreur de chargement</option>';
            });
    }
});
