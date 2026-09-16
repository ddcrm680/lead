@extends('layouts.app')

@section('title', 'Dashboard')

@section('page-eyebrow', 'SALES OPERATIONS')

@section('page-title', 'Dashboard')

@section('content')

      <section class="crm-page active" id="dashboard" aria-labelledby="dashboardHeading">
        <div class="page-stack">
          <section class="welcome">
            <div><span class="eyebrow"><i class="bi bi-stars"></i> <span id="todayLabel">TODAY</span></span><h2 id="dashboardHeading">Good afternoon, Anuj.</h2><p>Here’s what needs your attention across the sales floor today.</p></div>
            <button class="btn btn-dark" id="dashboardExportBtn" type="button"><i class="bi bi-download"></i> Export report</button>
          </section>
          <section class="kpi-grid" id="kpiGrid" aria-label="Lead statistics"></section>
          <section class="dashboard-grid">
            <article class="panel performance">
              <div class="panel-head"><div><h3>Lead performance</h3><p>Lead movement and qualification trend</p></div><select class="form-select form-select-sm" id="chartPeriod"><option value="7">Last 7 days</option><option value="30">Last 30 days</option></select></div>
              <div class="chart" id="leadChart" aria-label="Lead performance chart"></div>
              <div class="chart-legend"><span><i class="red-dot"></i> New leads</span><span><i class="dark-dot"></i> Qualified</span><strong id="chartTotal">0 total</strong></div>
            </article>
            <article class="panel focus"><div class="panel-head"><div><h3>Today’s focus</h3><p>Priority actions for your team</p></div><button class="link-btn" type="button" data-go="tasks">View all</button></div><div id="focusList"></div></article>
          </section>
          <section class="panel">
            <div class="panel-head"><div><h3>Recent high-intent leads</h3><p>Ranked by engagement and lead quality</p></div><button class="link-btn" type="button" data-go="leads">View lead list <i class="bi bi-arrow-right"></i></button></div>
            <div id="recentTable"></div>
          </section>
        </div>
      </section>

      <section class="crm-page" id="priority" aria-labelledby="priorityHeading">
        <div class="page-stack">
          <section class="page-intro"><div><span class="eyebrow">LIVE SALES FLOOR</span><h2 id="priorityHeading">Leads Priority Queue</h2><p>Time-sensitive leads ranked by urgency, intent, response delay and value.</p></div><div class="live-state"><span class="live-dot"></span><span id="queueState">Live data</span><b id="queueCountdown">Refresh in 15s</b><small id="queueUpdated">Waiting for first refresh</small></div></section>
          <section class="panel filter-panel">
            <div class="filter-row"><div class="searchbox"><i class="bi bi-search"></i><input id="prioritySearch" type="search" placeholder="Search name, phone or city"></div><select class="form-select" id="priorityAgentFilter"><option value="">All agents</option></select><button class="btn btn-outline-dark" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterDrawer"><i class="bi bi-funnel"></i> Filters</button></div>
            <div class="chips" id="statusChips"></div>
          </section>
          <div class="priority-grid" id="priorityGrid"></div>
        </div>
      </section>

      <section class="crm-page" id="followups" aria-labelledby="followupsHeading">
        <div class="page-stack">
          <section class="page-intro"><div><span class="eyebrow">CUSTOMER TOUCHPOINTS</span><h2 id="followupsHeading">Follow-ups</h2><p>Manage today’s calls, overdue conversations and scheduled meetings.</p></div><button class="btn btn-danger" id="addFollowupBtn" type="button"><i class="bi bi-plus-lg"></i> Add follow-up</button></section>
          <section class="task-summary" id="followupSummary"></section>
          <section class="panel">
            <div class="tabs responsive-tabs" id="followupPageTabs"><button class="active" type="button" data-follow-status="Today">Today</button><button type="button" data-follow-status="Upcoming">Upcoming</button><button type="button" data-follow-status="Overdue">Overdue</button><button type="button" data-follow-status="Completed">Completed</button></div>
            <div class="table-toolbar"><div class="searchbox"><i class="bi bi-search"></i><input id="followupSearch" type="search" placeholder="Search follow-ups"></div><select id="followupTypeFilter" class="form-select"><option value="">All types</option><option>Call</option><option>WhatsApp</option><option>Email</option><option>Meeting</option><option>Site Visit</option></select><span id="followupResultCount"></span></div>
            <div id="followupTable"></div>
          </section>
        </div>
      </section>

      <section class="crm-page" id="tasks" aria-labelledby="tasksHeading">
        <div class="page-stack">
          <section class="page-intro"><div><span class="eyebrow">TEAM EXECUTION</span><h2 id="tasksHeading">Assigned Tasks</h2><p>Plan calls, proposals, meetings and next actions without losing momentum.</p></div><div class="action-row"><button class="btn btn-outline-danger" id="openFollowUps" type="button"><i class="bi bi-telephone-forward"></i> Priority Follow-ups</button><button class="btn btn-outline-dark" id="taskCalendarBtn" type="button"><i class="bi bi-calendar3"></i> Calendar view</button></div></section>
          <section class="task-summary" id="taskSummary"></section>
          <section class="panel"><div class="tabs responsive-tabs" id="taskTabs"><button class="active" type="button" data-task-tab="Today">Today</button><button type="button" data-task-tab="Upcoming">Upcoming</button><button type="button" data-task-tab="Overdue">Overdue</button><button type="button" data-task-tab="Completed">Completed</button></div><div class="task-list" id="taskList"></div></section>
        </div>
      </section>

      <section class="crm-page" id="leads" aria-labelledby="leadsHeading">
        <div class="page-stack">
          <section class="page-intro"><div><span class="eyebrow">LEAD DATABASE</span><h2 id="leadsHeading">All Leads</h2><p>Search, filter, assign and update every franchise opportunity.</p></div><div class="action-row"><button class="btn btn-outline-dark" type="button" data-go="import"><i class="bi bi-upload"></i> Import</button><button class="btn btn-dark" type="button" data-go="export"><i class="bi bi-download"></i> Export</button><button class="btn btn-danger" type="button" data-bs-toggle="offcanvas" data-bs-target="#addLead"><i class="bi bi-plus-lg"></i> Add Lead</button></div></section>
          <section class="panel">
            <div class="table-toolbar leads-toolbar">
              <div class="searchbox"><i class="bi bi-search"></i><input id="leadSearch" type="search" placeholder="Search by name, mobile, city or lead ID"></div>
              <select id="leadStatusFilter" class="form-select"><option value="">All statuses</option></select>
              <select id="leadSourceFilter" class="form-select"><option value="">All sources</option></select>
              <button class="btn btn-outline-dark" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterDrawer"><i class="bi bi-funnel"></i> Advanced</button>
              <div class="view-toggle" role="group" aria-label="Lead view"><button class="active" id="listViewBtn" type="button" aria-label="List view"><i class="bi bi-list-ul"></i></button><button id="compactViewBtn" type="button" aria-label="Compact card view"><i class="bi bi-grid"></i></button></div>
              <span id="resultCount"></span>
            </div>
            <div id="allLeadTable"></div>
            <div class="pagination-row"><span id="showingCount"></span><div id="leadPagination"></div></div>
          </section>
        </div>
      </section>

      <section class="crm-page" id="pipeline" aria-labelledby="pipelineHeading">
        <div class="page-stack">
          <section class="page-intro"><div><span class="eyebrow">SALES PIPELINE</span><h2 id="pipelineHeading">Lead Pipeline</h2><p>Move opportunities through each stage and keep ownership visible.</p></div><div class="action-row"><select id="pipelineAgentFilter" class="form-select"><option value="">All agents</option></select><button class="btn btn-danger" type="button" data-bs-toggle="offcanvas" data-bs-target="#addLead"><i class="bi bi-plus-lg"></i> New Lead</button></div></section>
          <section class="pipeline-mobile-control"><label for="mobileStageSelect">Pipeline stage</label><select id="mobileStageSelect" class="form-select"></select></section>
          <section class="kanban" id="kanbanBoard" aria-label="Lead pipeline"></section>
        </div>
      </section>

      <section class="crm-page" id="reports" aria-labelledby="reportsHeading">
        <div class="page-stack">
          <section class="page-intro"><div><span class="eyebrow">PERFORMANCE INTELLIGENCE</span><h2 id="reportsHeading">Reports</h2><p>Understand conversion, lead sources, cities and team performance.</p></div><div class="action-row"><select id="reportPeriod" class="form-select"><option value="30">Last 30 days</option><option value="7">Last 7 days</option><option value="month">This month</option><option value="all">All time</option></select><button class="btn btn-dark" id="reportExportBtn" type="button"><i class="bi bi-download"></i> Export report</button></div></section>
          <section class="kpi-grid" id="reportKpis"></section>
          <section class="reports-grid"><article class="panel"><div class="panel-head"><div><h3>Source performance</h3><p>Lead volume and conversion by channel</p></div></div><div id="sourceReport"></div></article><article class="panel"><div class="panel-head"><div><h3>City conversion</h3><p>Top-performing franchise markets</p></div></div><div id="cityReport"></div></article></section>
          <section class="panel"><div class="panel-head"><div><h3>Agent performance</h3><p>Ownership, activity and conversion quality</p></div></div><div id="agentReport"></div></section>
        </div>
      </section>

      <section class="crm-page" id="studios" aria-labelledby="studiosHeading">
        <div class="page-stack"><section class="page-intro"><div><span class="eyebrow">FRANCHISE NETWORK</span><h2 id="studiosHeading">Business Locations</h2><p>Manage active, upcoming and archived business locations.</p></div><button class="btn btn-danger" id="addStudioBtn" type="button"><i class="bi bi-plus-lg"></i> Add Studio</button></section><section class="directory-grid" id="studioGrid"></section></div>
      </section>

      <section class="crm-page" id="property" aria-labelledby="propertyHeading"><div class="page-stack"><section class="page-intro"><div><span class="eyebrow">SITE ACQUISITION</span><h2 id="propertyHeading">Rental Property Pipeline</h2><p>Shortlisted locations for upcoming franchise studios.</p></div><button class="btn btn-danger" id="addPropertyBtn" type="button"><i class="bi bi-plus-lg"></i> Add Property</button></section><section class="directory-grid" id="propertyGrid"></section></div></section>

      <section class="crm-page" id="employees" aria-labelledby="employeesHeading"><div class="page-stack"><section class="page-intro"><div><span class="eyebrow">TEAM DIRECTORY</span><h2 id="employeesHeading">Employees</h2><p>Agent availability, ownership, workload and performance.</p></div><button class="btn btn-danger" id="addEmployeeBtn" type="button"><i class="bi bi-plus-lg"></i> Add Employee</button></section><section class="directory-grid" id="employeeGrid"></section></div></section>

      <section class="crm-page" id="import" aria-labelledby="importHeading">
        <div class="page-stack">
          <section class="page-intro"><div><span class="eyebrow">DATA INTAKE</span><h2 id="importHeading">Import Leads</h2><p>Upload CSV or Excel, validate records and prevent duplicates before importing.</p></div><button class="btn btn-outline-dark" id="downloadTemplateBtn" type="button"><i class="bi bi-file-earmark-arrow-down"></i> Download template</button></section>
          <section class="import-grid">
            <article class="panel upload-panel"><div class="dropzone" id="leadDropzone" tabindex="0"><input id="leadFileInput" type="file" accept=".csv,.xlsx" hidden><div class="upload-icon"><i class="bi bi-cloud-arrow-up"></i></div><h3>Drop your lead file here</h3><p>CSV or XLSX up to 10 MB</p><button class="btn btn-danger" id="browseFileBtn" type="button">Browse file</button></div><div class="file-meta" id="selectedFileMeta" hidden></div></article>
            <article class="panel import-help"><h3>Import checklist</h3><ol><li>Use one row per lead.</li><li>Mobile number is required.</li><li>Existing mobile or email values are marked duplicate.</li><li>Review validation results before import.</li></ol><div class="notice-box"><i class="bi bi-shield-check"></i><div><strong>Safe import</strong><p>Nothing is saved until you confirm the import.</p></div></div></article>
          </section>
          <section class="task-summary" id="importStats"></section>
          <section class="panel" id="importPreviewPanel" hidden><div class="panel-head"><div><h3>Import preview</h3><p>Review detected fields and record status</p></div><button class="btn btn-danger" id="confirmImportBtn" type="button"><i class="bi bi-check2-circle"></i> Import valid records</button></div><div id="importPreview"></div></section>
        </div>
      </section>

      <section class="crm-page" id="export" aria-labelledby="exportHeading">
        <div class="page-stack">
          <section class="page-intro"><div><span class="eyebrow">DATA DELIVERY</span><h2 id="exportHeading">Export Leads</h2><p>Build a filtered export for reporting, finance or campaign follow-up.</p></div></section>
          <section class="export-grid">
            <article class="panel export-builder"><h3>1. Choose filters</h3><div class="form-grid"><label>Status<select id="exportStatus" class="form-select"><option value="">All statuses</option></select></label><label>Lead source<select id="exportSource" class="form-select"><option value="">All sources</option></select></label><label>Assigned agent<select id="exportAgent" class="form-select"><option value="">All agents</option></select></label><label>City<input id="exportCity" class="form-control" placeholder="All cities"></label></div><h3>2. Choose fields</h3><div class="field-grid" id="exportFields"></div></article>
            <aside class="panel export-summary"><span class="eyebrow">EXPORT SUMMARY</span><strong id="exportRecordCount">0</strong><p>matching lead records</p><div class="export-actions"><button class="btn btn-dark" id="exportCsvBtn" type="button"><i class="bi bi-filetype-csv"></i> Export CSV</button><button class="btn btn-danger" id="exportXlsxBtn" type="button"><i class="bi bi-file-earmark-excel"></i> Export Excel</button></div><small>Excel export uses the bundled browser integration when available and falls back to an Excel-compatible file.</small></aside>
          </section>
        </div>
      </section>

   
@endsection

@push('js')

{{-- dashboard.js later --}}

@endpush
