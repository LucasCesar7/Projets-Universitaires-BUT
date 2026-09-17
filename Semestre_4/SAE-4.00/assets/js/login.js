// login.js - Gestion dynamique du formulaire de connexion
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('form');
    const passwordInput = document.getElementById('mot_de_passe');
    const emailInput = document.getElementById('email');
    const code2FAInput = document.getElementById('code_verification');
    
    // Validation en temps réel de l'email
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            const email = this.value.trim();
            if (email && !isValidEmail(email)) {
                showFieldError(this, 'Format d\'email invalide');
            } else {
                clearFieldError(this);
            }
        });
    }
    
    // Validation du code 2FA
    if (code2FAInput) {
        code2FAInput.addEventListener('input', function() {
            const code = this.value;
            if (code.length === 6 && !/^\d{6}$/.test(code)) {
                showFieldError(this, 'Le code doit contenir 6 chiffres');
            } else {
                clearFieldError(this);
            }
        });
        
        // Auto-soumettre quand le code est complet
        code2FAInput.addEventListener('keyup', function() {
            if (this.value.length === 6) {
                this.blur();
                setTimeout(() => {
                    loginForm.submit();
                }, 500);
            }
        });
    }
    
    // Validation du formulaire avant soumission
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Validation de l'email
            if (emailInput && !isValidEmail(emailInput.value.trim())) {
                showFieldError(emailInput, 'Format d\'email invalide');
                isValid = false;
            }
            
            // Validation du mot de passe
            if (passwordInput && passwordInput.value.length < 6) {
                showFieldError(passwordInput, 'Minimum 6 caractères');
                isValid = false;
            }
            
            // Validation du code 2FA si présent
            if (code2FAInput && code2FAInput.value) {
                if (!/^\d{6}$/.test(code2FAInput.value)) {
                    showFieldError(code2FAInput, 'Code 2FA invalide (6 chiffres requis)');
                    isValid = false;
                }
            }
            
            if (!isValid) {
                e.preventDefault();
                showToast('Veuillez corriger les erreurs', 'error');
            } else {
                // Afficher un loader pendant la soumission
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Connexion en cours...';
                    submitBtn.disabled = true;
                }
            }
        });
    }
    
    // Fonctions utilitaires
    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    
    function showFieldError(input, message) {
        clearFieldError(input);
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error';
        errorDiv.style.color = '#e74c3c';
        errorDiv.style.fontSize = '0.85rem';
        errorDiv.style.marginTop = '5px';
        errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
        
        input.parentNode.appendChild(errorDiv);
        input.style.borderColor = '#e74c3c';
    }
    
    function clearFieldError(input) {
        const errorDiv = input.parentNode.querySelector('.field-error');
        if (errorDiv) {
            errorDiv.remove();
        }
        input.style.borderColor = '#e0e0e0';
    }
    
    function showToast(message, type = 'info') {
        // Créer ou réutiliser le conteneur de toast
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container';
            toastContainer.style.position = 'fixed';
            toastContainer.style.top = '20px';
            toastContainer.style.right = '20px';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }
        
        // Créer le toast
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.style.padding = '15px 20px';
        toast.style.marginBottom = '10px';
        toast.style.borderRadius = '8px';
        toast.style.color = 'white';
        toast.style.fontWeight = '500';
        toast.style.boxShadow = '0 5px 15px rgba(0,0,0,0.2)';
        toast.style.transform = 'translateX(100%)';
        toast.style.transition = 'transform 0.3s';
        
        // Couleurs selon le type
        const colors = {
            'success': '#27ae60',
            'error': '#e74c3c',
            'info': '#3498db',
            'warning': '#f39c12'
        };
        
        toast.style.backgroundColor = colors[type] || colors.info;
        toast.innerHTML = message;
        
        toastContainer.appendChild(toast);
        
        // Animation d'entrée
        setTimeout(() => {
            toast.style.transform = 'translateX(0)';
        }, 10);
        
        // Auto-destruction après 5 secondes
        setTimeout(() => {
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 5000);
    }
    
    // Gestion du mot de passe oublié
    const forgotPasswordLink = document.querySelector('.forgot-password');
    if (forgotPasswordLink) {
        forgotPasswordLink.addEventListener('click', function(e) {
            e.preventDefault();
            
            const email = emailInput ? emailInput.value.trim() : '';
            if (email && !isValidEmail(email)) {
                showToast('Veuillez d\'abord entrer un email valide', 'warning');
                if (emailInput) emailInput.focus();
                return;
            }
            
            // Simuler l'envoi du lien de réinitialisation
            showToast('Lien de réinitialisation envoyé à votre email', 'success');
            
            // En production, ici on ferait un appel AJAX
            // fetch('/api/forgot-password', { method: 'POST', body: JSON.stringify({ email }) })
        });
    }
});