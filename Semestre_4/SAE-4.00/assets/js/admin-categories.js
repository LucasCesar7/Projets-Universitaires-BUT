// admin-categories.js

document.addEventListener('DOMContentLoaded', function() {
    // Elementos DOM
    const searchInput = document.getElementById('searchInput');
    const platformFilter = document.getElementById('platformFilter');
    const editionFilter = document.getElementById('editionFilter');
    const statusFilter = document.getElementById('statusFilter');
    const tableRows = document.querySelectorAll('#categoriesTable tbody tr');
    
    // Filtros
    if (searchInput && tableRows.length > 0) {
        setupFilters();
        setupSorting();
        setupViewButtons();
    }
    
    // Configuração dos filtros
    function setupFilters() {
        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase();
            const platformValue = platformFilter.value;
            const editionValue = editionFilter.value;
            const statusValue = statusFilter.value;
            
            tableRows.forEach(row => {
                const categoryName = row.querySelector('.category-info h4').textContent.toLowerCase();
                const platform = row.getAttribute('data-platform');
                const edition = row.getAttribute('data-edition');
                const status = row.getAttribute('data-status');
                
                const matchesSearch = categoryName.includes(searchTerm);
                const matchesPlatform = !platformValue || platform === platformValue;
                const matchesEdition = !editionValue || edition === editionValue;
                const matchesStatus = !statusValue || status === statusValue;
                
                if (matchesSearch && matchesPlatform && matchesEdition && matchesStatus) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
        
        searchInput.addEventListener('input', filterTable);
        platformFilter.addEventListener('change', filterTable);
        editionFilter.addEventListener('change', filterTable);
        statusFilter.addEventListener('change', filterTable);
    }
    
    // Configuração da ordenação
    function setupSorting() {
        document.querySelectorAll('.enhanced-table th').forEach((header, index) => {
            header.style.cursor = 'pointer';
            header.title = 'Cliquer pour trier';
            header.addEventListener('click', () => {
                sortTable(index);
            });
        });
    }
    
    // Função de ordenação
    function sortTable(columnIndex) {
        const table = document.getElementById('categoriesTable');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        const isAscending = table.getAttribute('data-sort-dir') !== 'asc';
        
        rows.sort((a, b) => {
            let aValue, bValue;
            
            switch(columnIndex) {
                case 0: // Nome
                    aValue = a.querySelector('.category-info h4').textContent;
                    bValue = b.querySelector('.category-info h4').textContent;
                    break;
                case 1: // Plataforma
                    aValue = a.getAttribute('data-platform');
                    bValue = b.getAttribute('data-platform');
                    break;
                case 2: // Edição
                    aValue = a.querySelector('td:nth-child(3) strong').textContent;
                    bValue = b.querySelector('td:nth-child(3) strong').textContent;
                    break;
                case 4: // Status
                    aValue = a.getAttribute('data-status');
                    bValue = b.getAttribute('data-status');
                    break;
                default:
                    return 0;
            }
            
            if (isAscending) {
                return aValue.localeCompare(bValue, 'fr', { sensitivity: 'base' });
            } else {
                return bValue.localeCompare(aValue, 'fr', { sensitivity: 'base' });
            }
        });
        
        // Remove as linhas existentes
        rows.forEach(row => tbody.removeChild(row));
        
        // Adiciona as linhas ordenadas
        rows.forEach(row => tbody.appendChild(row));
        
        // Atualiza o ícone de ordenação
        table.setAttribute('data-sort-dir', isAscending ? 'asc' : 'desc');
        
        // Atualiza os ícones nos cabeçalhos
        updateSortIcons(columnIndex, isAscending);
    }
    
    // Atualiza ícones de ordenação
    function updateSortIcons(columnIndex, isAscending) {
        const headers = document.querySelectorAll('.enhanced-table th');
        headers.forEach((header, index) => {
            header.innerHTML = header.innerHTML.replace(/<i class="fas fa-sort[^"]*"><\/i>/, '');
            if (index === columnIndex) {
                const iconClass = isAscending ? 'fa-sort-up' : 'fa-sort-down';
                header.innerHTML = header.innerHTML.replace(/<i class="fas[^"]*"><\/i>/, '') + 
                    `<i class="fas ${iconClass}"></i>`;
            }
        });
    }
    
    // Configuração dos botões de visualização
    function setupViewButtons() {
        document.querySelectorAll('.action-btn.view').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const row = this.closest('tr');
                const categoryData = {
                    id: row.getAttribute('data-id'),
                    nom: row.querySelector('.category-info h4').textContent,
                    description: row.querySelector('.description')?.textContent || '',
                    plateforme_cible: row.getAttribute('data-platform'),
                    edition_nom: row.querySelector('td:nth-child(3) strong').textContent,
                    nb_candidatures: row.getAttribute('data-candidatures'),
                    nb_nominations: row.getAttribute('data-nominations'),
                    limite_nomines: row.getAttribute('data-limite'),
                    date_debut_votes: row.getAttribute('data-debut'),
                    date_fin_votes: row.getAttribute('data-fin'),
                    edit_url: row.querySelector('.action-btn.edit')?.getAttribute('href') || ''
                };
                showCategoryDetails(categoryData);
            });
        });
    }
});

