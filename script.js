// Service modal functions
function openServiceModal(modalId) {
    console.log('Opening modal:', modalId);
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    } else {
        console.error('Modal not found:', modalId);
    }
}

function closeServiceModal(modalId) {
    console.log('Closing modal:', modalId);
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }
}



// Add click event listeners to service cards
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up service card listeners');
    
    // Service card click handlers
    const weddingCard = document.getElementById('weddingCard');
    const birthdayCard = document.getElementById('birthdayCard');
    const anniversaryCard = document.getElementById('anniversaryCard');

    console.log('Found cards:', { weddingCard, birthdayCard, anniversaryCard });

    if (weddingCard) {
        weddingCard.addEventListener('click', () => {
            console.log('Wedding card clicked');
            openServiceModal('weddingModal');
        });
    }
    if (birthdayCard) {
        birthdayCard.addEventListener('click', () => {
            console.log('Birthday card clicked');
            openServiceModal('birthdayModal');
        });
    }
    if (anniversaryCard) {
        anniversaryCard.addEventListener('click', () => {
            console.log('Anniversary card clicked');
            openServiceModal('anniversaryModal');
        });
    }



    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            if (event.target === modal) {
                modal.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        });
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (modal.classList.contains('active')) {
                    modal.classList.remove('active');
                    document.body.style.overflow = 'auto';
                }
            });
        }
    });
});

// Initialize the main app if it exists
document.addEventListener('DOMContentLoaded', function() {
    if (typeof InviteWebApp !== 'undefined') {
        new InviteWebApp();
    }
}); 
// Здесь будет ваш код из script.js
// Вставьте сюда весь код из https://welcome-to-day.ru/script.js 