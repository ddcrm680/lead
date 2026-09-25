
  <div class="offcanvas offcanvas-end notification-drawer" tabindex="-1" id="notificationDrawer" aria-labelledby="notificationDrawerTitle">
    <div class="offcanvas-header">
      <div>
        <span class="eyebrow">ACTIVITY CENTER</span>
        <h2 id="notificationDrawerTitle">Notifications</h2>
      </div>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body" id="notificationList"></div>
  </div>


  <div class="modal fade" id="globalSearchModal" tabindex="-1" aria-labelledby="globalSearchTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content search-modal">
        <div class="modal-header">
          <div>
            <span class="eyebrow">GLOBAL SEARCH</span>
            <h2 id="globalSearchTitle">Find anything</h2>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="searchbox global-searchbox">
            <i class="bi bi-search"></i>
            <input id="globalSearchInput" type="search" placeholder="Search lead name, mobile, email, city or ID" autocomplete="off">
          </div>
          <div id="globalSearchResults" class="global-results"></div>
        </div>
      </div>
    </div>
  </div>


  <div class="followup-shell" id="followUpShell" aria-hidden="true">
    <div class="followup-backdrop" id="followUpBackdrop"></div>
    <aside class="followup-drawer" role="dialog" aria-modal="true" aria-labelledby="followUpTitle">
      <div class="followup-head">
        <h2 id="followUpTitle">Priority Follow-ups</h2>
        <button id="closeFollowUps" type="button" aria-label="Close follow-ups">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
      <div class="followup-tabs" id="followUpTabs">
        <button class="active" type="button" data-follow-tab="Today">Today <b>11</b>
        </button>
        <button type="button" data-follow-tab="Upcoming">Upcoming <b>24</b>
        </button>
        <button type="button" data-follow-tab="Overdue">Overdue <b>7</b>
        </button>
        <button type="button" data-follow-tab="Completed">Done <b>88</b>
        </button>
      </div>
      <div class="followup-list" id="followUpList"></div>
    </aside>
  </div>
