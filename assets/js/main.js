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
    const nav = document.querySelector('.main-nav ul');
    const headerContainer = document.querySelector('.header-container');

    // Create toggle button
    const toggleBtn = document.createElement('button');
    toggleBtn.innerHTML = '☰';
    toggleBtn.className = 'mobile-menu-toggle';
    toggleBtn.style.display = 'none'; // Hidden by default (desktop)
    toggleBtn.style.background = 'none';
    toggleBtn.style.border = 'none';
    toggleBtn.style.fontSize = '1.5em';
    toggleBtn.style.cursor = 'pointer';

    // Check if we need mobile menu
    function checkWidth() {
        if (window.innerWidth < 768 && nav) {
            toggleBtn.style.display = 'block';
            nav.style.display = 'none';
            nav.style.flexDirection = 'column';
            nav.style.width = '100%';
            nav.style.background = '#fff';
            nav.style.position = 'absolute';
            nav.style.top = '60px';
            nav.style.left = '0';
            nav.style.padding = '10px 0';
            nav.style.boxShadow = '0 2px 5px rgba(0,0,0,0.1)';

            const lis = nav.querySelectorAll('li');
            lis.forEach(li => {
                li.style.margin = '10px 20px';
            });
        } else if (nav) {
            toggleBtn.style.display = 'none';
            nav.style.display = 'flex';
            nav.style.flexDirection = 'row';
            nav.style.position = 'static';
            nav.style.boxShadow = 'none';

            const lis = nav.querySelectorAll('li');
            lis.forEach(li => {
                li.style.margin = '0 0 0 20px';
            });
        }
    }

    if (nav && headerContainer) {
        headerContainer.appendChild(toggleBtn);
        checkWidth();
        window.addEventListener('resize', checkWidth);

        toggleBtn.addEventListener('click', function() {
            if (nav.style.display === 'none') {
                nav.style.display = 'flex';
            } else {
                nav.style.display = 'none';
            }
        });
    }
});