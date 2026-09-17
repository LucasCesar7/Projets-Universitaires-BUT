<!-- Bandeau de consentement RGPD -->
<div id="cookie-banner" style="display: none; position: fixed; bottom: 0; width: 100%; background: #333; color: #fff; padding: 20px; text-align: center; z-index: 9999;">
    <p>Ce site utilise des cookies pour améliorer votre expérience. En poursuivant votre navigation, vous acceptez leur utilisation.</p>
    <button id="accept-cookies" style="margin-left: 15px; padding: 10px 20px; cursor: pointer;">Accepter</button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Vérifie si le consentement a déjà été donné
        if (!localStorage.getItem('cookie_consent')) {
            document.getElementById('cookie-banner').style.display = 'block';
        }

        document.getElementById('accept-cookies').addEventListener('click', function() {
            localStorage.setItem('cookie_consent', 'true');
            document.getElementById('cookie-banner').style.display = 'none';
            // Vous pouvez ici déclencher le chargement de scripts tiers (ex: Analytics)
            location.reload(); 
        });
    });
</script>