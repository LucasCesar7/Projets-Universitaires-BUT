// FICHIER : categories.js
// DESCRIPTION : Gestion dynamique des catégories avec filtres
// FONCTIONNALITÉ : Filtrage par plateforme et animations

document.addEventListener('DOMContentLoaded', function() {
    // Filtre par plateforme
    const filterButtons = document.querySelectorAll('.filter-btn');
    const categoryCards = document.querySelectorAll('.category-card');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Mettre à jour l'état actif
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            const filterValue = this.getAttribute('data-filter');
            
            // Filtrer les cartes
            categoryCards.forEach(card => {
                if (filterValue === 'all') {
                    card.style.display = 'block';
                    animateCard(card, true);
                } else {
                    const platforms = card.getAttribute('data-platform');
                    if (platforms && platforms.includes(filterValue)) {
                        card.style.display = 'block';
                        animateCard(card, true);
                    } else {
                        animateCard(card, false);
                    }
                }
            });
        });
    });
    
    // Animation des cartes
    function animateCard(card, show) {
        if (show) {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 50);
        } else {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.display = 'none';
            }, 300);
        }
    }
    
    // Animation initiale
    categoryCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    // Bouton "Voir les Nominés"
    const viewButtons = document.querySelectorAll('.btn-view-nominees');
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const categoryId = this.getAttribute('data-category-id');
            const categoryName = this.getAttribute('data-category-name');
            
            if (categoryId && categoryId !== '0') {
                // Animation de clic
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Chargement...';
                
                // Redirection après un délai
                setTimeout(() => {
                    window.location.href = `nominees?category=${categoryId}&name=${encodeURIComponent(categoryName)}`;
                }, 500);
            }
        });
    });
    
    // Gestion des états vides
    const noCategoriesElement = document.querySelector('.no-categories-message');
    if (noCategoriesElement) {
        setTimeout(() => {
            noCategoriesElement.style.opacity = '1';
            noCategoriesElement.style.transform = 'translateY(0)';
        }, 500);
    }
});