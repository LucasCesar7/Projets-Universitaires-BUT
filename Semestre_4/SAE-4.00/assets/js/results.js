// FICHIER : results.js
// DESCRIPTION : Gestion dynamique de la page des résultats avec filtres et interactions
// FONCTIONNALITÉ : Filtrage par plateforme, animations et chargement d'édition

document.addEventListener('DOMContentLoaded', function() {
    // Éléments DOM
    const tabButtons = document.querySelectorAll('.tab-btn');
    const resultCards = document.querySelectorAll('.result-card');
    const editionSelect = document.getElementById('editionSelect');
    const loadingIndicator = document.getElementById('loadingIndicator');
    
    // Filtrage par plateforme
    function setupPlatformFilters() {
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Retirer la classe active de tous les boutons
                tabButtons.forEach(btn => btn.classList.remove('active'));
                
                // Ajouter la classe active au bouton cliqué
                this.classList.add('active');
                
                const filterValue = this.getAttribute('data-tab');
                
                // Filtrer les cartes de résultats
                filterResultCards(filterValue);
            });
        });
    }
    
    // Fonction de filtrage des cartes
    function filterResultCards(filterValue) {
        resultCards.forEach(card => {
            const cardPlatform = card.getAttribute('data-platform') || 'all';
            
            if (filterValue === 'all' || cardPlatform === filterValue) {
                card.style.display = 'block';
                // Animation d'apparition
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                    card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                }, 50);
            } else {
                // Animation de disparition
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                setTimeout(() => {
                    card.style.display = 'none';
                }, 300);
            }
        });
    }
    
    // Gestion du changement d'édition
    function setupEditionSelector() {
        if (!editionSelect) return;
        
        editionSelect.addEventListener('change', function() {
            const editionId = this.value;
            const editionName = this.options[this.selectedIndex].text;
            
            // Afficher l'indicateur de chargement
            if (loadingIndicator) {
                loadingIndicator.classList.add('active');
                loadingIndicator.innerHTML = `
                    <i class="fas fa-spinner fa-spin"></i> 
                    Chargement des résultats pour ${editionName}...
                `;
            }
            
            // Simuler un chargement (dans une version future, ce serait une requête AJAX)
            setTimeout(() => {
                // Ici, normalement on ferait un appel AJAX ou une redirection
                console.log(`Chargement des données pour l'édition ${editionId}`);
                
                // Pour l'instant, on redirige avec un paramètre GET
                window.location.href = `results?edition=${editionId}`;
            }, 500);
        });
    }
    
    // Animations au défilement
    function setupScrollAnimations() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observer les éléments animables
        const animatedElements = document.querySelectorAll('.result-card, .stat-card, .top-winner, .grand-winner');
        
        animatedElements.forEach(element => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(30px)';
            element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(element);
        });
    }
    
    // Effets de survol pour les gagnants
    function setupHoverEffects() {
        const winnerItems = document.querySelectorAll('.winner-item');
        
        winnerItems.forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.transform = 'translateX(5px) scale(1.02)';
                this.style.boxShadow = '0 10px 25px rgba(0,0,0,0.15)';
            });
            
            item.addEventListener('mouseleave', function() {
                this.style.transform = 'translateX(0) scale(1)';
                this.style.boxShadow = 'none';
            });
        });
    }
    
    // Initialiser toutes les fonctionnalités
    function init() {
        setupPlatformFilters();
        setupEditionSelector();
        setupScrollAnimations();
        setupHoverEffects();
        
        // Vérifier s'il y a un paramètre d'édition dans l'URL
        const urlParams = new URLSearchParams(window.location.search);
        const editionParam = urlParams.get('edition');
        
        if (editionParam && editionSelect) {
            editionSelect.value = editionParam;
        }
        
        // Afficher un message si aucune catégorie n'est disponible
        const noCategoriesElement = document.querySelector('.no-categories');
        const noResultsElement = document.querySelector('.no-results');
        
        if (noCategoriesElement || noResultsElement) {
            setTimeout(() => {
                if (noCategoriesElement) {
                    noCategoriesElement.style.opacity = '1';
                    noCategoriesElement.style.transform = 'translateY(0)';
                }
                if (noResultsElement) {
                    noResultsElement.style.opacity = '1';
                    noResultsElement.style.transform = 'translateY(0)';
                }
            }, 1000);
        }
    }
    
    // Démarrer l'initialisation
    init();
    
    // Gestionnaire pour le rafraîchissement automatique (optionnel)
    let autoRefreshInterval = null;
    
    function setupAutoRefresh(intervalMinutes = 5) {
        // Désactiver le rafraîchissement automatique par défaut
        // Pour l'activer, appeler setupAutoRefresh(5) pour rafraîchir toutes les 5 minutes
        if (intervalMinutes > 0) {
            autoRefreshInterval = setInterval(() => {
                console.log('Rafraîchissement automatique des résultats...');
                // Ici on pourrait faire un appel AJAX pour mettre à jour sans recharger la page
            }, intervalMinutes * 60 * 1000);
        }
    }
    
    // Désactiver le rafraîchissement automatique quand la page n'est pas visible
    document.addEventListener('visibilitychange', function() {
        if (document.hidden && autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
            autoRefreshInterval = null;
        } else if (!document.hidden && !autoRefreshInterval) {
            // setupAutoRefresh(5); // Réactiver si besoin
        }
    });
});