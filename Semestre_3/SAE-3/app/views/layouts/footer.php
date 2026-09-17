<link rel="stylesheet" href="/sae-3-festivote-tas-cesar/public/css/footer.css">
</main>
<footer>
        <div class="footer-content">
            <p><?php echo date('Y'); ?> CineVote - Plateforme de vote sécurisée - Tous droits réservés</p>
            <p id="nom-footer"><i>TAS & CESAR</i></p>
            <ul>
                <li><a href="/sae-3-festivote-tas-cesar/app/views/pages/cgu.php">CGU</a></li>
                <li><a href="/sae-3-festivote-tas-cesar/app/views/pages/mentions-legales.php">Mentions légales</a></li>
                <li><a href="/sae-3-festivote-tas-cesar/app/views/pages/confidentialite.php">Confidentialité</a></li>
                <li><a href="/sae-3-festivote-tas-cesar/app/views/pages/contact.php">Contact</a></li>
            </ul>
        </div>
</footer>
<script defer>
// Protection des images
document.addEventListener('DOMContentLoaded', function() {
    // Bloquer clic droit sur images
    document.addEventListener('contextmenu', function(e) {
        if (e.target.tagName === 'IMG') {
            e.preventDefault();
            return false;
        }
    });
    
    // Bloquer drag & drop
    document.addEventListener('dragstart', function(e) {
        if (e.target.tagName === 'IMG') {
            e.preventDefault();
            return false;
        }
    });
    
    // Désactiver sélection
    document.querySelectorAll('img').forEach(function(img) {
        img.style.userSelect = 'none';
        img.style.webkitUserSelect = 'none';
        img.setAttribute('draggable', 'false');
        img.setAttribute('oncontextmenu', 'return false;');
    });
});
</script>