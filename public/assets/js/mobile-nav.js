  document.addEventListener("DOMContentLoaded", function () {
    const mobileNavItems = Array.from(
      document.querySelectorAll("[data-mobile-nav]")
    );

    const directMobilePages = new Set([
      "dashboard",
      "leads",
      "followups"
    ]);

    const morePages = new Set([
      "priority",
      "tasks",
      "pipeline",
      "reports",
      "studios",
      "property",
      "employees",
      "import",
      "export",
      "settings",
      "profile"
    ]);

    function updateMobileNavigation(pageId) {
      let activeMobileItem = null;

      if (directMobilePages.has(pageId)) {
        activeMobileItem = pageId;
      } else if (morePages.has(pageId)) {
        activeMobileItem = "more";
      }

      mobileNavItems.forEach(function (item) {
        const isActive = item.dataset.mobileNav === activeMobileItem;

        item.classList.toggle("is-active", isActive);

        if (isActive) {
          item.setAttribute("aria-current", "page");
        } else {
          item.removeAttribute("aria-current");
        }
      });
    }

    /*
     * app.js already handles every [data-page] and [data-go] control.
     * This listener only synchronizes the fixed mobile navigation state.
     */
    document.querySelectorAll("[data-page], [data-go]").forEach(function (control) {
      control.addEventListener("click", function () {
        const targetPage = control.dataset.page || control.dataset.go;

        if (targetPage) {
          updateMobileNavigation(targetPage);
        }
      });
    });

    /*
     * Keep the mobile navigation correct when another script activates a page.
     */
    const pageContainer = document.querySelector(".content");

    if (pageContainer) {
      const pageObserver = new MutationObserver(function () {
        const activePage = pageContainer.querySelector(".crm-page.active");

        if (activePage) {
          updateMobileNavigation(activePage.id);
        }
      });

      pageObserver.observe(pageContainer, {
        subtree: true,
        attributes: true,
        attributeFilter: ["class"]
      });
    }

    const initialPage = document.querySelector(".crm-page.active");
    updateMobileNavigation(initialPage ? initialPage.id : "dashboard");
  });
