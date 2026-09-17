// Gestion du tableau de bord administratif
document.addEventListener('DOMContentLoaded', function() {
    // Menu mobile
    const mobileMenuBtn = document.createElement('button');
    mobileMenuBtn.className = 'mobile-menu-btn';
    mobileMenuBtn.innerHTML = '<i class="fas fa-bars"></i>';
    document.querySelector('.admin-header').prepend(mobileMenuBtn);
    
    mobileMenuBtn.addEventListener('click', function() {
        document.querySelector('.sidebar').classList.toggle('active');
    });
    
    // Fermer le menu en cliquant à l'extérieur
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.sidebar') && !e.target.closest('.mobile-menu-btn')) {
            document.querySelector('.sidebar').classList.remove('active');
        }
    });
    
    // Validation des formulaires
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    highlightError(field);
                } else {
                    removeError(field);
                }
            });
            
            // Validation spécifique pour les mots de passe
            const password = this.querySelector('#password');
            const confirmPassword = this.querySelector('#confirm_password');
            
            if (password && confirmPassword && password.value && confirmPassword.value) {
                if (password.value !== confirmPassword.value) {
                    isValid = false;
                    highlightError(confirmPassword);
                    showPasswordError(confirmPassword);
                }
            }
            
            if (!isValid) {
                e.preventDefault();
                showNotification('Veuillez remplir tous les champs obligatoires correctement.', 'error');
            }
        });
    });
    
    // Recherche dans les tables
    const searchInputs = document.querySelectorAll('[id^="search"]');
    searchInputs.forEach(input => {
        input.addEventListener('input', function() {
            const tableId = this.id.replace('search', '').toLowerCase();
            const table = document.querySelector(`#${tableId}Table`) || document.querySelector('.data-table');
            const searchTerm = this.value.toLowerCase();
            
            if (table) {
                const rows = table.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            }
        });
    });
    
    // Filtres
    const filters = document.querySelectorAll('[id^="filter"]');
    filters.forEach(filter => {
        filter.addEventListener('change', function() {
            const table = this.closest('.table-container').querySelector('.data-table');
            const rows = table.querySelectorAll('tbody tr');
            const filterValue = this.value;
            
            rows.forEach(row => {
                if (!filterValue) {
                    row.style.display = '';
                    return;
                }
                
                const statusCell = row.querySelector('.status-badge');
                if (statusCell && statusCell.classList.contains(`status-${filterValue}`)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
    
    // Confirmation avant suppression
    const deleteButtons = document.querySelectorAll('.btn-danger');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.')) {
                e.preventDefault();
            }
        });
    });
    
    // Mise à jour du statut
    const statusButtons = document.querySelectorAll('[data-status]');
    statusButtons.forEach(button => {
        button.addEventListener('click', function() {
            const newStatus = this.getAttribute('data-status');
            const itemId = this.getAttribute('data-id');
            const itemType = this.getAttribute('data-type');
            
            if (confirm(`Changer le statut en "${newStatus}" ?`)) {
                updateStatus(itemId, itemType, newStatus);
            }
        });
    });
    
    // Exportation
    const exportButtons = document.querySelectorAll('[data-export]');
    exportButtons.forEach(button => {
        button.addEventListener('click', function() {
            const format = this.getAttribute('data-format');
            const type = this.getAttribute('data-type');
            exportData(type, format);
        });
    });
});

// Fonctions utilitaires
function highlightError(field) {
    field.style.borderColor = '#E74C3C';
    field.style.backgroundColor = '#FDEDED';
}

function removeError(field) {
    field.style.borderColor = '';
    field.style.backgroundColor = '';
}

function showPasswordError(field) {
    let errorMsg = field.nextElementSibling;
    if (!errorMsg || !errorMsg.classList.contains('error-message')) {
        errorMsg = document.createElement('div');
        errorMsg.className = 'error-message';
        field.parentNode.appendChild(errorMsg);
    }
    errorMsg.textContent = 'Les mots de passe ne correspondent pas';
    errorMsg.style.color = '#E74C3C';
    errorMsg.style.fontSize = '0.85rem';
    errorMsg.style.marginTop = '5px';
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
        <button class="close-notification">&times;</button>
    `;
    
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${type === 'success' ? '#4CAF50' : '#F44336'};
        color: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        gap: 10px;
        z-index: 10000;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    // Fermer la notification
    notification.querySelector('.close-notification').addEventListener('click', () => {
        notification.remove();
    });
    
    // Fermer automatiquement après 5 secondes
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

async function updateStatus(id, type, status) {
    try {
        const response = await fetch('/api/update-status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id, type, status })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('Statut mis à jour avec succès', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('Erreur lors de la mise à jour', 'error');
        }
    } catch (error) {
        showNotification('Erreur de connexion', 'error');
    }
}

function exportData(type, format) {
    // Simulation d'export
    showNotification(`Export ${type} en ${format.toUpperCase()} en cours...`, 'success');
    
    setTimeout(() => {
        showNotification(`Export ${type} terminé`, 'success');
    }, 1500);
}