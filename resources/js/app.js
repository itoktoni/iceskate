import './bootstrap';

// Import TomSelect CSS
import 'tom-select/dist/css/tom-select.bootstrap4.css';

import * as bootstrap from 'bootstrap'

import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';

// import ajax from '@imacrayon/alpine-ajax';

// Alpine.plugin(ajax)

Livewire.start()

import TomSelect from 'tom-select';
window.TomSelect = TomSelect;

// Main application code using native JavaScript
(function() {
    'use strict';

    // Get window and body elements
    const wind = window;
    const body = document.body;

    // Helper function to query elements
    const $ = (selector) => document.querySelector(selector);
    const $$ = (selector) => document.querySelectorAll(selector);

    // Helper function to check if element matches selector
    function matches(element, selector) {
        return element.matches || element.matchesSelector ||
               element.msMatchesSelector || element.mozMatchesSelector ||
               element.webkitMatchesSelector || element.oMatchesSelector;
    }

    // Helper function to check if element is within a parent
    function isWithin(element, parent) {
        return parent && parent.contains(element);
    }

    // Add event listener helper
    function on(selector, event, handler, useCapture = false) {
        if (selector === document) {
            document.addEventListener(event, handler, useCapture);
        } else {
            $$(selector).forEach(el => el.addEventListener(event, handler, useCapture));
        }
    }

    // toastr.options = {
    //     timeOut: shared('timer'),
    //     progressBar: true,
    //     showDuration: 200,
    //     hideDuration: 200
    // };

    // flatpickr(".datejs", {
    //     altInput: true,
    //     defaultDate : 'today',
    //     altFormat: "j F Y",
    //     dateFormat: "Y-m-d"
    // });

    // flatpickr(".datetime", {
    //     enableTime: true,
    //     altInput: true,
    //     defaultDate : 'today',
    //     altFormat: "j M Y H:i",
    //     dateFormat: "Y-m-d H:i"
    // });

    // $('.table').cardtable();

    // Also try to initialize immediately in case elements are already loaded

    // Checkbox functionality - Master checkbox toggle
    function toggleRowCheckboxes(masterCheckbox) {
        var isChecked = masterCheckbox.checked;

        // Try to find table first (for btn-check-d in table header)
        var table = masterCheckbox.closest('table');
        if (table) {
            // Only affect row checkboxes within the same table, excluding the master checkbox
            const checkboxes = table.querySelectorAll('tbody input[type="checkbox"]:not(.btn-check-m):not(.btn-check-d), input.checkbox');
            checkboxes.forEach(cb => {
                cb.checked = isChecked;
            });
        } else {
            // For btn-check-m outside table, find all checkboxes within the same form or container
            const form = masterCheckbox.closest('form');
            if (form) {
                const checkboxes = form.querySelectorAll('input[type="checkbox"]:not(.btn-check-m):not(.btn-check-d), input.checkbox');
                checkboxes.forEach(cb => {
                    cb.checked = isChecked;
                });
            } else {
                // Fallback to all checkboxes on the page (excluding master checkboxes)
                const checkboxes = document.querySelectorAll('input[type="checkbox"]:not(.btn-check-m):not(.btn-check-d)');
                checkboxes.forEach(cb => {
                    cb.checked = isChecked;
                });
            }
        }
        console.log('Master checkbox clicked. Setting row checkboxes to:', isChecked);
    }

    // Handle click events
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-check-m') || e.target.classList.contains('btn-check-d')) {
            // Let the browser handle the checkbox state change naturally
            setTimeout(() => {
                toggleRowCheckboxes(e.target);
            }, 0);
        }
    });

    // Handle change events (in case click doesn't work)
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('btn-check-m') || e.target.classList.contains('btn-check-d')) {
            toggleRowCheckboxes(e.target);
        }
    });

    // Profile dropdown functionality
    window.showDropdown = function() {
        const profileItem = document.querySelector('.nav-item.profile');
        if (profileItem) {
            const dropdownContent = profileItem.querySelector('.dropdown-content');
            if (dropdownContent) {
                dropdownContent.classList.toggle('show');
            }
        }
    };

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const profileItem = document.querySelector('.nav-item.profile');
        if (profileItem && !profileItem.contains(e.target)) {
            const dropdownContent = profileItem.querySelector('.dropdown-content');
            if (dropdownContent) {
                dropdownContent.classList.remove('show');
            }
        }
    });

    // Fullscreen toggle
    on('click', '[data-toggle="fullscreen"]', function(e) {
        e.preventDefault();
        this.classList.toggle('active-fullscreen');

        if (document.fullscreenEnabled) {
            if (this.classList.contains("active-fullscreen")) {
                document.documentElement.requestFullscreen();
            } else {
                document.exitFullscreen();
            }
        } else {
            alert("Your browser does not support fullscreen.");
        }
        return false;
    });

    // Overlay click handler
    on('click', '.overlay', function() {
        removeOverlay();
        if (body.classList.contains('hidden-navigation')) {
            const navMenuBody = $('.navigation .navigation-menu-body');
            if (navMenuBody) navMenuBody.remove();
        }
        body.classList.remove('navigation-show');
    });

    /*------------- create/remove overlay -------------*/
    function createOverlay() {
        if ($('.overlay') === null) {
            body.classList.add('no-scroll');
            body.insertAdjacentHTML('beforeend', '<div class="overlay"></div>');
            const overlay = $('.overlay');
            if (overlay) overlay.classList.add('show');
        }
    }

    function removeOverlay() {
        body.classList.remove('no-scroll');
        const overlay = $('.overlay');
        if (overlay) overlay.remove();
    }
    /*------------- create/remove overlay -------------*/

    // Background image functionality
    $$('[data-backround-image]').forEach(element => {
        const bgImage = element.getAttribute('data-backround-image');
        element.style.background = `url(${bgImage})`;
    });

    // Navigation target handler with better event delegation
    document.addEventListener('click', function(e) {
        const navTarget = e.target.closest('[data-nav-target]');
        if (!navTarget) return;

        e.preventDefault();
        e.stopPropagation();

        const target = navTarget.getAttribute('data-nav-target');

        if (body.classList.contains('navigation-toggle-one')) {
            body.classList.add('navigation-show');
        }

        const navGroups = $$('.navigation .navigation-menu-body .navigation-menu-group > div');
        navGroups.forEach(group => group.classList.remove('open'));

        // Find target element based on actual DOM structure
        let targetElement;

        // First try direct ID search (e.g., #aplikasi, #laporan, #setting)
        targetElement = $(target);

        // If not found, try within navigation menu group
        if (!targetElement) {
            const navMenuGroup = $('.navigation-menu-group');
            if (navMenuGroup) {
                targetElement = navMenuGroup.querySelector(target);
            }
        }

        // Final fallback: search within all navigation groups
        if (!targetElement) {
            const navGroups = $$('.navigation-menu-group');
            navGroups.forEach(group => {
                const found = group.querySelector(target);
                if (found && !targetElement) {
                    targetElement = found;
                }
            });
        }

        if (targetElement) targetElement.classList.add('open');

        // Remove active class from all navigation icons and add to current
        $$('.navigation-menu-tab [data-nav-target]').forEach(el => {
            el.classList.remove('active');
        });
        navTarget.classList.add('active');
    });

    // Navigation toggler - for desktop
    on('click', '.navigation-toggler a', function() {
        if (wind.innerWidth < 1200) {
            createOverlay();
            body.classList.add('navigation-show');
        } else {
            if (!body.classList.contains('navigation-toggle-one') && !body.classList.contains('navigation-toggle-two')) {
                body.classList.add('navigation-toggle-one');
            } else if (body.classList.contains('navigation-toggle-one') && !body.classList.contains('navigation-toggle-two')) {
                body.classList.add('navigation-toggle-two');
                body.classList.remove('navigation-toggle-one');
            } else if (!body.classList.contains('navigation-toggle-one') && body.classList.contains('navigation-toggle-two')) {
                body.classList.remove('navigation-toggle-two');
                body.classList.remove('navigation-toggle-one');
            }
        }
        return false;
    });

    // Mobile toggler - specifically for mobile devices (multiple possible selectors)
    document.addEventListener('click', function(e) {
        const mobileToggler = e.target.closest('.mobile-toggler, .mobile-menu-toggler, .hamburger, .menu-toggle, [data-mobile-toggle], [data-toggle="mobile-nav"]');
        if (mobileToggler) {
            e.preventDefault();
            e.stopPropagation();

            if (wind.innerWidth < 1200) {
                // Toggle mobile navigation state
                if (body.classList.contains('mobile-navigation-open')) {
                    body.classList.remove('mobile-navigation-open', 'navigation-show');
                    removeOverlay();
                } else {
                    createOverlay();
                    body.classList.add('navigation-show', 'mobile-navigation-open');
                }
            } else {
                // For desktop, just trigger normal navigation toggle
                if (mobileToggler.classList.contains('navigation-toggler') ||
                    mobileToggler.closest('.navigation-toggler')) {
                    // Let the existing navigation-toggler handle it
                    return;
                }
            }
            return false;
        }
    });

    // Fallback for any element with data attributes that might control mobile nav
    document.addEventListener('click', function(e) {
        const toggleElement = e.target.closest('[data-toggle="mobile-nav"], [data-mobile-nav-toggle], [data-nav-toggle="mobile"]');
        if (toggleElement && wind.innerWidth < 1200) {
            e.preventDefault();
            e.stopPropagation();

            if (body.classList.contains('mobile-navigation-open')) {
                body.classList.remove('mobile-navigation-open', 'navigation-show');
                removeOverlay();
            } else {
                createOverlay();
                body.classList.add('navigation-show', 'mobile-navigation-open');
            }
            return false;
        }
    });

    // Touch events for mobile devices
    document.addEventListener('touchstart', function(e) {
        // Touch handling for mobile navigation
        if (body.classList.contains('mobile-navigation-open')) {
            const navigation = $('.navigation');
            const toggler = $('.mobile-toggler');

            if (navigation && !isWithin(e.target, navigation) &&
                (!toggler || !isWithin(e.target, toggler))) {
                // Close mobile navigation when touching outside
                body.classList.remove('mobile-navigation-open', 'navigation-show');
                removeOverlay();
            }
        }
    });

    // Handle window resize for mobile navigation
    wind.addEventListener('resize', function() {
        if (wind.innerWidth >= 1200) {
            // Remove mobile navigation classes on desktop
            body.classList.remove('mobile-navigation-open', 'navigation-show');
            removeOverlay();
        }
    });

    // Header toggler
    on('click', '.header-toggler a', function() {
        const navbar = $('.header ul.navbar-nav');
        if (navbar) navbar.classList.toggle('open');
        return false;
    });

    // Close navigation on outside click
    document.addEventListener('click', function(e) {
        if (!isWithin(e.target, $('.navigation')) &&
            !isWithin(e.target, $('.navigation-toggler')) &&
            body.classList.contains('navigation-toggle-one')) {
            body.classList.remove('navigation-show');
        }
    });

    // Close header navbar on outside click
    document.addEventListener('click', function(e) {
        const navbar = $('.header ul.navbar-nav');
        if (navbar && !isWithin(e.target, navbar) &&
            !isWithin(e.target, $('.header-toggler'))) {
            navbar.classList.remove('open');
        }
    });

    /*------------- header search -------------*/
    on('click', '[data-toggle="search"], [data-toggle="search"] *', function() {
        const search = $('.header .header-body .header-search');
        if (search) {
            search.style.display = 'block';
            const formControl = search.querySelector('.form-control');
            if (formControl) formControl.focus();
        }
        return false;
    });

    on('click', '.close-header-search, .close-header-search svg', function() {
        const search = $('.header .header-body .header-search');
        if (search) search.style.display = 'none';
        return false;
    });

    document.addEventListener('click', function(e) {
        const search = $('.header .header-body .header-search');
        if (search && !isWithin(e.target, $('.header')) &&
            !isWithin(e.target, $('[data-toggle="search"]'))) {
            search.style.display = 'none';
        }
    });
    /*------------- header search -------------*/

    /*------------- custom accordion -------------*/
    on('click', '.accordion.custom-accordion .accordion-row a.accordion-header', function() {
        const accordion = this.closest('.accordion.custom-accordion');
        const rows = accordion.querySelectorAll('.accordion-row');

        rows.forEach(row => {
            if (row !== this.parentElement) {
                row.classList.remove('open');
            }
        });

        this.parentElement.classList.toggle('open');
        return false;
    });
    /*------------- custom accordion -------------*/

    /*------------- responsive table dropdown -------------*/
    let dropdownMenu;
    const tableResponsive = $('.table-responsive');

    if (tableResponsive) {
        tableResponsive.addEventListener('show.bs.dropdown', function(e) {
            const target = e.target;
            dropdownMenu = target.querySelector('.dropdown-menu');

            if (dropdownMenu) {
                body.appendChild(dropdownMenu);
                const rect = target.getBoundingClientRect();

                dropdownMenu.style.display = 'block';
                dropdownMenu.style.top = (rect.bottom + window.scrollY) + 'px';
                dropdownMenu.style.left = rect.left + 'px';
                dropdownMenu.style.width = '184px';
                dropdownMenu.style.fontSize = '14px';
                dropdownMenu.classList.add('mobPosDropdown');
            }
        });

        tableResponsive.addEventListener('hide.bs.dropdown', function(e) {
            const target = e.target;
            if (dropdownMenu && target) {
                target.appendChild(dropdownMenu);
                dropdownMenu.style.display = 'none';
            }
        });
    }
    /*------------- responsive table dropdown -------------*/

    /*------------- chat -------------*/
    on('click', '.chat-app-wrapper .btn-chat-sidebar-open', function() {
        const sidebar = $('.chat-app-wrapper .chat-sidebar');
        if (sidebar) sidebar.classList.add('chat-sidebar-opened');
        return false;
    });

    document.addEventListener('click', function(e) {
        const sidebar = $('.chat-app-wrapper .chat-sidebar');
        const button = $('.chat-app-wrapper .btn-chat-sidebar-open');

        if (sidebar && button &&
            !isWithin(e.target, sidebar) &&
            !isWithin(e.target, button)) {
            sidebar.classList.remove('chat-sidebar-opened');
        }
    });
    /*------------- chat -------------*/

    /*------------- aside menu toggle -------------*/
    // Use event delegation for navigation menu
    document.addEventListener('click', function(e) {
        const link = e.target.closest('.navigation ul li a');
        if (!link) return;

        // Check if this link has a submenu
        const submenu = link.nextElementSibling;
        if (submenu && submenu.tagName === 'UL') {
            e.preventDefault();
            e.stopPropagation(); // Prevent other handlers

            const arrow = link.querySelector('.sub-menu-arrow');
            if (arrow) {
                arrow.classList.toggle('rotate-in');
            }

            const isCurrentlyVisible = submenu.style.display === 'block';
            submenu.style.display = isCurrentlyVisible ? 'none' : 'block';

            // Slide up any nested submenus when closing
            if (isCurrentlyVisible) {
                const nestedSubmenus = submenu.querySelectorAll('li ul');
                nestedSubmenus.forEach(nested => {
                    nested.style.display = 'none';
                });

                // Reset arrow classes for nested items
                const nestedArrows = submenu.querySelectorAll('li > a .sub-menu-arrow');
                nestedArrows.forEach(nestedArrow => {
                    nestedArrow.classList.remove('fa-minus', 'rotate-in');
                    nestedArrow.classList.add('fa-chevron-down');
                });
            }

            if (arrow && arrow.classList.contains('rotate-in')) {
                setTimeout(() => {
                    arrow.classList.remove('fa-chevron-down');
                    arrow.classList.add('fa-minus');
                }, 200);
            } else if (arrow) {
                arrow.classList.remove('fa-minus');
                arrow.classList.add('fa-chevron-down');
            }

            if (!body.classList.contains('horizontal-side-menu') && wind.innerWidth >= 1200) {
                setTimeout(() => {
                    const menuBody = $('.navigation .navigation-menu-body');
                    if (menuBody) {
                        // Trigger resize event
                        const event = new Event('resize');
                        window.dispatchEvent(event);
                    }
                }, 300);
            }
            return false;
        }
    });

    // Modal example
    const exampleModal = $('#exampleModal');
    if (exampleModal) {
        exampleModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const recipient = button.getAttribute('data-whatever');
            const modal = this;

            const title = modal.querySelector('.modal-title');
            const input = modal.querySelector('.modal-body input');

            if (title) title.textContent = `New message to ${recipient}`;
            if (input) input.value = recipient;
        });
    }

    // Filter functionality
    on('click', '.filter', function() {
        $$('.collapse').forEach(collapse => {
            // Bootstrap collapse toggle
            const bsCollapse = bootstrap.Collapse.getOrCreateInstance(collapse);
            bsCollapse.toggle();
        });
    });

    // Navigation menu group link
    on('click', '.navigation-menu-group ul li .link', function() {
        $$('.navigation-menu-group ul li .link.active').forEach(link => {
            link.classList.remove('active');
        });
        this.classList.add('active');
    });

    // TomSelect initialization
    function initializeTomSelect() {
        // Initialize TomSelect on ALL select.form-control elements and select.search
        $$('select.search').forEach(select => {
            if (!select.classList.contains('ts-hidden-accessible') &&
                !select.hasAttribute('data-exclude-tom-select')) {

                const isMultiple = select.multiple;

                const options = {
                    create: false,
                    sortField: 'text',
                    placeholder: 'Select an option...',
                    allowEmptyOption: true
                };

                // Add remove button plugin for multiple selects
                if (isMultiple) {
                    options.plugins = ['remove_button'];
                }

                new TomSelect(select, options);
            }
        });

        // Handle select elements with data-tom-select attribute for custom options
        $$('select[data-tom-select]').forEach(select => {
            if (!select.classList.contains('ts-hidden-accessible')) {
                try {
                    const customOptions = JSON.parse(select.getAttribute('data-tom-select')) || {};
                    const isMultiple = select.multiple;

                    const options = {
                        create: false,
                        sortField: 'text',
                        placeholder: 'Select an option...',
                        allowEmptyOption: true,
                        ...customOptions
                    };

                    if (isMultiple) {
                        options.plugins = ['remove_button'];
                    }

                    new TomSelect(select, options);
                } catch (e) {
                    console.warn('Invalid data-tom-select JSON:', e);
                }
            }
        });
    }

    // Helper function to create TomSelect with custom options
    window.createTomSelect = function(selector, options = {}) {
        const element = typeof selector === 'string' ? $(selector) : selector;
        if (!element) return null;

        const isMultiple = element.multiple;

        const defaultOptions = {
            create: false,
            sortField: 'text',
            placeholder: 'Select an option...',
            allowEmptyOption: true
        };

        if (isMultiple) {
            defaultOptions.plugins = ['remove_button'];
            defaultOptions.remove_button = {
                title: 'Remove',
            };
            defaultOptions.persist = false;
            defaultOptions.keyboardControl = true;
        }

        const mergedOptions = { ...defaultOptions, ...options };
        return new TomSelect(selector, mergedOptions);
    };

    // Initialize on document ready
    document.addEventListener('DOMContentLoaded', initializeTomSelect);

    // Initialize after Livewire updates
    document.addEventListener('livewire:navigated', initializeTomSelect);
    document.addEventListener('livewire:load', initializeTomSelect);

    // Handle dynamic content (for content loaded via AJAX, etc.)
    document.addEventListener('DOMNodeInserted', function(e) {
        const target = e.target;
        if (target.querySelector &&
            (target.querySelector('.tom-select') ||
             target.querySelector('select.form-control:not([data-exclude-tom-select])') ||
             target.querySelector('select[data-tom-select]'))) {
            initializeTomSelect();
        }
    });
})();