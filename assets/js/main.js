/**
 * Main JavaScript
 * General site functionality
 */

(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        initStickyHeader();
        initStickyBottomBar();
        initFormHandling();
        initDeityContextColors();
        initFormPreFill();
        initPackageCardLinks();
        initSacredPackageTabs();
        initItineraryShowMore();
        initOverviewSeeMore();
    });

    /**
     * Sticky Header - Appears after scrolling
     */
    function initStickyHeader() {
        const header = document.querySelector('.tk-header');
        if (!header) return;

        let ticking = false;

        window.addEventListener('scroll', function() {
            if (!ticking) {
                window.requestAnimationFrame(function() {
                    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                    updateHeader(scrollTop, header);
                    ticking = false;
                });

                ticking = true;
            }
        });
    }
    
    /**
     * Overview See More/Less Toggle
     */
    function initOverviewSeeMore() {
        const overviewContainers = document.querySelectorAll('[data-overview-content]');
        
        overviewContainers.forEach(container => {
            const content = container.querySelector('.tk-overview-collapsible');
            const fade = container.querySelector('.tk-overview-fade');
            const btn = container.querySelector('.tk-overview-see-more');
            
            if (!content || !btn) return;

            // Get full content height and calculate 25% to show initially
            const fullHeight = content.scrollHeight;
            const collapsedHeight = Math.max(fullHeight * 0.25, 100); // Show 25%, minimum 100px
            
            // If content is short enough, no need for collapse
            if (fullHeight <= 200) {
                btn.style.display = 'none';
                if (fade) fade.style.display = 'none';
                content.style.maxHeight = 'none';
                return;
            }

            // Set initial collapsed height
            content.style.maxHeight = collapsedHeight + 'px';

            btn.addEventListener('click', function() {
                const textSpan = btn.querySelector('.tk-overview-see-more__text');
                const isExpanded = container.classList.contains('is-expanded');

                if (isExpanded) {
                    // Collapse - set back to 25%
                    container.classList.remove('is-expanded');
                    content.classList.remove('is-expanded');
                    btn.classList.remove('is-expanded');
                    content.style.maxHeight = collapsedHeight + 'px';
                    textSpan.textContent = 'See More';
                } else {
                    // Expand - show full content
                    container.classList.add('is-expanded');
                    content.classList.add('is-expanded');
                    btn.classList.add('is-expanded');
                    content.style.maxHeight = fullHeight + 'px';
                    textSpan.textContent = 'See Less';
                }
            });
        });
    }

    /**
     * Itinerary Show More/Less Toggle
     */
    function initItineraryShowMore() {
        const showMoreBtns = document.querySelectorAll('.tk-itinerary-show-more__btn');
        
        showMoreBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const section = btn.closest('.tk-pilgrimage-itinerary');
                if (!section) return;

                const hiddenItems = section.querySelectorAll('.tk-itinerary-item--hidden');
                const textSpan = btn.querySelector('.tk-itinerary-show-more__text');
                const isExpanded = btn.classList.contains('is-expanded');

                if (isExpanded) {
                    // Collapse - hide items
                    hiddenItems.forEach(item => {
                        item.style.display = 'none';
                    });
                    btn.classList.remove('is-expanded');
                    textSpan.textContent = 'Show More';
                } else {
                    // Expand - show items
                    hiddenItems.forEach(item => {
                        item.style.display = 'block';
                    });
                    btn.classList.add('is-expanded');
                    textSpan.textContent = 'Show Less';
                }
            });
        });
    }

    function initSacredPackageTabs() {
        const sections = document.querySelectorAll('[data-sacred-packages]');
        if (!sections.length) return;

        const urlParams = new URLSearchParams(window.location.search);
        const requestedDeity = urlParams.get('deity');

        sections.forEach(section => {
            const tabs = section.querySelectorAll('.tk-sacred-tab');
            const grid = section.querySelector('[data-sacred-grid]');
            if (!tabs.length || !grid) return;

            const cards = grid.querySelectorAll('.tk-package-card');

            function applyFilter(filter) {
                cards.forEach(card => {
                    const deities = (card.getAttribute('data-deities') || '').split(/\s+/).filter(Boolean);
                    if (filter === 'all') {
                        card.classList.remove('tk-sacred-card-hidden');
                    } else if (deities.includes(filter)) {
                        card.classList.remove('tk-sacred-card-hidden');
                    } else {
                        card.classList.add('tk-sacred-card-hidden');
                    }
                });
            }

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const filter = tab.getAttribute('data-filter');

                    tabs.forEach(t => t.classList.remove('is-active'));
                    tab.classList.add('is-active');

                    applyFilter(filter);

                    try {
                        const url = new URL(window.location.href);
                        if (filter === 'all') {
                            url.searchParams.delete('deity');
                        } else {
                            url.searchParams.set('deity', filter);
                        }
                        window.history.replaceState({}, '', url.toString());
                    } catch (e) {
                        // Fallback: ignore URL update errors
                    }
                });
            });

            let initialTab = tabs[0];
            if (requestedDeity) {
                tabs.forEach(tab => {
                    if (tab.getAttribute('data-filter') === requestedDeity) {
                        initialTab = tab;
                    }
                });
            }

            const initialFilter = initialTab.getAttribute('data-filter');
            tabs.forEach(t => t.classList.remove('is-active'));
            initialTab.classList.add('is-active');

            applyFilter(initialFilter);
        });
    }

    function updateHeader(scrollTop, header) {
        // Header appears with background after scrolling down 50px
        if (scrollTop > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    }

    /**
     * Sticky Bottom Bar (Mobile)
     */
    function initStickyBottomBar() {
        const bottomBar = document.querySelector('.tk-sticky-bottom-bar');
        if (!bottomBar) return;

        let lastScrollTop = 0;
        let ticking = false;

        window.addEventListener('scroll', function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            if (!ticking) {
                window.requestAnimationFrame(function() {
                    updateBottomBar(scrollTop, lastScrollTop, bottomBar);
                    lastScrollTop = scrollTop;
                    ticking = false;
                });

                ticking = true;
            }
        });
    }

    function updateBottomBar(scrollTop, lastScrollTop, bottomBar) {
        // Show when scrolled down past 200px
        if (scrollTop > 200) {
            bottomBar.classList.add('visible');
        } else {
            bottomBar.classList.remove('visible');
        }
    }

})();

    /**
     * Form Handling and Validation
     */
    function initFormHandling() {
        const forms = document.querySelectorAll('.tk-contact-form');
        
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                handleFormSubmit(form);
            });
        });
    }

    function handleFormSubmit(form) {
        // Clear previous messages
        const messageDiv = form.parentElement.querySelector('.tk-form-message');
        if (messageDiv) {
            messageDiv.style.display = 'none';
            messageDiv.className = 'tk-form-message';
        }

        // Validate required fields
        const name = form.querySelector('[name="name"]').value.trim();
        const email = form.querySelector('[name="email"]').value.trim();

        if (!name || !email) {
            showFormMessage(form, 'Please fill in all required fields.', 'error');
            return;
        }

        if (!isValidEmail(email)) {
            showFormMessage(form, 'Please enter a valid email address.', 'error');
            return;
        }

        // Prepare form data
        const formData = new FormData(form);

        // Show loading state
        const submitBtn = form.querySelector('[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Sending...';
        submitBtn.disabled = true;

        // Submit via AJAX
        fetch(window.tripKailashData?.ajaxUrl || '/wp-admin/admin-ajax.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Debug logging - check browser console (F12) to see details
            console.log('Form submission response:', data);
            
            if (data.success) {
                const successMsg = form.querySelector('[name="success_message"]').value;
                showFormMessage(form, successMsg, 'success');
                form.reset();
            } else {
                showFormMessage(form, data.data?.message || 'An error occurred. Please try again.', 'error');
            }
        })
        .catch(error => {
            console.error('Form submission error:', error);
            showFormMessage(form, 'An error occurred. Please try again.', 'error');
        })
        .finally(() => {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        });
    }

    function showFormMessage(form, message, type) {
        const messageDiv = form.parentElement.querySelector('.tk-form-message');
        if (messageDiv) {
            messageDiv.textContent = message;
            messageDiv.className = 'tk-form-message ' + type;
            messageDiv.style.display = 'block';
        }
    }

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    /**
     * Deity Context Colors for Form Inputs
     */
    function initDeityContextColors() {
        // Detect deity context from body class or page
        const body = document.body;
        let deityClass = null;

        if (body.classList.contains('tk-deity--shiva')) {
            deityClass = 'tk-deity--shiva';
        } else if (body.classList.contains('tk-deity--vishnu')) {
            deityClass = 'tk-deity--vishnu';
        } else if (body.classList.contains('tk-deity--devi')) {
            deityClass = 'tk-deity--devi';
        }

        // Apply deity class to form containers if deity context exists
        if (deityClass) {
            const formContainers = document.querySelectorAll('.tk-contact-form-widget');
            formContainers.forEach(container => {
                container.classList.add(deityClass);
            });
        }
    }


    /**
     * Contact Form Pre-fill
     */
    function initFormPreFill() {
        const urlParams = new URLSearchParams(window.location.search);
        const packageParam = urlParams.get('package');

        if (packageParam) {
            const packageSelect = document.getElementById('tk-package-interest');
            if (packageSelect) {
                // Find and select the matching option
                const options = packageSelect.querySelectorAll('option');
                options.forEach(option => {
                    if (option.value === packageParam) {
                        option.selected = true;
                    }
                });

                // Scroll to form
                const form = packageSelect.closest('.tk-contact-form-widget');
                if (form) {
                    setTimeout(() => {
                        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }, 500);
                }
            }
        }
    }

    /**
     * Package Card Links with Contact Parameter
     */
    function initPackageCardLinks() {
        // Add click handler to "Book This Yatra" buttons in overlay
        document.addEventListener('click', function(e) {
            if (e.target.matches('.tk-overlay__sticky-cta .tk-btn')) {
                const overlay = e.target.closest('.tk-package-overlay');
                if (overlay) {
                    const title = overlay.querySelector('h2')?.textContent;
                    if (title) {
                        const contactUrl = '#contact?package=' + encodeURIComponent(title);
                        window.location.href = contactUrl;
                    }
                }
            }
        });
    }



/**
 * Force remove top spacing from first Elementor section
 * This overrides any inline styles set in Elementor editor
 */
function forceRemoveElementorTopSpacing() {
    // Don't run on package archive pages
    if (document.body.classList.contains('post-type-archive-pilgrimage_package')) {
        return;
    }

    // Find first Elementor section
    const firstSection = document.querySelector('.elementor > .elementor-section-wrap > .elementor-section:first-child, .elementor > .elementor-section:first-child');
    
    if (firstSection) {
        firstSection.style.paddingTop = '0';
        firstSection.style.marginTop = '0';
        
        // Also target the container inside
        const container = firstSection.querySelector('.elementor-container');
        if (container) {
            container.style.paddingTop = '0';
            container.style.marginTop = '0';
        }
    }
}

// Run on page load
document.addEventListener('DOMContentLoaded', forceRemoveElementorTopSpacing);

// Also run after a short delay to catch any dynamic content
setTimeout(forceRemoveElementorTopSpacing, 100);
