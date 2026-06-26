// simple javascript functionalities
document.addEventListener("DOMContentLoaded", function() {
    // Add simple confirmation dialogs to any forms that have delete action (although we added inline onclick in PHP)
    const deleteForms = document.querySelectorAll('form input[value="delete"]');
    deleteForms.forEach(input => {
        const form = input.closest('form');
        if (form && !form.hasAttribute('onsubmit')) {
            form.onsubmit = function(e) {
                if (!confirm("Are you sure you want to delete this?")) {
                    e.preventDefault();
                }
            };
        }
    });

    // Simple mobile menu toggle logic
    const nav = document.querySelector('.main-nav');
    const headerContainer = document.querySelector('.header-container');

    // Create toggle button
    const toggleBtn = document.createElement('button');
    toggleBtn.innerHTML = '☰';
    toggleBtn.className = 'mobile-menu-toggle';
    toggleBtn.setAttribute('aria-label', 'Toggle navigation menu');

    if (nav && headerContainer) {
        headerContainer.appendChild(toggleBtn);

        toggleBtn.addEventListener('click', function() {
            nav.classList.toggle('nav-open');
        });
    }
});