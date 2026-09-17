// confirm-delete-nomination.js
document.addEventListener('DOMContentLoaded', function() {
    // Encontrar todos os formulários de exclusão
    const deleteForms = document.querySelectorAll('form[action*="delete-nomination"]');
    
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const nominationId = this.querySelector('input[name="id"]').value;
            const nominationTitle = this.closest('tr').querySelector('td:nth-child(2) strong').textContent;
            
            // Exibir modal de confirmação personalizado
            showDeleteConfirmation(nominationId, nominationTitle, this);
        });
    });
    
    // Modal de confirmação
    function showDeleteConfirmation(id, title, form) {
        // Criar modal
        const modal = document.createElement('div');
        modal.className = 'custom-modal';
        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h3><i class="fas fa-exclamation-triangle text-warning"></i> Confirmer la suppression</h3>
                    <button class="modal-close">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer la nomination suivante ?</p>
                    <div class="alert alert-warning">
                        <strong>"${title}"</strong>
                    </div>
                    <p class="text-danger">
                        <i class="fas fa-exclamation-circle"></i> Cette action est irréversible.
                    </p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" id="cancelDelete">Annuler</button>
                    <button class="btn btn-danger" id="confirmDelete">
                        <i class="fas fa-trash"></i> Supprimer définitivement
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Mostrar modal com animação
        setTimeout(() => modal.classList.add('active'), 10);
        
        // Event listeners
        modal.querySelector('.modal-close').addEventListener('click', () => closeModal(modal));
        modal.querySelector('#cancelDelete').addEventListener('click', () => closeModal(modal));
        
        modal.querySelector('#confirmDelete').addEventListener('click', () => {
            // Adicionar indicação de carregamento
            const confirmBtn = modal.querySelector('#confirmDelete');
            confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Suppression...';
            confirmBtn.disabled = true;
            
            // Enviar formulário
            form.submit();
        });
        
        // Fechar ao clicar fora
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal(modal);
            }
        });
    }
    
    function closeModal(modal) {
        modal.classList.remove('active');
        setTimeout(() => modal.remove(), 300);
    }
});