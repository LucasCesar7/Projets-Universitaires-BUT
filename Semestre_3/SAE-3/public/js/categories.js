document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const filmCards = document.querySelectorAll('.film-card');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const selectedGenre = this.getAttribute('data-filter');
            
            // Mettre à jour le bouton actif
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Filtrer les films
            filmCards.forEach(card => {
                const cardGenres = card.getAttribute('data-genre');
                
                if (selectedGenre === 'all') {
                    card.style.display = 'block';
                } else {
                    // Vérifier si le genre est dans les genres du film
                    if (cardGenres && cardGenres.toLowerCase().includes(selectedGenre.toLowerCase())) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                }
            });
        });
    });
});
