// FICHIER : nominees.js
// DESCRIPTION : Gestion dynamique des nominés avec recherche et filtres
// FONCTIONNALITÉ : Recherche en temps réel, filtres par catégorie/plateforme, animations

function getAppBasePath() {
    const path = window.location.pathname;
    const markers = ['/views/', '/assets/', '/config/', '/app/'];

    for (const marker of markers) {
        const index = path.indexOf(marker);
        if (index !== -1) {
            return path.slice(0, index);
        }
    }

    const segments = path.split('/').filter(Boolean);
    if (segments.length > 1) {
        return `/${segments.slice(0, -1).join('/')}`;
    }

    return '';
}

function appUrl(path = '') {
    const normalizedPath = path.replace(/^\/+/, '');
    const basePath = getAppBasePath();

    return normalizedPath ? `${basePath}/${normalizedPath}` : (basePath || '/');
}

document.addEventListener('DOMContentLoaded', function() {
    // Éléments DOM
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const platformFilter = document.getElementById('platformFilter');
    const nomineeCards = document.querySelectorAll('.nominee-card');
    const nomineesBaseUrl = appUrl('nominees');
    const nomineesLoginUrl = appUrl('login');

    function navigateWithFilters(categoryId = '', platform = '') {
        const url = new URL(nomineesBaseUrl, window.location.origin);
        const currentParams = new URLSearchParams(window.location.search);
        const currentPerPage = currentParams.get('per_page');

        if (categoryId && categoryId !== '0') {
            const categoryName = categoryFilter ? categoryFilter.options[categoryFilter.selectedIndex].text : '';
            url.searchParams.set('category', categoryId);
            url.searchParams.set('name', categoryName);
        }

        if (platform) {
            url.searchParams.set('platform', platform);
        }

        if (currentPerPage && !Number.isNaN(Number(currentPerPage))) {
            url.searchParams.set('per_page', currentPerPage);
        }

        window.location.href = url.toString();
    }
    
    // Initialisation des filtres
    function initFilters() {
        // Recherche en temps réel
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                filterNominees(searchTerm, null, null);
            });
        }
        
        // Filtre par catégorie (changement de page via select)
        if (categoryFilter) {
            categoryFilter.addEventListener('change', function() {
                const categoryId = this.value;
                navigateWithFilters(categoryId, platformFilter ? platformFilter.value : '');
            });
        }
        
        // Filtre par plateforme (navigation canonique)
        if (platformFilter) {
            platformFilter.addEventListener('change', function() {
                navigateWithFilters(categoryFilter ? categoryFilter.value : '', this.value);
            });
        }
    }
    
    // Fonction de filtrage combiné
    function filterNominees(searchTerm = '', platform = '', category = '') {
        nomineeCards.forEach(card => {
            const nomineeName = card.querySelector('h3').textContent.toLowerCase();
            const nomineeCategory = card.getAttribute('data-category') || '';
            const nomineePlatform = card.getAttribute('data-platform') || '';
            const nomineeDataName = card.getAttribute('data-name') || '';
            
            // Conditions de filtrage
            const matchesSearch = !searchTerm || 
                                 nomineeName.includes(searchTerm) || 
                                 nomineeDataName.includes(searchTerm);
            
            const matchesPlatform = !platform || nomineePlatform === platform;
            const matchesCategory = !category || nomineeCategory === category;
            
            // Appliquer l'affichage
            if (matchesSearch && matchesPlatform && matchesCategory) {
                showCard(card);
            } else {
                hideCard(card);
            }
        });
        
        // Afficher/masquer le message "aucun résultat"
        updateNoResultsMessage();
    }
    
    // Animation pour afficher une carte
    function showCard(card) {
        if (card.style.display === 'none') {
            card.style.display = 'block';
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 50);
        }
    }
    
    // Animation pour masquer une carte
    function hideCard(card) {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        
        setTimeout(() => {
            card.style.display = 'none';
        }, 300);
    }
    
    // Mettre à jour le message "aucun résultat"
    function updateNoResultsMessage() {
        const visibleCards = Array.from(nomineeCards).filter(card => 
            card.style.display !== 'none'
        ).length;
        
        let noResultsMsg = document.getElementById('noResultsMessage');
        
        if (visibleCards === 0 && nomineeCards.length > 0) {
            if (!noResultsMsg) {
                noResultsMsg = document.createElement('div');
                noResultsMsg.id = 'noResultsMessage';
                noResultsMsg.className = 'no-results-message';
                noResultsMsg.innerHTML = `
                    <i class="fas fa-search"></i>
                    <h3>Aucun résultat trouvé</h3>
                    <p>Essayez avec d'autres termes de recherche ou filtres.</p>
                `;
                
                // Styles inline
                noResultsMsg.style.cssText = `
                    text-align: center;
                    padding: 40px 20px;
                    color: var(--gray);
                    display: none;
                    animation: fadeIn 0.5s ease;
                `;
                
                const nomineesGrid = document.querySelector('.nominees-grid');
                if (nomineesGrid) {
                    nomineesGrid.parentNode.insertBefore(noResultsMsg, nomineesGrid.nextSibling);
                }
            }
            
            setTimeout(() => {
                noResultsMsg.style.display = 'block';
            }, 300);
        } else if (noResultsMsg) {
            noResultsMsg.style.display = 'none';
        }
    }
    
    // Gestion des boutons de vote
    function initVoteButtons() {
        const voteButtons = document.querySelectorAll('.btn-vote');
        
        voteButtons.forEach(button => {
            // Événement pour les boutons désactivés
            if (button.classList.contains('btn-disabled')) {
                button.addEventListener('click', function(e) {
                    e.preventDefault();

                    if (button.getAttribute('data-voting-open') === 'false') {
                        alert('Les votes sont fermés pour cette catégorie.');
                        return;
                    }
                    
                    // Message selon l'état d'authentification
                    const isAuthenticated = button.getAttribute('data-authenticated') === 'true';
                    
                    if (!isAuthenticated) {
                        if (confirm('Vous devez être connecté pour voter. Voulez-vous vous connecter maintenant?')) {
                            const currentPage = window.location.pathname + window.location.search;
                            window.location.href = nomineesLoginUrl + '?redirect=' + encodeURIComponent(currentPage);
                        }
                    } else {
                        alert('Seuls les électeurs peuvent voter. Votre compte n\'a pas les permissions nécessaires.');
                    }
                });
            }
            
            // Événement pour les boutons actifs
            else {
                button.addEventListener('click', function(e) {
                    if (button.getAttribute('data-voting-open') === 'false') {
                        e.preventDefault();
                        alert('Les votes sont fermés pour cette catégorie.');
                        return;
                    }

                    const nomineeCard = this.closest('.nominee-card');
                    const nomineeName = nomineeCard.querySelector('h3').textContent;
                    
                    // Confirmation de vote
                    if (!confirm(`Confirmez-vous votre vote pour "${nomineeName}" ?`)) {
                        e.preventDefault();
                        return;
                    }
                    
                    // Animation de vote
                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Vote en cours...';
                    this.disabled = true;
                    
                    // Simulation d'envoi (à remplacer par appel AJAX réel)
                    setTimeout(() => {
                        this.innerHTML = '<i class="fas fa-check"></i> Vote envoyé!';
                        this.style.background = 'var(--success-color, #28a745)';
                        
                        // Notification
                        showNotification(`Vote pour ${nomineeName} enregistré !`, 'success');
                        
                        // Réinitialisation après 3 secondes
                        setTimeout(() => {
                            this.innerHTML = originalText;
                            this.disabled = false;
                            this.style.background = '';
                        }, 3000);
                    }, 1500);
                });
            }
        });
    }
    
    // Notification système
    function showNotification(message, type = 'info') {
        // Créer le conteneur de notifications s'il n'existe pas
        let container = document.querySelector('.notification-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'notification-container';
            container.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
            `;
            document.body.appendChild(container);
        }
        
        // Créer la notification
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-info-circle'}"></i>
                <span>${message}</span>
            </div>
            <button class="notification-close">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        // Styles
        notification.style.cssText = `
            background: ${type === 'success' ? '#28a745' : '#17a2b8'};
            color: white;
            padding: 15px 20px;
            margin-bottom: 10px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-width: 300px;
            max-width: 400px;
            animation: slideInRight 0.3s ease;
            transform: translateX(100%);
        `;
        
        // Animation d'entrée
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 10);
        
        // Bouton de fermeture
        const closeBtn = notification.querySelector('.notification-close');
        closeBtn.addEventListener('click', () => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 300);
        });
        
        // Auto-destruction après 5 secondes
        setTimeout(() => {
            if (notification.parentNode) {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => notification.remove(), 300);
            }
        }, 5000);
        
        container.appendChild(notification);
    }
    
    // Animations au défilement
    function initScrollAnimations() {
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
        
        // Observer chaque carte
        nomineeCards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });
    }
    
    // Effets de survol améliorés
    function initHoverEffects() {
        nomineeCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px)';
                this.style.boxShadow = '0 15px 40px rgba(79, 189, 171, 0.3)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 10px 30px rgba(0, 0, 0, 0.15)';
            });
        });
    }
    
    // Initialisation complète
    function init() {
        initFilters();
        initVoteButtons();
        initScrollAnimations();
        initHoverEffects();
        
        // Vérifier les paramètres URL pour les filtres
        const urlParams = new URLSearchParams(window.location.search);
        const categoryParam = urlParams.get('category');
        const searchParam = urlParams.get('search');
        
        if (searchParam && searchInput) {
            searchInput.value = searchParam;
            filterNominees(searchParam, null, null);
        }
        
        // Ajouter des styles CSS dynamiques si nécessaire
        if (!document.querySelector('#nominees-dynamic-styles')) {
            const style = document.createElement('style');
            style.id = 'nominees-dynamic-styles';
            style.textContent = `
                @keyframes slideInRight {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                
                @keyframes fadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
                
                .no-results-message {
                    animation: fadeIn 0.5s ease;
                }
                
                .notification-close {
                    background: none;
                    border: none;
                    color: white;
                    cursor: pointer;
                    padding: 5px;
                    margin-left: 10px;
                    border-radius: 4px;
                    transition: background 0.3s ease;
                }
                
                .notification-close:hover {
                    background: rgba(255, 255, 255, 0.2);
                }
                
                /* Responsivité améliorée */
                @media (max-width: 768px) {
                    .search-filter {
                        flex-direction: column;
                    }
                    
                    .search-filter > * {
                        width: 100%;
                        margin-bottom: 10px;
                    }
                }
            `;
            document.head.appendChild(style);
        }
    }
    
    // Démarrer l'initialisation
    init();
    
    // Exporter des fonctions pour utilisation globale (si nécessaire)
    window.nomineesApp = {
        filterNominees,
        showNotification,
        updateNoResultsMessage
    };
});