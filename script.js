// Video platform global scripts

document.addEventListener('DOMContentLoaded', function() {
    // Handle category selection
    const categoryElements = document.querySelectorAll('.category');
    if (categoryElements.length > 0) {
        categoryElements.forEach(function(category) {
            category.addEventListener('click', function() {
                // Remove active class from all categories
                document.querySelector('.category.active')?.classList.remove('active');
                // Add active class to clicked category
                category.classList.add('active');

                // In a real app, this would fetch videos for the selected category
                console.log('Category selected:', category.textContent);
                // fetchVideosByCategory(category.textContent);
            });
        });
    }

    // Form validation for registration
    const registrationForm = document.getElementById('registrationForm');
    if (registrationForm) {
        registrationForm.addEventListener('submit', function(e) {
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');

            if (password && confirmPassword && password.value !== confirmPassword.value) {
                e.preventDefault();
                showFormError('Passwords do not match!');
                confirmPassword.focus();
            }
        });
    }

    // Video player functionality (simplified)
    const videoPlayer = document.getElementById('videoPlayer');
    if (videoPlayer) {
        const playBtn = document.querySelector('.play-btn');
        if (playBtn) {
            playBtn.addEventListener('click', function() {
                if (videoPlayer.paused) {
                    videoPlayer.play();
                    playBtn.innerHTML = '<i class="fas fa-pause"></i>';
                } else {
                    videoPlayer.pause();
                    playBtn.innerHTML = '<i class="fas fa-play"></i>';
                }
            });
        }

        // Auto hide controls after inactivity
        let timeout;
        const videoControls = document.querySelector('.video-controls');
        if (videoControls) {
            videoPlayer.addEventListener('mousemove', function() {
                videoControls.style.opacity = 1;
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    if (!videoPlayer.paused) {
                        videoControls.style.opacity = 0;
                    }
                }, 3000);
            });
        }
    }

    // AJAX navigation (load content without full page reload)
    const navLinks = document.querySelectorAll('.page-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const page = this.getAttribute('data-page');
            loadPage(page);

            // Update URL without page reload
            history.pushState({page: page}, '', `?page=${page}`);
        });
    });

    // Handle browser back/forward navigation
    window.addEventListener('popstate', function(e) {
        if (e.state && e.state.page) {
            loadPage(e.state.page);
        } else {
            loadPage('home'); // Default page
        }
    });

    // Check URL on page load to determine which content to show
    const urlParams = new URLSearchParams(window.location.search);
    const page = urlParams.get('page') || 'home';
    loadPage(page);
});

// Load page content via AJAX
function loadPage(page) {
    const contentArea = document.getElementById('content');
    if (!contentArea) return;

    fetch(`pages/${page}.php`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Page not found');
            }
            return response.text();
        })
        .then(html => {
            contentArea.innerHTML = html;

            // Re-initialize any scripts specific to the loaded content
            initializePageScripts(page);

            // Highlight active nav link
            document.querySelectorAll('.nav-links a').forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('data-page') === page) {
                    link.classList.add('active');
                }
            });
        })
        .catch(error => {
            contentArea.innerHTML = `<div class="alert alert-danger">Error loading page: ${error.message}</div>`;
        });
}

// Initialize page-specific scripts after loading content
function initializePageScripts(page) {
    if (page === 'home') {
        // Initialize home-specific scripts
        const categoryElements = document.querySelectorAll('.category');
        if (categoryElements.length > 0) {
            categoryElements.forEach(function(category) {
                category.addEventListener('click', function() {
                    document.querySelector('.category.active')?.classList.remove('active');
                    category.classList.add('active');
                });
            });
        }
    } else if (page === 'watch') {
        // Initialize video player scripts
        const videoPlayer = document.getElementById('videoPlayer');
        if (videoPlayer) {
            videoPlayer.focus();
        }
    }
}

// Utility function to show form errors
function showFormError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'alert alert-danger';
    errorDiv.textContent = message;

    const form = document.querySelector('form');
    const formHeader = document.querySelector('.form-header');

    if (form && formHeader) {
        form.insertBefore(errorDiv, formHeader.nextSibling);

        // Remove after 5 seconds
        setTimeout(() => {
            errorDiv.remove();
        }, 5000);
    }
}