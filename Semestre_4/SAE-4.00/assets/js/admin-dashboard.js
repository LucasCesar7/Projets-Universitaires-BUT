document.addEventListener('DOMContentLoaded', function() {
    updateDateTime();
    
    setInterval(updateDateTime, 60000);
    
    initLinkHoverEffects();
});

function updateDateTime() {
    const now = new Date();
    const options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
    };
    
    const formatter = new Intl.DateTimeFormat('fr-FR', options);
    const dateTimeString = formatter.format(now);
    
    const dateTimeElement = document.getElementById('currentDateTime');
    if (dateTimeElement) {
        dateTimeElement.textContent = dateTimeString;
    }
}

function initLinkHoverEffects() {
    const linkItems = document.querySelectorAll('.link-item');
    
    linkItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
}

function typeWriterEffect() {
    const welcomeMessage = document.querySelector('.welcome-message');
    if (!welcomeMessage) return;
    
    const text = welcomeMessage.textContent;
    welcomeMessage.textContent = '';
    
    let i = 0;
    const speed = 50; 
    
    function typeWriter() {
        if (i < text.length) {
            welcomeMessage.textContent += text.charAt(i);
            i++;
            setTimeout(typeWriter, speed);
        }
    }

    if (!sessionStorage.getItem('welcomeTyped')) {
        setTimeout(typeWriter, 1000);
        sessionStorage.setItem('welcomeTyped', 'true');
    } else {
        welcomeMessage.textContent = text;
    }
}
