// js/script.js végére illeszd be
document.addEventListener('DOMContentLoaded', () => {
    const navButtons = document.querySelectorAll('.btn.page-link');

    navButtons.forEach(btn => {
        // belépéskor nagyítunk kicsit
        btn.addEventListener('mouseenter', () => {
            btn.style.transition = 'transform 0.2s ease-out';
            btn.style.transform = 'scale(1.1)';
            btn.style.boxShadow = '0 4px 8px rgba(0,0,0,0.2)';
        });
        // kilépéskor visszaállítjuk az eredetit
        btn.addEventListener('mouseleave', () => {
            btn.style.transition = 'transform 0.2s ease-in';
            btn.style.transform = 'scale(1)';
            btn.style.boxShadow = 'none';
        });
    });
});


