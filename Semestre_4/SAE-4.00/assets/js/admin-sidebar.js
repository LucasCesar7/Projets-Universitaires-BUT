// Admin Sidebar JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Elementos DOM
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const mainContent = document.querySelector('.admin-main-content');
    
    // Verificar se os elementos existem
    if (!sidebar || !sidebarToggle) return;
    
    // Função para verificar o tamanho da tela
    function checkScreenSize() {
        if (window.innerWidth <= 768) {
            // Mobile
            sidebarToggle.style.display = 'flex';
            sidebar.classList.remove('active');
            
            if (mainContent) {
                mainContent.style.marginLeft = '0';
            }
            if (sidebarOverlay) {
                sidebarOverlay.style.display = 'none';
            }
        } else {
            // Desktop
            sidebarToggle.style.display = 'none';
            sidebar.classList.add('active');
            
            if (mainContent) {
                mainContent.style.marginLeft = '250px';
            }
        }
    }
    
    // Alternar sidebar (mobile)
    sidebarToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        sidebar.classList.toggle('active');
        
        // Ajustar overlay
        if (sidebarOverlay) {
            sidebarOverlay.style.display = sidebar.classList.contains('active') ? 'block' : 'none';
        }
        
        // Ajustar conteúdo principal
        if (mainContent) {
            if (sidebar.classList.contains('active')) {
                mainContent.style.marginLeft = '280px';
            } else {
                mainContent.style.marginLeft = '0';
            }
        }
    });
    
    // Fechar sidebar ao clicar no overlay
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('active');
            sidebarOverlay.style.display = 'none';
            
            if (mainContent) {
                mainContent.style.marginLeft = '0';
            }
        });
    }
    
    // Fechar sidebar ao clicar em um link (mobile)
    document.querySelectorAll('.sidebar-nav a').forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('active');
                if (sidebarOverlay) {
                    sidebarOverlay.style.display = 'none';
                }
                if (mainContent) {
                    mainContent.style.marginLeft = '0';
                }
            }
        });
    });
    
    // Prevenir fechamento ao clicar dentro da sidebar
    sidebar.addEventListener('click', function(e) {
        e.stopPropagation();
    });
    
    // Inicialização
    checkScreenSize();
    
    // Redimensionamento da janela
    window.addEventListener('resize', checkScreenSize);
});

// Exportar funções para uso externo
window.AdminSidebar = {
    toggle: function() {
        const sidebar = document.getElementById('sidebar');
        if (sidebar) sidebar.classList.toggle('active');
    },
    open: function() {
        const sidebar = document.getElementById('sidebar');
        if (sidebar) sidebar.classList.add('active');
    },
    close: function() {
        const sidebar = document.getElementById('sidebar');
        if (sidebar) sidebar.classList.remove('active');
    }
};
