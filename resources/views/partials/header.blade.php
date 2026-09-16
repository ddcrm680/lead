   <header class="topbar">

    <div class="topbar-context">

        <button class="menu-btn" id="openNav" type="button" aria-label="Open navigation">
            <i class="bi bi-list"></i>
        </button>

        <div>
            <small id="pageEyebrow">
                @yield('page-eyebrow', 'WORKSPACE')
            </small>

            <h1 id="pageTitle">
                @yield('page-title', 'Dashboard')
            </h1>
        </div>

    </div>

      <div class="top-actions">
        <button class="icon-btn" id="globalSearchBtn" type="button" aria-label="Open global search"><i class="bi bi-search"></i></button>
        <button class="icon-btn notice" id="notificationBtn" type="button" aria-label="Notifications"><i class="bi bi-bell"></i><b id="notificationCount">3</b></button>

        <button class="btn btn-danger add-btn" 
                type="button" 
                data-create-url="{{ route('createLead') }}" 
                data-bs-target="#addLead" 
                aria-controls="addLead">
            <i class="bi bi-plus-lg"></i>
            <span>New Lead</span>
        </button>
        
      </div>
    </header>
