document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const categoryFilter = document.getElementById('categoryFilter');
    const table = document.getElementById('candidaturesTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;
        const categoryValue = categoryFilter.value;
        
        for (let row of rows) {
            const rowText = row.textContent.toLowerCase();
            const rowStatus = row.getAttribute('data-status');
            const rowCategory = row.getAttribute('data-category');
            
            const matchesSearch = searchTerm === '' || rowText.includes(searchTerm);
            const matchesStatus = statusValue === '' || rowStatus === statusValue;
            const matchesCategory = categoryValue === '' || rowCategory === categoryValue;
            
            row.style.display = matchesSearch && matchesStatus && matchesCategory ? '' : 'none';
        }
    }
    
    if (searchInput) searchInput.addEventListener('input', filterTable);
    if (statusFilter) statusFilter.addEventListener('change', filterTable);
    if (categoryFilter) categoryFilter.addEventListener('change', filterTable);
    
    const platformBadges = document.querySelectorAll('.platform-badge');
    platformBadges.forEach(badge => {
        const platform = badge.textContent.toLowerCase();
        if (platform.includes('instagram')) badge.style.background = 'linear-gradient(45deg, #405DE6, #5851DB, #833AB4, #C13584, #E1306C, #FD1D1D)';
        else if (platform.includes('tiktok')) badge.style.background = '#000000';
        else if (platform.includes('youtube')) badge.style.background = '#FF0000';
    });
    
    document.querySelectorAll('.candidate-avatar').forEach(avatar => {
        const colors = ['#3498db', '#2ecc71', '#9b59b6', '#e74c3c', '#f39c12'];
        const randomColor = colors[Math.floor(Math.random() * colors.length)];
        avatar.style.background = randomColor;
        avatar.style.color = 'white';
    });
});