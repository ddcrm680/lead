<!-- Mobile Bottom Navigation -->
<nav
  class="mobile-bottom-nav"
  aria-label="Mobile primary navigation"
>
  <button
    type="button"
    class="mobile-bottom-nav__item is-active"
    data-page="dashboard"
    data-mobile-nav="dashboard"
    aria-controls="dashboard"
    aria-current="page"
  >
    <i class="bi bi-grid-1x2-fill mobile-bottom-nav__icon" aria-hidden="true"></i>
    <span class="mobile-bottom-nav__label">Dashboard</span>
  </button>

  <button
    type="button"
    class="mobile-bottom-nav__item"
    data-page="leads"
    data-mobile-nav="leads"
    aria-controls="leads"
  >
    <i class="bi bi-people-fill mobile-bottom-nav__icon" aria-hidden="true"></i>
    <span class="mobile-bottom-nav__label">Leads</span>
  </button>

  <div class="mobile-bottom-nav__add-wrap">
    <button
      type="button"
      class="mobile-bottom-nav__add"
      data-bs-toggle="offcanvas"
      data-bs-target="#addLead"
      aria-controls="addLead"
      aria-label="Add a new lead"
    >
      <i class="bi bi-plus-lg" aria-hidden="true"></i>
    </button>

    <span class="mobile-bottom-nav__add-label">Add Lead</span>
  </div>

  <button
    type="button"
    class="mobile-bottom-nav__item"
    data-page="followups"
    data-mobile-nav="followups"
    aria-controls="followups"
  >
    <i class="bi bi-telephone-outbound-fill mobile-bottom-nav__icon" aria-hidden="true"></i>
    <span class="mobile-bottom-nav__label">Follow-ups</span>
  </button>

  <button
    type="button"
    class="mobile-bottom-nav__item"
    data-mobile-nav="more"
    data-bs-toggle="offcanvas"
    data-bs-target="#mobileMoreMenu"
    aria-controls="mobileMoreMenu"
    aria-label="Open more navigation options"
  >
    <i class="bi bi-grid-fill mobile-bottom-nav__icon" aria-hidden="true"></i>
    <span class="mobile-bottom-nav__label">More</span>
  </button>
</nav>

<!-- Mobile More Menu -->
<div
  class="offcanvas offcanvas-bottom mobile-more-menu"
  tabindex="-1"
  id="mobileMoreMenu"
  aria-labelledby="mobileMoreMenuLabel"
>
  <div class="offcanvas-header">
    <div>
      <small class="text-uppercase text-danger fw-bold">Navigation</small>
      <h2 class="h5 mb-0 mt-1" id="mobileMoreMenuLabel">More options</h2>
    </div>

    <button
      type="button"
      class="btn-close"
      data-bs-dismiss="offcanvas"
      aria-label="Close"
    ></button>
  </div>

  <div class="offcanvas-body">
    <div class="mobile-more-menu__grid">
      <button type="button" class="mobile-more-menu__link" data-page="pipeline" data-bs-dismiss="offcanvas">
        <i class="bi bi-kanban-fill" aria-hidden="true"></i>
        <span>Pipeline</span>
      </button>

      <button type="button" class="mobile-more-menu__link" data-page="priority" data-bs-dismiss="offcanvas">
        <i class="bi bi-lightning-charge-fill" aria-hidden="true"></i>
        <span>Priority</span>
      </button>

      <button type="button" class="mobile-more-menu__link" data-page="tasks" data-bs-dismiss="offcanvas">
        <i class="bi bi-check2-square" aria-hidden="true"></i>
        <span>Tasks</span>
      </button>

      <button type="button" class="mobile-more-menu__link" data-page="reports" data-bs-dismiss="offcanvas">
        <i class="bi bi-bar-chart-fill" aria-hidden="true"></i>
        <span>Reports</span>
      </button>

      <button type="button" class="mobile-more-menu__link" data-page="studios" data-bs-dismiss="offcanvas">
        <i class="bi bi-buildings-fill" aria-hidden="true"></i>
        <span>Studios</span>
      </button>

      <button type="button" class="mobile-more-menu__link" data-page="property" data-bs-dismiss="offcanvas">
        <i class="bi bi-building-fill-check" aria-hidden="true"></i>
        <span>Rent Property</span>
      </button>

      <button type="button" class="mobile-more-menu__link" data-page="employees" data-bs-dismiss="offcanvas">
        <i class="bi bi-person-badge-fill" aria-hidden="true"></i>
        <span>Employees</span>
      </button>

      <button type="button" class="mobile-more-menu__link" data-page="import" data-bs-dismiss="offcanvas">
        <i class="bi bi-file-earmark-arrow-up-fill" aria-hidden="true"></i>
        <span>Import</span>
      </button>

      <button type="button" class="mobile-more-menu__link" data-page="export" data-bs-dismiss="offcanvas">
        <i class="bi bi-file-earmark-arrow-down-fill" aria-hidden="true"></i>
        <span>Export</span>
      </button>

      <button type="button" class="mobile-more-menu__link" data-page="settings" data-bs-dismiss="offcanvas">
        <i class="bi bi-gear-fill" aria-hidden="true"></i>
        <span>Settings</span>
      </button>

      <button type="button" class="mobile-more-menu__link" data-page="profile" data-bs-dismiss="offcanvas">
        <i class="bi bi-person-circle" aria-hidden="true"></i>
        <span>Profile</span>
      </button>
    </div>
  </div>
</div>