// Modal de detalhes da categoria
function showCategoryDetails(category) {
    // Remove modal existente
    const existingModal = document.querySelector('.category-modal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Cria novo modal
    const modal = document.createElement('div');
    modal.className = 'category-modal active';
    
    // Formata datas
    const formatDate = (dateString) => {
        if (!dateString) return 'Non défini';
        const date = new Date(dateString);
        return date.toLocaleDateString('fr-FR', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    };
    
    // Calcula status
    let statusText = 'Non défini';
    let statusClass = '';
    if (category.date_fin_votes && category.date_debut_votes) {
        const now = new Date();
        const start = new Date(category.date_debut_votes);
        const end = new Date(category.date_fin_votes);
        
        if (now > end) {
            statusText = 'Terminé';
            statusClass = 'status-ended';
        } else if (now < start) {
            statusText = 'À venir';
            statusClass = 'status-upcoming';
        } else {
            statusText = 'En cours';
            statusClass = 'status-active';
        }
    }
    
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h3>Détails de la Catégorie</h3>
                <button class="modal-close" onclick="closeModal()">×</button>
            </div>
            
            <div class="modal-section">
                <h4>${category.nom}</h4>
                ${category.description ? `<p style="color: #666; line-height: 1.6;">${category.description}</p>` : ''}
            </div>
            
            <div class="modal-grid">
                <div>
                    <span class="modal-label">Plateforme</span>
                    <span class="modal-value">${category.plateforme_cible}</span>
                </div>
                <div>
                    <span class="modal-label">Édition</span>
                    <span class="modal-value">${category.edition_nom}</span>
                </div>
                <div>
                    <span class="modal-label">Candidatures</span>
                    <span class="modal-value">${category.nb_candidatures}</span>
                </div>
                <div>
                    <span class="modal-label">Nominés</span>
                    <span class="modal-value">${category.nb_nominations}</span>
                </div>
                ${category.limite_nomines ? `
                <div>
                    <span class="modal-label">Limite de nominés</span>
                    <span class="modal-value">${category.limite_nomines}</span>
                </div>
                ` : ''}
                <div>
                    <span class="modal-label">Statut</span>
                    <span class="modal-value ${statusClass}">${statusText}</span>
                </div>
            </div>
            
            ${category.date_debut_votes || category.date_fin_votes ? `
            <div class="modal-section">
                <h4>Période de votes</h4>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 6px;">
                    <div style="margin-bottom: 5px;">
                        <strong>Début:</strong> ${formatDate(category.date_debut_votes)}
                    </div>
                    <div>
                        <strong>Fin:</strong> ${formatDate(category.date_fin_votes)}
                    </div>
                </div>
            </div>
            ` : ''}
            
            <div class="modal-footer">
                <a href="${category.edit_url || `admin/categories/edit?id=${category.id}`}" 
                   class="btn btn-primary" 
                   style="display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-edit"></i> Modifier
                </a>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Fecha modal ao clicar fora
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
    
    // Fecha modal com ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
}

function closeModal() {
    const modal = document.querySelector('.category-modal');
    if (modal) {
        modal.classList.remove('active');
        setTimeout(() => modal.remove(), 300);
    }
}

// Confirmação de exclusão aprimorada
function confirmDelete(id, name) {
    return confirm(`Êtes-vous sûr de vouloir supprimer la catégorie "${name}" ?\n\n⚠️ Cette action est irréversible et supprimera toutes les données associées.`);
}