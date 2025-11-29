// Run on initial page load
document.addEventListener("DOMContentLoaded", function () {
    initializeJavaScript();
});

// Run when Turbo visits a new page
document.addEventListener("turbo:load", function () {
    initializeJavaScript();
    // initialClick();
});

// Optional: Handle when Turbo renders a frame
document.addEventListener("turbo:render", function () {
    initializeJavaScript();
});

function showDropdown() {
    document.querySelector(".dropdown-content").classList.toggle("show");
}

function initialClick()
{
    // Close the dropdown menu if the user clicks outside of it
    window.onclick = function(event) {
        if (!event.target.matches('.dropdown-toggle')) {
            var dropdowns = document.getElementsByClassName("dropdown-content");
            var i;
            for (i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
        }
    }
}

function initTomSelect() {

    document.querySelectorAll(['.search', '.tag']).forEach(select => {
        // Skip if already initialized, not in DOM, or not a valid select element
        if (!select || !document.body.contains(select) || select.tomselect) return;

        // Ensure the select element has options and is properly formed
        if (select.tagName === 'SELECT' && select.options.length > 0) {
            try {

                var options = {};
                if (select.hasAttribute('multiple')) {
                    options = {
                        remove_button: {
                            title: 'Remove this item',
                        }
                    };
                }

                select.tomselect = new TomSelect(select, {
                    plugins: options,
                });
            } catch (error) {
                console.warn('Tom Select init skipped:', error);
            }
        }
    });
}

function initializeJavaScript() {

    const wind_ = window;
    const body_ = document.body;

    initialClick();
    initTomSelect();

    //fix for checkbox select all
    document.addEventListener('click', function (e) {
        if (e.target.closest('.btn-check-m, .btn-check-d')) {
            const checkboxes = document.querySelectorAll('input[type="checkbox"]');
            const isChecked = e.target.closest('.btn-check-m, .btn-check-d').checked;
            checkboxes.forEach(checkbox => {
                if (checkbox !== e.target.closest('.btn-check-m, .btn-check-d')) {
                    checkbox.checked = isChecked;
                }
            });
        }
    });

    // fix for navigation mobile
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('overlay')) {
            removeOverlay();
            if (body_.classList.contains('hidden-navigation')) {
                const navMenuBody = document.querySelector('.navigation .navigation-menu-body');
                if (navMenuBody) navMenuBody.remove();
            }
            body_.classList.remove('navigation-show');
        }
    });

    /*------------- create/remove overlay -------------*/
    function createOverlay() {
        if (!document.querySelector('.overlay')) {
            body_.classList.add('no-scroll');
            const overlay = document.createElement('div');
            overlay.className = 'overlay';
            body_.appendChild(overlay);
            overlay.classList.add('show');
        }
    }

    function removeOverlay() {
        body_.classList.remove('no-scroll');
        const overlay = document.querySelector('.overlay');
        if (overlay) overlay.remove();
    }
    /*------------- create/remove overlay -------------*/

    // fix for group menu
    document.addEventListener('click', function (e) {
        const navTarget = e.target.closest('[data-nav-target]');
        if (navTarget) {
            e.preventDefault();
            const target = navTarget.dataset.navTarget;

            if (body_.classList.contains('navigation-toggle-one')) {
                body_.classList.add('navigation-show');
            }

            document.querySelectorAll('.navigation .navigation-menu-body .navigation-menu-group > div').forEach(el => {
                el.classList.remove('open');
            });

            const targetElement = document.querySelector(`.navigation .navigation-menu-body .navigation-menu-group ${target}`);
            if (targetElement) targetElement.classList.add('open');

            document.querySelectorAll('[data-nav-target]').forEach(el => {
                el.classList.remove('active');
            });
            navTarget.classList.add('active');
        }
    });

    document.addEventListener('click', function (e) {
        const navToggler = e.target.closest('.navigation-toggler a');
        if (navToggler) {
            e.preventDefault();
            if (window.innerWidth < 1200) {
                createOverlay();
                body_.classList.add('navigation-show');
            } else {
                if (!body_.classList.contains('navigation-toggle-one') && !body_.classList.contains('navigation-toggle-two')) {
                    body_.classList.add('navigation-toggle-one');
                } else if (body_.classList.contains('navigation-toggle-one') && !body_.classList.contains('navigation-toggle-two')) {
                    body_.classList.add('navigation-toggle-two');
                    body_.classList.remove('navigation-toggle-one');
                } else if (!body_.classList.contains('navigation-toggle-one') && body_.classList.contains('navigation-toggle-two')) {
                    body_.classList.remove('navigation-toggle-two');
                    body_.classList.remove('navigation-toggle-one');
                }
            }
        }
    });

    document.addEventListener('click', function (e) {
        const headerToggler = e.target.closest('.header-toggler a');
        if (headerToggler) {
            e.preventDefault();
            const navbarNav = document.querySelector('.header ul.navbar-nav');
            if (navbarNav) navbarNav.classList.toggle('open');
        }
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.navigation, .navigation-toggler') && body_.classList.contains('navigation-toggle-one')) {
            body_.classList.remove('navigation-show');
        }
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.header ul.navbar-nav, .header-toggler')) {
            const navbarNav = document.querySelector('.header ul.navbar-nav');
            if (navbarNav) navbarNav.classList.remove('open');
        }
    });

    /*------------- header search -------------*/
    document.addEventListener('click', function (e) {
        const searchToggle = e.target.closest('[data-toggle="search"]');
        if (searchToggle) {
            e.preventDefault();
            const headerSearch = document.querySelector('.header .header-body .header-search');
            if (headerSearch) {
                headerSearch.style.display = 'block';
                const formControl = headerSearch.querySelector('.form-control');
                if (formControl) formControl.focus();
            }
        }
    });

    document.addEventListener('click', function (e) {
        const closeSearch = e.target.closest('.close-header-search');
        if (closeSearch) {
            e.preventDefault();
            const headerSearch = document.querySelector('.header .header-body .header-search');
            if (headerSearch) headerSearch.style.display = 'none';
        }
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.header, [data-toggle="search"]')) {
            const headerSearch = document.querySelector('.header .header-body .header-search');
            if (headerSearch) headerSearch.style.display = 'none';
        }
    });
    /*------------- header search -------------*/

    /*------------- custom accordion -------------*/
    document.addEventListener('click', function (e) {
        const accordionHeader = e.target.closest('.accordion.custom-accordion .accordion-row a.accordion-header');
        if (accordionHeader) {
            e.preventDefault();
            const accordionRow = accordionHeader.parentElement;
            const accordion = accordionHeader.closest('.accordion.custom-accordion');

            accordion.querySelectorAll('.accordion-row').forEach(row => {
                if (row !== accordionRow) row.classList.remove('open');
            });

            accordionRow.classList.toggle('open');
        }
    });
    /*------------- custom accordion -------------*/

    /*------------- responsive table dropdown -------------*/
    let dropdownMenu;
    const tableResponsive = document.querySelectorAll('.table-responsive');

    tableResponsive.forEach(table => {
        table.addEventListener('show.bs.dropdown', function (e) {
            dropdownMenu = e.target.querySelector('.dropdown-menu');
            if (dropdownMenu) {
                body_.appendChild(dropdownMenu);
                const eOffset = e.target.getBoundingClientRect();
                dropdownMenu.style.display = 'block';
                dropdownMenu.style.top = `${eOffset.top + e.target.offsetHeight}px`;
                dropdownMenu.style.left = `${eOffset.left}px`;
                dropdownMenu.style.width = '184px';
                dropdownMenu.style.fontSize = '14px';
                dropdownMenu.classList.add("mobPosDropdown");
            }
        });

        table.addEventListener('hide.bs.dropdown', function (e) {
            if (dropdownMenu) {
                e.target.appendChild(dropdownMenu);
                dropdownMenu.style.display = 'none';
            }
        });
    });
    /*------------- responsive table dropdown -------------*/

    /*------------- aside menu toggle -------------*/
    document.addEventListener('click', function (e) {
        const navLink = e.target.closest('.navigation ul li a');
        if (navLink && navLink.nextElementSibling && navLink.nextElementSibling.tagName === 'UL') {
            e.preventDefault();
            const subMenuArrow = navLink.querySelector('.sub-menu-arrow');
            const nextUl = navLink.nextElementSibling;

            nextUl.style.display = nextUl.style.display === 'none' ? 'block' : 'none';

            if (subMenuArrow) {
                subMenuArrow.classList.toggle('rotate-in');

                if (subMenuArrow.classList.contains('rotate-in')) {
                    setTimeout(() => {
                        subMenuArrow.classList.remove('fa-chevron-down');
                        subMenuArrow.classList.add('fa-minus');
                    }, 200);
                } else {
                    subMenuArrow.classList.remove('fa-minus');
                    subMenuArrow.classList.add('fa-chevron-down');
                }
            }

            // Close other submenus
            nextUl.querySelectorAll('li ul').forEach(subUl => {
                subUl.style.display = 'none';
            });

            nextUl.querySelectorAll('li>a .sub-menu-arrow').forEach(arrow => {
                arrow.classList.remove('fa-minus', 'rotate-in');
                arrow.classList.add('fa-chevron-down');
            });

            if (!body_.classList.contains('horizontal-side-menu') && window.innerWidth >= 1200) {
                setTimeout(() => {
                    const navMenuBody = document.querySelector('.navigation .navigation-menu-body');
                    if (navMenuBody) {
                        // Trigger resize event if needed
                        window.dispatchEvent(new Event('resize'));
                    }
                }, 300);
            }
        }
    });

    // filter
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('filter')) {
            document.querySelectorAll(".collapse").forEach(collapse => {
                const bsCollapse = new bootstrap.Collapse(collapse);
                bsCollapse.toggle();
            });
        }
    });

    document.addEventListener('click', function (e) {
        const navLink = e.target.closest('.navigation-menu-group ul li .link');
        if (navLink) {
            document.querySelectorAll('.navigation-menu-group ul li .link.active').forEach(link => {
                link.classList.remove('active');
            });
            navLink.classList.add('active');
        }
    });
}