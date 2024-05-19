/**
 * Admin Aside Menu Class.
 *
 * Handles the menu behaviour based on user interactions.
 */
class AppAdminMenuAside {
    constructor() {
        // Globals
        this.menuLinks = document.querySelectorAll(".menu-link");
        this.curURL = window.location.href;

        // Init
        this.handleCurrentPage();
    }

    /**
     * Handle active navigation link.
     */
    handleCurrentPage() {
        this.menuLinks.forEach((menuLink) => {
            const linkHref = menuLink.getAttribute("href");

            if (this.curURL === linkHref) {
                menuLink.classList.add("active");

                let parent = menuLink.parentElement;
                while (parent) {
                    if (parent.classList.contains("menu-sub-accordion")) {
                        parent.classList.add("show");
                        break;
                    }
                    parent = parent.parentElement;
                }
            }
        });
    }
}

// Initialize
const asideMenu = new AppAdminMenuAside();
