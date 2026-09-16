  <div class="offcanvas offcanvas-end detail-drawer" tabindex="-1" id="leadDetail" aria-labelledby="leadDetailTitle">
    <div class="offcanvas-header detail-head">
      <div>
        <span class="eyebrow">LEAD PROFILE</span>
        <h2 id="leadDetailTitle">Lead details</h2>
        <p id="leadDetailSubtitle"></p>
      </div>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body" id="leadDetailBody"></div>
  </div>


  <div class="offcanvas offcanvas-end filter-drawer" tabindex="-1" id="filterDrawer" aria-labelledby="filterDrawerTitle">
    <div class="offcanvas-header">
      <div>
        <span class="eyebrow">REFINE RESULTS</span>
        <h2 id="filterDrawerTitle">Advanced Filters</h2>
      </div>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
      <div class="form-section">
        <label>City <input class="form-control" id="advancedCity" placeholder="Any city">
        </label>
        <label>Assigned agent <select class="form-select" id="advancedAgent">
            <option value="">Any agent</option>
          </select>
        </label>
        <label>Minimum lead score <input class="form-range" id="advancedScore" type="range" min="0" max="100" value="0">
          <span id="advancedScoreLabel">0+</span>
        </label>
        <label>Created after <input class="form-control" id="advancedDate" type="date">
        </label>
      </div>
    </div>
    <div class="modal-foot">
      <button class="btn btn-light" id="resetAdvancedFilters" type="button">Reset</button>
      <button class="btn btn-danger" id="applyAdvancedFilters" type="button" data-bs-dismiss="offcanvas">Apply filters</button>
    </div>
  </div>


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