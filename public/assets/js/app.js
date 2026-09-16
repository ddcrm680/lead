(() => {
    "use strict";

    const $ = (id) => document.getElementById(id);
    const $$ = (selector, root = document) => [...root.querySelectorAll(selector)];
    const STORAGE = {
        leads: "ddlc_minimal_complete_leads_v1",
        settings: "ddlc_minimal_complete_settings_v1",
        drawerSeen: "ddlc_minimal_complete_followup_seen_v1",
    };

    const statuses = [
        "New",
        "Contacted",
        "Interested",
        "Qualified",
        "Proposal Sent",
        "Negotiation",
        "Site Visit",
        "Converted",
        "Lost",
    ];
    const sources = [
        "Website",
        "Google Ads",
        "Meta Ads",
        "Instagram",
        "WhatsApp",
        "Referral",
        "Franchise Enquiry",
        "Landing Page",
        "Prime Franchise",
    ];
    const agents = ["Aman Sharma", "Rishi Kohli", "Anjali Gupta", "Kunal Mehta", "Priya Verma"];
    const cities = [
        "New Delhi",
        "Noida",
        "Ghaziabad",
        "Gurugram",
        "Faridabad",
        "Jaipur",
        "Indore",
        "Pune",
        "Mumbai",
        "Vadodara",
        "Chandigarh",
        "Lucknow",
        "Jodhpur",
        "Meerut",
        "Amritsar",
    ];
    const statesByCity = {
        "New Delhi": "Delhi",
        Noida: "Uttar Pradesh",
        Ghaziabad: "Uttar Pradesh",
        Gurugram: "Haryana",
        Faridabad: "Haryana",
        Jaipur: "Rajasthan",
        Indore: "Madhya Pradesh",
        Pune: "Maharashtra",
        Mumbai: "Maharashtra",
        Vadodara: "Gujarat",
        Chandigarh: "Punjab",
        Lucknow: "Uttar Pradesh",
        Jodhpur: "Rajasthan",
        Meerut: "Uttar Pradesh",
        Amritsar: "Punjab",
    };
    const firstNames = [
        "Rohit",
        "Prithvi",
        "Arjun",
        "Vikram",
        "Aditya",
        "Rahul",
        "Nikhil",
        "Karan",
        "Sahil",
        "Mohit",
        "Aman",
        "Varun",
        "Ankit",
        "Deepak",
        "Saurabh",
        "Jaimin",
        "Akash",
        "Rajat",
        "Manish",
        "Harsh",
        "Neeraj",
        "Piyush",
        "Abhishek",
        "Rishabh",
        "Tarun",
    ];
    const lastNames = [
        "Kapoor",
        "Kaushik",
        "Sharma",
        "Mehta",
        "Gupta",
        "Malhotra",
        "Verma",
        "Bansal",
        "Khanna",
        "Singh",
        "Chopra",
        "Agarwal",
        "Jain",
        "Yadav",
        "Saxena",
        "Mishra",
        "Kohli",
        "Arora",
        "Choudhary",
        "Patel",
    ];
    const interests = ["Franchise", "Car Detailing", "Ceramic Coating", "PPF", "Car Wash", "Accessories"];
    const budgets = ["Under ₹10 Lakh", "₹10–25 Lakh", "₹25–50 Lakh", "₹50 Lakh–₹1 Crore", "₹1 Crore+"];
    const models = ["Prime Franchise", "Standard Franchise", "Premium Franchise", "FOFO 35L", "Fractional"];
    const timelines = ["Immediate", "1 Month", "3 Months", "6 Months", "12+ Months"];
    const studios = [
        {
            name: "Noida Sector 1",
            code: "DD-NOI-01",
            city: "Noida",
            state: "Uttar Pradesh",
            owner: "Company Operated",
            manager: "Aman Sharma",
            phone: "9876543101",
            status: "Active",
            opening: "12 Jan 2024",
        },
        {
            name: "Greater Noida Pari Chowk",
            code: "DD-GNO-02",
            city: "Greater Noida",
            state: "Uttar Pradesh",
            owner: "Rajat Mehra",
            manager: "Rishi Kohli",
            phone: "9876543102",
            status: "Active",
            opening: "18 Mar 2025",
        },
        {
            name: "Gurugram Badshahpur",
            code: "DD-GGN-03",
            city: "Gurugram",
            state: "Haryana",
            owner: "Fractional Network",
            manager: "Anjali Gupta",
            phone: "9876543103",
            status: "Active",
            opening: "15 Jan 2026",
        },
        {
            name: "Mumbai Goregaon",
            code: "DD-MUM-04",
            city: "Mumbai",
            state: "Maharashtra",
            owner: "Franchise Partner",
            manager: "Kunal Mehta",
            phone: "9876543104",
            status: "Active",
            opening: "06 Feb 2026",
        },
        {
            name: "Raj Nagar Ghaziabad",
            code: "DD-GZB-05",
            city: "Ghaziabad",
            state: "Uttar Pradesh",
            owner: "Franchise Partner",
            manager: "Priya Verma",
            phone: "9876543105",
            status: "Coming Soon",
            opening: "15 Sep 2026",
        },
        {
            name: "Vaishali Sector 4",
            code: "DD-GZB-06",
            city: "Ghaziabad",
            state: "Uttar Pradesh",
            owner: "Franchise Partner",
            manager: "TBD",
            phone: "9876543106",
            status: "Coming Soon",
            opening: "01 Nov 2026",
        },
    ];

    const titles = {
        dashboard: ["Dashboard", "SALES OPERATIONS"],
        priority: ["Priority Queue", "LIVE SALES FLOOR"],
        followups: ["Follow-ups", "CUSTOMER TOUCHPOINTS"],
        tasks: ["Assigned Tasks", "TEAM EXECUTION"],
        leads: ["All Leads", "LEAD DATABASE"],
        pipeline: ["Lead Pipeline", "SALES PIPELINE"],
        reports: ["Reports", "PERFORMANCE INTELLIGENCE"],
        studios: ["Studios", "FRANCHISE NETWORK"],
        property: ["Rent Property", "SITE ACQUISITION"],
        import: ["Import Leads", "DATA INTAKE"],
        export: ["Export Leads", "DATA DELIVERY"],
        settings: ["Settings", "WORKSPACE CONTROL"],
    };

    const formatDate = (value) =>
        new Intl.DateTimeFormat("en-IN", { day: "2-digit", month: "short", year: "numeric" }).format(new Date(value));
    const formatDateTime = (value) =>
        new Intl.DateTimeFormat("en-IN", { day: "2-digit", month: "short", hour: "2-digit", minute: "2-digit" }).format(
            new Date(value)
        );
    const initials = (name = "") =>
        name
            .split(/\s+/)
            .map((p) => p[0])
            .join("")
            .slice(0, 2)
            .toUpperCase();
    const digits = (value = "") => String(value).replace(/\D/g, "");
    const slugStatus = (status = "") => status.toLowerCase().replace(/\s+/g, "");
    const escapeHtml = (value = "") =>
        String(value).replace(
            /[&<>'"]/g,
            (char) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", "'": "&#39;", '"': "&quot;" })[char]
        );
    const downloadBlob = (content, filename, type = "text/plain;charset=utf-8") => {
        const url = URL.createObjectURL(new Blob([content], { type }));
        const a = document.createElement("a");
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        a.remove();
        URL.revokeObjectURL(url);
    };

    function buildDemoLeads() {
        const now = Date.now();
        return Array.from({ length: 100 }, (_, i) => {
            const name = `${firstNames[i % firstNames.length]} ${lastNames[(i * 7) % lastNames.length]}`;
            const city = cities[(i * 5) % cities.length];
            const status = statuses[(i * 3) % statuses.length];
            const score = 42 + ((i * 11) % 57);
            const created = new Date(now - (i % 48) * 86400000 - (i % 8) * 3600000);
            const next = new Date(now + ((i % 11) - 4) * 3600000 + (i % 5) * 86400000);
            const phone = `9${String(100000000 + ((i * 7919) % 899999999)).padStart(9, "0")}`;
            return {
                id: `DDL-26${String(i + 1).padStart(4, "0")}`,
                name,
                phone,
                whatsapp: phone,
                alternate: "",
                email: `${name.toLowerCase().replace(/\s+/g, ".")}@example.com`,
                city,
                state: statesByCity[city] || "Delhi",
                pincode: String(110001 + i),
                studio: studios[i % studios.length].name,
                type: i % 6 === 0 ? "Customer" : "Franchise",
                source: sources[(i * 2) % sources.length],
                interest: interests[(i * 4) % interests.length],
                budget: budgets[(i * 3) % budgets.length],
                model: models[(i * 2) % models.length],
                timeline: timelines[i % timelines.length],
                score,
                temperature: score >= 80 ? "Hot" : score >= 60 ? "Warm" : "Cold",
                status,
                agent: agents[(i * 3) % agents.length],
                createdAt: created.toISOString(),
                updatedAt: created.toISOString(),
                lastContact: new Date(created.getTime() + 86400000).toISOString(),
                nextFollowup: next.toISOString(),
                notes:
                    i % 3 === 0
                        ? "Requested franchise investment details and location availability."
                        : "Initial enquiry captured by sales team.",
                activities: [
                    {
                        type: "Lead Created",
                        at: created.toISOString(),
                        text: `Lead received from ${sources[(i * 2) % sources.length]}.`,
                    },
                    {
                        type: "Assigned",
                        at: new Date(created.getTime() + 15 * 60000).toISOString(),
                        text: `Assigned to ${agents[(i * 3) % agents.length]}.`,
                    },
                ],
            };
        });
    }

    let leads = (() => {
        try {
            return JSON.parse(localStorage.getItem(STORAGE.leads)) || buildDemoLeads();
        } catch {
            return buildDemoLeads();
        }
    })();
    let leadPage = 1;
    let leadView = "list";
    let priorityStatus = "All";
    let currentPage = "dashboard";
    let taskTab = "Today";
    let followupPageTab = "Today";
    let followupDrawerTab = "Today";
    let advancedFilters = { city: "", agent: "", score: 0, date: "" };
    let importRows = [];
    let selectedLeadId = null;
    let openingEditDrawer = false;

    const tasks = Array.from({ length: 28 }, (_, i) => ({
        id: `TASK-${i + 1}`,
        leadId: leads[i % leads.length].id,
        title: i % 3 === 0 ? "Send franchise proposal" : i % 3 === 1 ? "Follow-up call" : "Schedule site visit",
        type: i % 4 === 0 ? "Meeting" : i % 3 === 0 ? "Email" : "Call",
        status: ["Today", "Upcoming", "Overdue", "Completed"][i % 4],
        priority: i % 5 === 0 ? "High" : "Normal",
        dueAt: new Date(Date.now() + ((i % 9) - 4) * 3600000 + Math.floor(i / 9) * 86400000).toISOString(),
        agent: agents[i % agents.length],
    }));

    const followups = Array.from({ length: 38 }, (_, i) => ({
        id: `FU-${i + 1}`,
        leadId: leads[(i * 2) % leads.length].id,
        type: ["Call", "WhatsApp", "Email", "Meeting", "Site Visit"][i % 5],
        status: ["Today", "Upcoming", "Overdue", "Completed"][i % 4],
        priority: i % 6 === 0 ? "High" : "Normal",
        description:
            i % 2
                ? "Discuss investment timeline and city availability."
                : "Share proposal and confirm next decision step.",
        dueAt: new Date(Date.now() + ((i % 13) - 5) * 3600000 + Math.floor(i / 13) * 86400000).toISOString(),
        agent: agents[(i * 2) % agents.length],
    }));

    const properties = [
        ["Noida Sector 104", "1,850 sq ft · Main road", "Site visit scheduled", "₹1.75L/mo"],
        ["Raj Nagar Extension", "2,000 sq ft · Corner unit", "Negotiation", "₹1.45L/mo"],
        ["Vaishali Sector 4", "1,620 sq ft · 24 ft frontage", "Documents pending", "₹1.90L/mo"],
        ["Gurugram Sector 67", "1,900 sq ft · Ground floor", "Shortlisted", "₹2.10L/mo"],
    ];

    function saveLeads() {
        localStorage.setItem(STORAGE.leads, JSON.stringify(leads));
    }
    function statusBadge(value) {
        return `<span class="status-badge ${slugStatus(value)}">${escapeHtml(value)}</span>`;
    }
    function temperatureBadge(value) {
        return `<span class="status-badge ${String(value).toLowerCase()}">${escapeHtml(value)}</span>`;
    }
    function agentChip(value) {
        return `<span class="agent-chip"><span class="mini-avatar">${initials(value)}</span><span>${escapeHtml(value)}</span></span>`;
    }
    function getLead(id) {
        return leads.find((lead) => lead.id === id);
    }

    function showPage(id, updateHash = true) {
        if (!titles[id]) id = "dashboard";
        currentPage = id;
        $$(".crm-page").forEach((page) => page.classList.toggle("active", page.id === id));
        $$(".side-nav button,[data-page].user-card").forEach((button) =>
            button.classList.toggle("active", button.dataset.page === id)
        );
        $("pageTitle").textContent = titles[id][0];
        $("pageEyebrow").textContent = titles[id][1];
        $("sidebar").classList.remove("open");
        $("navBackdrop").classList.remove("show");
        document.body.classList.remove("nav-open");
        if (updateHash) history.replaceState(null, "", `#${id}`);
        window.scrollTo({ top: 0, behavior: "auto" });
        renderPage(id);
    }

    function renderPage(id) {
        const renderers = {
            dashboard: renderDashboard,
            priority: renderPriority,
            followups: renderFollowups,
            tasks: renderTasks,
            leads: renderLeads,
            pipeline: renderPipeline,
            reports: renderReports,
            studios: renderStudios,
            property: renderProperties,
            import: renderImport,
            export: renderExport,
        };
        renderers[id]?.();
    }

    function populateSelect(selectId, values) {
        const el = $(selectId);
        if (!el) return;
        const first = el.querySelector("option")?.outerHTML || "";
        el.innerHTML =
            first +
            values.map((value) => `<option value="${escapeHtml(value)}">${escapeHtml(value)}</option>`).join("");
    }

    function renderDashboard() {
        const active = leads.filter((l) => !["Converted", "Lost"].includes(l.status)).length;
        const today = new Date().toDateString();
        const newToday = leads.filter((l) => new Date(l.createdAt).toDateString() === today).length;
        const due = leads.filter(
            (l) => new Date(l.nextFollowup) <= new Date() && !["Converted", "Lost"].includes(l.status)
        ).length;
        const converted = leads.filter((l) => l.status === "Converted").length;
        const conversion = ((converted / leads.length) * 100).toFixed(1);
        const kpis = [
            ["Total active leads", active.toLocaleString("en-IN"), "+8.2%", "bi-people", "ink"],
            ["New leads today", newToday || 14, "+18.4%", "bi-person-plus", "red"],
            ["Follow-ups due", due, `${Math.min(due, 9)} overdue`, "bi-telephone", "amber"],
            ["Conversion rate", `${conversion}%`, "+2.1%", "bi-graph-up-arrow", "green"],
        ];
        $("kpiGrid").innerHTML = kpis
            .map(
                (k) =>
                    `<article class="kpi ${k[4]}"><div class="kpi-icon"><i class="bi ${k[3]}"></i></div><small>${k[0]}</small><strong>${k[1]}</strong><span>${k[2]}</span></article>`
            )
            .join("");
        $("todayLabel").textContent = new Intl.DateTimeFormat("en-IN", {
            weekday: "long",
            day: "numeric",
            month: "long",
            year: "numeric",
        })
            .format(new Date())
            .toUpperCase();
        renderChart();
        const focus = [
            ["Overdue follow-ups", due, "red", "bi-alarm", "followups"],
            [
                "Hot leads untouched",
                leads.filter((l) => l.temperature === "Hot" && Date.now() - new Date(l.lastContact) > 86400000).length,
                "amber",
                "bi-fire",
                "priority",
            ],
            [
                "Leads awaiting assignment",
                leads.filter((l) => !l.agent).length || 4,
                "blue",
                "bi-person-exclamation",
                "leads",
            ],
            [
                "Proposals pending",
                leads.filter((l) => l.status === "Proposal Sent").length,
                "purple",
                "bi-file-earmark-text",
                "pipeline",
            ],
        ];
        $("focusList").innerHTML = focus
            .map(
                (a) =>
                    `<button class="focus-row focus-button" type="button" data-go="${a[4]}"><span class="focus-icon ${a[2]}"><i class="bi ${a[3]}"></i></span><span><strong>${a[0]}</strong><small>Requires action today</small></span><b>${a[1]}</b><i class="bi bi-chevron-right"></i></button>`
            )
            .join("");
        $("recentTable").innerHTML = leadTable([...leads].sort((a, b) => b.score - a.score).slice(0, 6), true);
    }

    function renderChart() {
        const days = Number($("chartPeriod")?.value || 7);
        const labels =
            days === 7 ? ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"] : ["W1", "W2", "W3", "W4", "W5", "Now"];
        const values = labels.map((_, i) => 48 + ((i * 17 + days) % 45));
        $("leadChart").innerHTML = labels
            .map(
                (label, i) =>
                    `<div class="chart-group"><div class="chart-bar new" style="height:${values[i]}%" title="New leads: ${Math.round(values[i] * 1.8)}"></div><div class="chart-bar qualified" style="height:${Math.max(18, values[i] - 22)}%" title="Qualified: ${Math.round(values[i] * 1.1)}"></div><small>${label}</small></div>`
            )
            .join("");
        $("chartTotal").textContent = `${leads.length.toLocaleString("en-IN")} total`;
    }

    function leadTable(rows, compact = false) {
        if (!rows.length) return emptyState("No leads found", "Try changing your search or filters.");
        const desktop = `<div class="table-responsive table-desktop"><table class="crm-table"><thead><tr><th>Lead</th><th>Mobile</th><th>City</th><th>Status</th><th>Source</th><th>Assigned to</th><th>Score</th>${compact ? "" : "<th>Next follow-up</th>"}<th></th></tr></thead><tbody>${rows.map((l) => `<tr><td><button class="lead-ident link-reset" type="button" data-view-lead="${l.id}"><span class="mini-avatar">${initials(l.name)}</span><span><strong>${escapeHtml(l.name)}</strong><small>${l.id}</small></span></button></td><td>${escapeHtml(l.phone)}</td><td>${escapeHtml(l.city)}</td><td>${statusBadge(l.status)}</td><td>${escapeHtml(l.source)}</td><td>${agentChip(l.agent || "Unassigned")}</td><td><span class="score-pill">${l.score}</span></td>${compact ? "" : `<td>${formatDateTime(l.nextFollowup)}</td>`}<td><div class="dropdown"><button class="table-action" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Lead actions"><i class="bi bi-three-dots"></i></button><ul class="dropdown-menu dropdown-menu-end"><li><button class="dropdown-item" type="button" data-view-lead="${l.id}">View details</button></li><li><button class="dropdown-item" type="button" data-edit-lead="${l.id}">Edit lead</button></li><li><a class="dropdown-item" href="tel:${l.phone}">Call</a></li><li><a class="dropdown-item" target="_blank" rel="noopener" href="https://wa.me/91${digits(l.whatsapp || l.phone).slice(-10)}">WhatsApp</a></li><li><button class="dropdown-item text-danger" type="button" data-delete-lead="${l.id}">Delete</button></li></ul></div></td></tr>`).join("")}</tbody></table></div>`;
        const mobile = `<div class="mobile-records">${rows.map(mobileLeadCard).join("")}</div>`;
        return desktop + mobile;
    }

    function mobileLeadCard(l) {
        return `<article class="mobile-record"><div class="mobile-record-head"><div><h3>${escapeHtml(l.name)}</h3><span class="lead-id">${l.id}</span></div>${temperatureBadge(l.temperature)}</div><dl><div><dt>Phone</dt><dd>${escapeHtml(l.phone)}</dd></div><div><dt>Email</dt><dd>${escapeHtml(l.email)}</dd></div><div><dt>City</dt><dd>${escapeHtml(l.city)}</dd></div><div><dt>Source</dt><dd>${escapeHtml(l.source)}</dd></div><div><dt>Interested in</dt><dd>${escapeHtml(l.interest)}</dd></div><div><dt>Budget</dt><dd>${escapeHtml(l.budget)}</dd></div><div><dt>Status</dt><dd>${statusBadge(l.status)}</dd></div><div><dt>Assigned user</dt><dd>${escapeHtml(l.agent || "Unassigned")}</dd></div><div><dt>Created</dt><dd>${formatDate(l.createdAt)}</dd></div><div><dt>Next follow-up</dt><dd>${formatDateTime(l.nextFollowup)}</dd></div></dl><div class="mobile-actions"><button class="btn btn-dark" type="button" data-view-lead="${l.id}"><i class="bi bi-eye"></i> View</button><a class="btn btn-light" href="tel:${l.phone}"><i class="bi bi-telephone"></i> Call</a><a class="btn btn-light" target="_blank" rel="noopener" href="https://wa.me/91${digits(l.whatsapp || l.phone).slice(-10)}"><i class="bi bi-whatsapp"></i> WhatsApp</a><button class="btn btn-light" type="button" data-edit-lead="${l.id}"><i class="bi bi-pencil"></i> Edit</button></div></article>`;
    }

    function getFilteredLeads() {
        const search = ($("leadSearch")?.value || "").trim().toLowerCase();
        const status = $("leadStatusFilter")?.value || "";
        const source = $("leadSourceFilter")?.value || "";
        return leads.filter((l) => {
            const haystack = `${l.id} ${l.name} ${l.phone} ${l.email} ${l.city} ${l.agent}`.toLowerCase();
            const dateOk = !advancedFilters.date || new Date(l.createdAt) >= new Date(advancedFilters.date);
            return (
                (!search || haystack.includes(search)) &&
                (!status || l.status === status) &&
                (!source || l.source === source) &&
                (!advancedFilters.city || l.city.toLowerCase().includes(advancedFilters.city.toLowerCase())) &&
                (!advancedFilters.agent || l.agent === advancedFilters.agent) &&
                l.score >= Number(advancedFilters.score || 0) &&
                dateOk
            );
        });
    }

    function renderLeads() {
        const rows = getFilteredLeads();
        const perPage = 12;
        const pages = Math.max(1, Math.ceil(rows.length / perPage));
        leadPage = Math.min(leadPage, pages);
        const slice = rows.slice((leadPage - 1) * perPage, leadPage * perPage);
        $("allLeadTable").innerHTML =
            leadView === "list"
                ? leadTable(slice)
                : `<div class="compact-grid">${slice.map(mobileLeadCard).join("")}</div>`;
        $("resultCount").textContent = `${rows.length} results`;
        $("showingCount").textContent = rows.length
            ? `Showing ${(leadPage - 1) * perPage + 1}–${Math.min(leadPage * perPage, rows.length)} of ${rows.length}`
            : "No records";
        $("leadPagination").innerHTML =
            `<button type="button" data-lead-page="${leadPage - 1}" ${leadPage === 1 ? "disabled" : ""}><i class="bi bi-chevron-left"></i></button>${Array.from(
                { length: Math.min(5, pages) },
                (_, i) => i + Math.max(1, Math.min(leadPage - 2, pages - 4))
            )
                .map(
                    (p) =>
                        `<button type="button" class="${p === leadPage ? "active" : ""}" data-lead-page="${p}">${p}</button>`
                )
                .join(
                    ""
                )}<button type="button" data-lead-page="${leadPage + 1}" ${leadPage === pages ? "disabled" : ""}><i class="bi bi-chevron-right"></i></button>`;
    }

    function renderPriority() {
        const q = ($("prioritySearch").value || "").toLowerCase();
        const agent = $("priorityAgentFilter").value || "";
        const rows = [...leads]
            .filter(
                (l) =>
                    !["Converted", "Lost"].includes(l.status) &&
                    (priorityStatus === "All" || l.temperature === priorityStatus || l.status === priorityStatus) &&
                    (!agent || l.agent === agent) &&
                    `${l.name} ${l.phone} ${l.city}`.toLowerCase().includes(q)
            )
            .sort((a, b) => priorityWeight(b) - priorityWeight(a))
            .slice(0, 24);
        $("priorityGrid").innerHTML = rows.length
            ? rows
                  .map(
                      (l, i) =>
                          `<article class="lead-card"><div class="lead-top"><div class="mini-avatar">${initials(l.name)}</div><div><h3>${escapeHtml(l.name)}</h3><p><i class="bi bi-geo-alt"></i> ${escapeHtml(l.city)} · ${escapeHtml(l.source)}</p></div>${temperatureBadge(l.temperature)}</div><div class="contact-line"><a href="tel:${l.phone}"><i class="bi bi-telephone"></i>${l.phone}</a><a href="mailto:${l.email}"><i class="bi bi-envelope"></i>${l.email}</a></div><div class="due-box"><div><small>NEXT FOLLOW-UP</small><strong>${formatDateTime(l.nextFollowup)}</strong></div><span class="${new Date(l.nextFollowup) < new Date() ? "late" : ""}">${new Date(l.nextFollowup) < new Date() ? "OVERDUE" : `PRIORITY #${i + 1}`}</span></div><div class="lead-card-foot">${agentChip(l.agent)}<div><button class="btn btn-light" type="button" data-view-lead="${l.id}"><i class="bi bi-eye"></i></button><a class="btn btn-danger" href="tel:${l.phone}"><i class="bi bi-telephone-fill"></i> Call now</a></div></div></article>`
                  )
                  .join("")
            : emptyState("No matching priority leads", "Adjust the search or queue filters.");
        $("priorityCountNav").textContent = String(Math.min(rows.length, 99));
    }

    function priorityWeight(l) {
        const overdueHours = Math.max(0, (Date.now() - new Date(l.nextFollowup).getTime()) / 3600000);
        const budgetWeight = budgets.indexOf(l.budget) * 8;
        return (
            l.score +
            overdueHours * 2 +
            budgetWeight +
            (l.temperature === "Hot" ? 20 : l.temperature === "Warm" ? 10 : 0)
        );
    }

    function renderFollowups() {
        const q = ($("followupSearch")?.value || "").toLowerCase();
        const type = $("followupTypeFilter")?.value || "";
        const rows = followups
            .filter((f) => f.status === followupPageTab && (!type || f.type === type))
            .filter((f) => {
                const l = getLead(f.leadId);
                return `${l?.name || ""} ${l?.phone || ""} ${f.description}`.toLowerCase().includes(q);
            });
        $("followupSummary").innerHTML = summaryCards(followups, "status");
        $("followupResultCount").textContent = `${rows.length} follow-ups`;
        $("followupTable").innerHTML = followupTable(rows);
        $("followupCountNav").textContent = String(
            followups.filter((f) => f.status === "Today" || f.status === "Overdue").length
        );
    }

    function followupTable(rows) {
        if (!rows.length) return emptyState("No follow-ups here", "This list is currently clear.");
        const desktop = `<div class="table-responsive table-desktop"><table class="crm-table"><thead><tr><th>Lead</th><th>Date & time</th><th>Type</th><th>Description</th><th>Assigned agent</th><th>Priority</th><th>Actions</th></tr></thead><tbody>${rows
            .map((f) => {
                const l = getLead(f.leadId);
                return `<tr><td><button class="lead-ident link-reset" type="button" data-view-lead="${l.id}"><span class="mini-avatar">${initials(l.name)}</span><span><strong>${escapeHtml(l.name)}</strong><small>${l.phone}</small></span></button></td><td>${formatDateTime(f.dueAt)}</td><td>${escapeHtml(f.type)}</td><td>${escapeHtml(f.description)}</td><td>${agentChip(f.agent)}</td><td>${f.priority === "High" ? '<span class="status-badge hot">High</span>' : '<span class="status-badge cold">Normal</span>'}</td><td><button class="btn btn-sm btn-dark" type="button" data-complete-followup="${f.id}">Complete</button></td></tr>`;
            })
            .join("")}</tbody></table></div>`;
        const mobile = `<div class="mobile-records">${rows
            .map((f) => {
                const l = getLead(f.leadId);
                return `<article class="mobile-record"><div class="mobile-record-head"><div><h3>${escapeHtml(l.name)}</h3><span class="lead-id">${l.id}</span></div>${f.priority === "High" ? '<span class="status-badge hot">High</span>' : '<span class="status-badge cold">Normal</span>'}</div><dl><div><dt>Date</dt><dd>${formatDateTime(f.dueAt)}</dd></div><div><dt>Type</dt><dd>${f.type}</dd></div><div><dt>Assigned</dt><dd>${f.agent}</dd></div><div><dt>Description</dt><dd>${f.description}</dd></div></dl><div class="mobile-actions"><button class="btn btn-dark" type="button" data-view-lead="${l.id}">View lead</button><a class="btn btn-light" href="tel:${l.phone}">Call</a><button class="btn btn-danger" type="button" data-complete-followup="${f.id}">Complete</button></div></article>`;
            })
            .join("")}</div>`;
        return desktop + mobile;
    }

    function summaryCards(items, key) {
        const map = [
            ["Today", "red"],
            ["Upcoming", "blue"],
            ["Overdue", "amber"],
            ["Completed", "green"],
        ];
        return map
            .map(
                ([label, cls]) =>
                    `<div class="${cls}"><strong>${items.filter((item) => item[key] === label).length}</strong><span>${label}</span></div>`
            )
            .join("");
    }

    function renderTasks() {
        $("taskSummary").innerHTML = summaryCards(tasks, "status");
        const rows = tasks.filter((task) => task.status === taskTab);
        $("taskList").innerHTML = rows.length
            ? rows
                  .map((task) => {
                      const l = getLead(task.leadId);
                      const time = new Intl.DateTimeFormat("en-IN", { hour: "numeric", minute: "2-digit" }).format(
                          new Date(task.dueAt)
                      );
                      return `<article><div class="task-time ${task.priority === "High" ? "urgent" : ""}"><strong>${time.replace(/\s?(am|pm)/i, "")}</strong><span>${/pm/i.test(time) ? "PM" : "AM"}</span></div><div class="task-main"><span class="task-type"><i class="bi ${task.type === "Call" ? "bi-telephone" : task.type === "Email" ? "bi-envelope" : "bi-calendar-event"}"></i> ${task.type}</span><h3>${escapeHtml(l.name)}</h3><p>${escapeHtml(task.title)} · ${escapeHtml(l.city)}</p></div><div class="task-owner"><small>ASSIGNED TO</small>${agentChip(task.agent)}</div>${task.priority === "High" ? '<span class="status-badge hot">High</span>' : '<span class="status-badge cold">Normal</span>'}<div class="task-actions"><button type="button" data-complete-task="${task.id}" aria-label="Complete task"><i class="bi bi-check2"></i></button><button type="button" data-view-lead="${l.id}" aria-label="View lead"><i class="bi bi-three-dots"></i></button></div></article>`;
                  })
                  .join("")
            : emptyState("No tasks here", "This task list is currently clear.");
    }

    function renderPipeline() {
        const agent = $("pipelineAgentFilter").value || "";
        const rows = agent ? leads.filter((l) => l.agent === agent) : leads;
        $("mobileStageSelect").innerHTML = statuses.map((status) => `<option>${status}</option>`).join("");
        $("kanbanBoard").innerHTML = statuses
            .map((status, index) => {
                const cards = rows.filter((l) => l.status === status).slice(0, 18);
                return `<article class="kanban-column ${index === 0 ? "mobile-active" : ""}" data-stage="${status}"><div class="kanban-head"><h3>${status}</h3><span>${cards.length}</span></div><div class="kanban-list" data-stage-list="${status}">${cards.map((l) => `<article class="kanban-card" draggable="true" data-lead-id="${l.id}"><h4>${escapeHtml(l.name)}</h4><p>${escapeHtml(l.city)} · ${escapeHtml(l.budget)}</p><div class="kanban-card-meta">${temperatureBadge(l.temperature)}<span class="score-pill">${l.score}</span></div></article>`).join("")}</div></article>`;
            })
            .join("");
        bindKanban();
    }

    function bindKanban() {
        $$(".kanban-card").forEach((card) =>
            card.addEventListener("dragstart", (e) => e.dataTransfer.setData("text/plain", card.dataset.leadId))
        );
        $$(".kanban-list").forEach((list) => {
            list.addEventListener("dragover", (e) => e.preventDefault());
            list.addEventListener("drop", (e) => {
                e.preventDefault();
                const id = e.dataTransfer.getData("text/plain");
                const lead = getLead(id);
                if (!lead) return;
                const previous = lead.status;
                lead.status = list.dataset.stageList;
                lead.updatedAt = new Date().toISOString();
                lead.activities.unshift({
                    type: "Status Changed",
                    at: lead.updatedAt,
                    text: `${previous} → ${lead.status}`,
                });
                saveLeads();
                renderPipeline();
                toast("Pipeline updated", `${lead.name} moved to ${lead.status}.`);
            });
        });
    }

    function renderReports() {
        const total = leads.length,
            qualified = leads.filter((l) =>
                ["Qualified", "Proposal Sent", "Negotiation", "Site Visit", "Converted"].includes(l.status)
            ).length,
            converted = leads.filter((l) => l.status === "Converted").length,
            lost = leads.filter((l) => l.status === "Lost").length;
        const kpis = [
            ["Total leads", total, "+8.2%", "bi-people", "ink"],
            ["Qualified", qualified, `${((qualified / total) * 100).toFixed(1)}%`, "bi-patch-check", "green"],
            ["Converted", converted, `${((converted / total) * 100).toFixed(1)}%`, "bi-trophy", "red"],
            ["Lost", lost, `${((lost / total) * 100).toFixed(1)}%`, "bi-x-circle", "amber"],
        ];
        $("reportKpis").innerHTML = kpis
            .map(
                (k) =>
                    `<article class="kpi ${k[4]}"><div class="kpi-icon"><i class="bi ${k[3]}"></i></div><small>${k[0]}</small><strong>${k[1]}</strong><span>${k[2]}</span></article>`
            )
            .join("");
        $("sourceReport").innerHTML = barReport(
            sources.map((source) => [source, leads.filter((l) => l.source === source).length])
        );
        const cityCounts = cities
            .map((city) => [city, leads.filter((l) => l.city === city && l.status === "Converted").length])
            .sort((a, b) => b[1] - a[1])
            .slice(0, 8);
        $("cityReport").innerHTML = barReport(cityCounts);
        $("agentReport").innerHTML = agentTable();
    }

    function barReport(items) {
        const max = Math.max(1, ...items.map((i) => i[1]));
        return `<div class="report-bars">${items
            .filter((i) => i[1] > 0)
            .map(
                ([name, value]) =>
                    `<div class="report-bar-row"><span>${escapeHtml(name)}</span><div class="report-track"><div class="report-fill" style="width:${Math.max(5, (value / max) * 100)}%"></div></div><strong>${value}</strong></div>`
            )
            .join("")}</div>`;
    }

    function agentTable() {
        const rows = agents.map((agent) => {
            const owned = leads.filter((l) => l.agent === agent);
            const converted = owned.filter((l) => l.status === "Converted").length;
            const qualified = owned.filter((l) =>
                ["Qualified", "Proposal Sent", "Negotiation", "Site Visit", "Converted"].includes(l.status)
            ).length;
            return {
                agent,
                assigned: owned.length,
                qualified,
                converted,
                rate: owned.length ? ((converted / owned.length) * 100).toFixed(1) : "0.0",
                pending: followups.filter((f) => f.agent === agent && f.status !== "Completed").length,
            };
        });
        return `<div class="table-responsive"><table class="crm-table"><thead><tr><th>Agent</th><th>Assigned</th><th>Qualified</th><th>Converted</th><th>Conversion</th><th>Pending follow-ups</th></tr></thead><tbody>${rows.map((r) => `<tr><td>${agentChip(r.agent)}</td><td>${r.assigned}</td><td>${r.qualified}</td><td>${r.converted}</td><td>${r.rate}%</td><td>${r.pending}</td></tr>`).join("")}</tbody></table></div>`;
    }

    function directoryCard(item, icon, status = "Active") {
        return `<article class="panel directory-card"><div class="directory-icon"><i class="bi ${icon}"></i></div><span class="availability"><i></i>${escapeHtml(status)}</span><h3>${escapeHtml(item[0])}</h3><p>${escapeHtml(item[1])}</p><div class="directory-meta"><span>${escapeHtml(item[2])}</span><strong>${escapeHtml(item[3])}</strong></div><button class="btn btn-light" type="button">View details <i class="bi bi-arrow-up-right"></i></button></article>`;
    }

    function renderStudios() {
        $("studioGrid").innerHTML = studios
            .map((s) =>
                directoryCard(
                    [s.name, `${s.code} · ${s.city}, ${s.state}`, `${s.manager} · ${s.phone}`, s.opening],
                    "bi-shop-window",
                    s.status
                )
            )
            .join("");
    }
    function renderProperties() {
        $("propertyGrid").innerHTML = properties.map((p) => directoryCard(p, "bi-buildings", "Active")).join("");
    }
    

    function renderImport() {
        $("importStats").innerHTML = [
            ["New Records", importRows.filter((r) => r._status === "valid").length, "green"],
            ["Duplicate Records", importRows.filter((r) => r._status === "duplicate").length, "amber"],
            ["Invalid Records", importRows.filter((r) => r._status === "invalid").length, "red"],
            ["Total Rows", importRows.length, "blue"],
        ]
            .map(([label, value, cls]) => `<div class="${cls}"><strong>${value}</strong><span>${label}</span></div>`)
            .join("");
        $("importPreviewPanel").hidden = !importRows.length;
        $("confirmImportBtn").disabled = !importRows.some((r) => r._status === "valid");
        $("importPreview").innerHTML = importRows.length ? importPreviewTable(importRows.slice(0, 50)) : "";
    }

    function importPreviewTable(rows) {
        return `<div class="table-responsive"><table class="crm-table"><thead><tr><th>Status</th><th>Name</th><th>Mobile</th><th>Email</th><th>City</th><th>Source</th></tr></thead><tbody>${rows.map((r) => `<tr><td><span class="import-status ${r._status}">${r._status}</span></td><td>${escapeHtml(r.name || "")}</td><td>${escapeHtml(r.mobile || r.phone || "")}</td><td>${escapeHtml(r.email || "")}</td><td>${escapeHtml(r.city || "")}</td><td>${escapeHtml(r.source || "")}</td></tr>`).join("")}</tbody></table></div>`;
    }

    function renderExport() {
        const fields = [
            "id",
            "name",
            "phone",
            "email",
            "city",
            "state",
            "source",
            "interest",
            "budget",
            "status",
            "score",
            "agent",
            "nextFollowup",
            "createdAt",
        ];
        if (!$("exportFields").children.length)
            $("exportFields").innerHTML = fields
                .map(
                    (field) =>
                        `<label class="field-option"><input type="checkbox" value="${field}" checked> ${humanize(field)}</label>`
                )
                .join("");
        const rows = getExportRows();
        $("exportRecordCount").textContent = rows.length.toLocaleString("en-IN");
    }

    function getExportRows() {
        const status = $("exportStatus")?.value || "",
            source = $("exportSource")?.value || "",
            agent = $("exportAgent")?.value || "",
            city = ($("exportCity")?.value || "").toLowerCase();
        return leads.filter(
            (l) =>
                (!status || l.status === status) &&
                (!source || l.source === source) &&
                (!agent || l.agent === agent) &&
                (!city || l.city.toLowerCase().includes(city))
        );
    }

    function humanize(value) {
        return value.replace(/([A-Z])/g, " $1").replace(/^./, (c) => c.toUpperCase());
    }


    function calculateScore(data) {
        let score = 35;
        score += Math.max(0, budgets.indexOf(data.budget)) * 9;
        score += { Immediate: 22, "1 Month": 16, "3 Months": 10, "6 Months": 5, "12+ Months": 0 }[data.timeline] || 0;
        if (["Referral", "Prime Franchise", "Franchise Enquiry"].includes(data.source)) score += 9;
        if (data.email) score += 4;
        if (data.interest === "Franchise") score += 6;
        return Math.min(99, score);
    }

    function autoAssign(city) {
        const preferred =
            city === "Mumbai" || city === "Pune"
                ? "Kunal Mehta"
                : city === "Chandigarh" || city === "Amritsar"
                  ? "Priya Verma"
                  : null;
        if (preferred) return preferred;
        const loads = agents.map((agent) => [
            agent,
            leads.filter((l) => l.agent === agent && !["Converted", "Lost"].includes(l.status)).length,
        ]);
        return loads.sort((a, b) => a[1] - b[1])[0][0];
    }

    function updateLeadPreview() {
        const data = getLeadFormData();
        const score = calculateScore(data);
        const temp = score >= 80 ? "Hot" : score >= 60 ? "Warm" : "Cold";
        $("leadScorePreview").textContent = score;
        $("leadTempPreview").textContent = temp;
        $("assignmentText").textContent =
            `${data.name || "This lead"} will be routed to the best available agent based on ${data.city ? `${data.city}, ` : ""}availability and workload.`;
    }

    function getLeadFormData() {
        return {
            name: $("newName").value.trim(),
            phone: digits($("newMobile").value).slice(-10),
            whatsapp: digits($("newWhatsapp").value).slice(-10),
            alternate: digits($("newAlternate").value).slice(-10),
            email: $("newEmail").value.trim(),
            city: $("newCity").value.trim(),
            state: $("newState").value,
            pincode: $("newPincode").value.trim(),
            studio: $("newStudio").value,
            type: $("newType").value,
            source: $("newSource").value,
            interest: $("newInterest").value,
            budget: $("newBudget").value,
            model: $("newModel").value,
            timeline: $("newTimeline").value,
            assignee: $("newAssignee").value,
        };
    }

    function validateLeadForm(data) {
        $$(".field-error", $("leadForm")).forEach((e) => (e.textContent = ""));
        let valid = true;
        const error = (id, message) => {
            const field = $(id);
            const target = field.parentElement.querySelector(".field-error");
            if (target) target.textContent = message;
            field.classList.add("is-invalid");
            valid = false;
        };
        $$(".is-invalid", $("leadForm")).forEach((e) => e.classList.remove("is-invalid"));
        if (!data.name) error("newName", "Full name is required.");
        if (data.phone.length !== 10) error("newMobile", "Enter a valid 10-digit mobile number.");
        if (!data.city) error("newCity", "City is required.");
        if (!data.state) error("newState", "State is required.");
        if (data.email && !/^\S+@\S+\.\S+$/.test(data.email)) error("newEmail", "Enter a valid email address.");
        const duplicate = leads.find(
            (l) =>
                l.id !== $("editLeadId").value &&
                (digits(l.phone).slice(-10) === data.phone ||
                    (data.email && l.email.toLowerCase() === data.email.toLowerCase()))
        );
        if (duplicate) {
            error("newMobile", `Duplicate lead: ${duplicate.name} (${duplicate.id}).`);
        }
        return valid;
    }

    function resetLeadDrawer() {
        $("leadForm").reset();
        $("editLeadId").value = "";
        $("addLeadLabel").textContent = "Add a new lead";
        $("leadDrawerEyebrow").textContent = "NEW OPPORTUNITY";
        $("leadDrawerSubtitle").textContent = "Create and assign a lead to the right sales owner.";
        $("leadSubmitText").textContent = "Create lead";
        $("newStudio").innerHTML =
            `<option value="">Select preferred studio</option>${studios.map((s) => `<option>${s.name}</option>`).join("")}`;
        $("newAssignee").innerHTML =
            `<option value="auto">Auto assign</option>${agents.map((a) => `<option>${a}</option>`).join("")}`;
        updateLeadPreview();
    }

    function openEditLead(id) {
        const l = getLead(id);
        if (!l) return;
        resetLeadDrawer();
        $("editLeadId").value = l.id;
        $("addLeadLabel").textContent = "Edit lead";
        $("leadDrawerEyebrow").textContent = "UPDATE OPPORTUNITY";
        $("leadDrawerSubtitle").textContent = `Update ${l.name}'s lead information.`;
        $("leadSubmitText").textContent = "Save changes";
        const map = {
            newName: l.name,
            newMobile: l.phone,
            newWhatsapp: l.whatsapp,
            newAlternate: l.alternate,
            newEmail: l.email,
            newCity: l.city,
            newState: l.state,
            newPincode: l.pincode,
            newStudio: l.studio,
            newType: l.type,
            newSource: l.source,
            newInterest: l.interest,
            newBudget: l.budget,
            newModel: l.model,
            newTimeline: l.timeline,
            newAssignee: l.agent,
        };
        Object.entries(map).forEach(([idKey, value]) => {
            if ($(idKey)) $(idKey).value = value || "";
        });
        updateLeadPreview();
        openingEditDrawer = true;
        bootstrap.Offcanvas.getOrCreateInstance($("addLead")).show();
    }

    function openLeadDetail(id) {
        const l = getLead(id);
        if (!l) return;
        selectedLeadId = id;
        $("leadDetailTitle").textContent = l.name;
        $("leadDetailSubtitle").textContent = `${l.id} · ${l.city} · Assigned to ${l.agent || "Unassigned"}`;
        $("leadDetailBody").innerHTML =
            `<div class="detail-score"><div><small>LEAD SCORE</small><strong>${l.score}/100</strong></div><div><small>TEMPERATURE</small><strong>${temperatureBadge(l.temperature)}</strong></div><div><small>STATUS</small><strong>${statusBadge(l.status)}</strong></div></div><div class="quick-actions"><a class="btn btn-dark" href="tel:${l.phone}"><i class="bi bi-telephone"></i> Call</a><a class="btn btn-light" target="_blank" rel="noopener" href="https://wa.me/91${digits(l.whatsapp || l.phone).slice(-10)}"><i class="bi bi-whatsapp"></i> WhatsApp</a><a class="btn btn-light" href="mailto:${l.email}"><i class="bi bi-envelope"></i> Email</a><button class="btn btn-danger" type="button" data-edit-lead="${l.id}"><i class="bi bi-pencil"></i> Edit</button></div><section class="detail-section"><h3>Overview</h3><dl class="detail-grid"><div><dt>Mobile</dt><dd>${l.phone}</dd></div><div><dt>Email</dt><dd>${escapeHtml(l.email)}</dd></div><div><dt>Location</dt><dd>${escapeHtml(l.city)}, ${escapeHtml(l.state)}</dd></div><div><dt>Lead source</dt><dd>${escapeHtml(l.source)}</dd></div><div><dt>Interested in</dt><dd>${escapeHtml(l.interest)}</dd></div><div><dt>Budget</dt><dd>${escapeHtml(l.budget)}</dd></div><div><dt>Franchise model</dt><dd>${escapeHtml(l.model)}</dd></div><div><dt>Timeline</dt><dd>${escapeHtml(l.timeline)}</dd></div><div><dt>Preferred studio</dt><dd>${escapeHtml(l.studio || "Not selected")}</dd></div><div><dt>Next follow-up</dt><dd>${formatDateTime(l.nextFollowup)}</dd></div></dl></section><section class="detail-section"><h3>Internal note</h3><p>${escapeHtml(l.notes)}</p></section><section class="detail-section"><h3>Activity timeline</h3><div class="timeline">${(l.activities || []).map((a) => `<div class="timeline-item"><span class="timeline-dot"></span><div><strong>${escapeHtml(a.type)}</strong><p>${escapeHtml(a.text)}</p><small>${formatDateTime(a.at)}</small></div></div>`).join("")}</div></section>`;
        bootstrap.Offcanvas.getOrCreateInstance($("leadDetail")).show();
    }

    function parseCsv(text) {
        const rows = [];
        let row = [],
            cell = "",
            quoted = false;
        for (let i = 0; i < text.length; i++) {
            const char = text[i],
                next = text[i + 1];
            if (char === '"' && quoted && next === '"') {
                cell += '"';
                i++;
            } else if (char === '"') quoted = !quoted;
            else if (char === "," && !quoted) {
                row.push(cell.trim());
                cell = "";
            } else if ((char === "\n" || char === "\r") && !quoted) {
                if (char === "\r" && next === "\n") i++;
                row.push(cell.trim());
                if (row.some(Boolean)) rows.push(row);
                row = [];
                cell = "";
            } else cell += char;
        }
        if (cell || row.length) {
            row.push(cell.trim());
            rows.push(row);
        }
        if (rows.length < 2) return [];
        const headers = rows.shift().map((h) =>
            h
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, " ")
                .trim()
        );
        const aliases = {
            "full name": "name",
            "lead name": "name",
            name: "name",
            mobile: "mobile",
            "mobile number": "mobile",
            phone: "mobile",
            email: "email",
            "email address": "email",
            city: "city",
            state: "state",
            source: "source",
            "lead source": "source",
            status: "status",
            budget: "budget",
            "investment budget": "budget",
            agent: "agent",
            "assigned agent": "agent",
            interest: "interest",
            "interested in": "interest",
        };
        return rows.map((values) =>
            Object.fromEntries(headers.map((h, i) => [aliases[h] || h.replace(/\s+/g, ""), values[i] || ""]))
        );
    }

    async function handleLeadFile(file) {
        if (!file) return;
        if (file.size > 10 * 1024 * 1024) return toast("File too large", "Please upload a file below 10 MB.", "error");
        let rows = [];
        try {
            if (/\.csv$/i.test(file.name)) rows = parseCsv(await file.text());
            else if (window.SimpleXLSX) {
                rows = await SimpleXLSX.read(await file.arrayBuffer());
                rows = rows.map((r) =>
                    Object.fromEntries(
                        Object.entries(r).map(([k, v]) => [k.toLowerCase().replace(/[^a-z0-9]+/g, ""), String(v)])
                    )
                );
            } else
                return toast(
                    "Excel reader unavailable",
                    "Use CSV or verify that the bundled XLSX helper loaded correctly.",
                    "error"
                );
            importRows = rows.map(validateImportRow);
            $("selectedFileMeta").hidden = false;
            $("selectedFileMeta").innerHTML =
                `<strong>${escapeHtml(file.name)}</strong><small>${rows.length} rows detected · ${(file.size / 1024).toFixed(1)} KB</small>`;
            renderImport();
        } catch (error) {
            console.error(error);
            toast("Unable to read file", "Check the file format and try again.", "error");
        }
    }

    function validateImportRow(row) {
        const normalized = {
            name: row.name || row.fullname || row.leadname || "",
            mobile: digits(row.mobile || row.mobilenumber || row.phone || "").slice(-10),
            email: row.email || row.emailaddress || "",
            city: row.city || "",
            state: row.state || "",
            source: row.source || row.leadsource || "Import",
            status: row.status || "New",
            budget: row.budget || row.investmentbudget || "₹10–25 Lakh",
            agent: row.agent || row.assignedagent || "",
            interest: row.interest || row.interestedin || "Franchise",
        };
        if (!normalized.name || normalized.mobile.length !== 10) return { ...normalized, _status: "invalid" };
        const duplicate = leads.some(
            (l) =>
                digits(l.phone).slice(-10) === normalized.mobile ||
                (normalized.email && l.email.toLowerCase() === normalized.email.toLowerCase())
        );
        return { ...normalized, _status: duplicate ? "duplicate" : "valid" };
    }

    async function exportRows(format) {
        const rows = getExportRows();
        const fields = $$("#exportFields input:checked").map((i) => i.value);
        if (!fields.length) return toast("Choose export fields", "Select at least one field.", "error");
        const data = rows.map((l) =>
            Object.fromEntries(
                fields.map((field) => [
                    humanize(field),
                    ["createdAt", "nextFollowup"].includes(field) ? formatDateTime(l[field]) : l[field],
                ])
            )
        );
        if (format === "csv") {
            const headers = Object.keys(data[0] || {});
            const csv = [headers, ...data.map((row) => headers.map((h) => row[h]))]
                .map((row) => row.map((v) => `"${String(v ?? "").replace(/"/g, '""')}"`).join(","))
                .join("\n");
            downloadBlob(
                "\ufeff" + csv,
                `lead-crm-${new Date().toISOString().slice(0, 10)}.csv`,
                "text/csv;charset=utf-8"
            );
        } else if (window.SimpleXLSX) {
            await SimpleXLSX.write(data, `lead-crm-${new Date().toISOString().slice(0, 10)}.xlsx`);
        } else {
            const headers = Object.keys(data[0] || {});
            const html = `<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel"><head><meta charset="utf-8"></head><body><table><tr>${headers.map((h) => `<th>${escapeHtml(h)}</th>`).join("")}</tr>${data.map((row) => `<tr>${headers.map((h) => `<td>${escapeHtml(row[h] ?? "")}</td>`).join("")}</tr>`).join("")}</table></body></html>`;
            downloadBlob(html, `lead-crm-${new Date().toISOString().slice(0, 10)}.xls`, "application/vnd.ms-excel");
        }
        toast("Export ready", `${rows.length} lead records were exported.`);
    }

    function emptyState(title, text) {
        return `<div class="empty-state"><i class="bi bi-inbox"></i><strong>${title}</strong><p>${text}</p></div>`;
    }
    function toast(title, text, icon = "success") {
        return Swal.fire({
            toast: true,
            position: "top-end",
            title,
            text,
            icon,
            timer: 2600,
            showConfirmButton: false,
        });
    }

    function renderNotifications() {
        const items = [
            [
                "bi-alarm",
                `${followups.filter((f) => f.status === "Overdue").length} follow-ups are overdue.`,
                "Review the overdue list now.",
            ],
            [
                "bi-fire",
                `${leads.filter((l) => l.temperature === "Hot").length} hot leads are active.`,
                "Prioritize the highest scores first.",
            ],
            ["bi-person-plus", "New franchise lead received.", "Website · New Delhi · 2 minutes ago"],
            ["bi-calendar-event", "Follow-up scheduled for Prithvi Kaushik.", "Today at 4:00 PM"],
        ];
        $("notificationList").innerHTML = items
            .map(
                ([icon, title, desc]) =>
                    `<article class="notification-item"><i class="bi ${icon}"></i><div><strong>${title}</strong><small>${desc}</small></div></article>`
            )
            .join("");
        $("notificationCount").textContent = "4";
    }

    function renderGlobalSearch() {
        const q = $("globalSearchInput").value.trim().toLowerCase();
        const rows = q
            ? leads
                  .filter((l) =>
                      `${l.id} ${l.name} ${l.phone} ${l.email} ${l.city} ${l.agent}`.toLowerCase().includes(q)
                  )
                  .slice(0, 8)
            : [];
        $("globalSearchResults").innerHTML = q
            ? rows.length
                ? rows
                      .map(
                          (l) =>
                              `<button class="global-result link-reset" type="button" data-view-lead="${l.id}" data-bs-dismiss="modal"><span class="mini-avatar">${initials(l.name)}</span><span><strong>${escapeHtml(l.name)}</strong><small>${l.id} · ${l.phone} · ${l.city}</small></span>${temperatureBadge(l.temperature)}</button>`
                      )
                      .join("")
                : emptyState("No results", "Try another name, mobile number, city or lead ID.")
            : '<p class="muted">Start typing to search all leads.</p>';
    }

    // Initial population
    if ($("statusChips")) {

    populateSelect("leadStatusFilter", statuses);
    populateSelect("leadSourceFilter", sources);
    populateSelect("priorityAgentFilter", agents);
    populateSelect("pipelineAgentFilter", agents);
    populateSelect("exportStatus", statuses);
    populateSelect("exportSource", sources);
    populateSelect("exportAgent", agents);
    populateSelect("advancedAgent", agents);
    $("newStudio").innerHTML =
        `<option value="">Select preferred studio</option>${studios.map((s) => `<option>${s.name}</option>`).join("")}`;
    $("newAssignee").innerHTML =
        `<option value="auto">Auto assign</option>${agents.map((a) => `<option>${a}</option>`).join("")}`;
    $("statusChips").innerHTML = ["All", "Hot", "Warm", "Cold", "New"]
        .map(
            (f) =>
                `<button type="button" data-priority-status="${f}" class="${f === "All" ? "active" : ""}">${f}${f === "All" ? `<b>${leads.length}</b>` : ""}</button>`
        )
        .join("");
    renderNotifications();
    renderImport();
    renderExport();
    resetLeadDrawer();
}

    // Navigation and shell
    $("openNav").addEventListener("click", () => {
        $("sidebar").classList.add("open");
        $("navBackdrop").classList.add("show");
        document.body.classList.add("nav-open");
    });
    [$("closeNav"), $("navBackdrop")].forEach((el) =>
        el.addEventListener("click", () => {
            $("sidebar").classList.remove("open");
            $("navBackdrop").classList.remove("show");
            document.body.classList.remove("nav-open");
        })
    );
    window.addEventListener("hashchange", () => showPage(location.hash.slice(1) || "dashboard", false));

    // Dashboard
    $("chartPeriod").addEventListener("change", renderChart);
    $("dashboardExportBtn").addEventListener("click", () => {
        showPage("export");
    });
    $("reportExportBtn").addEventListener("click", () => {
        showPage("export");
    });

    // Search and notifications
    $("notificationBtn").addEventListener("click", () =>
        bootstrap.Offcanvas.getOrCreateInstance($("notificationDrawer")).show()
    );
    $("globalSearchBtn").addEventListener("click", () => {
        bootstrap.Modal.getOrCreateInstance($("globalSearchModal")).show();
        setTimeout(() => $("globalSearchInput").focus(), 250);
    });
    $("globalSearchInput").addEventListener("input", renderGlobalSearch);

    // Priority
    $("prioritySearch").addEventListener("input", renderPriority);
    $("priorityAgentFilter").addEventListener("change", renderPriority);
    $("statusChips").addEventListener("click", (e) => {
        const button = e.target.closest("[data-priority-status]");
        if (!button) return;
        priorityStatus = button.dataset.priorityStatus;
        $$("#statusChips button").forEach((b) => b.classList.toggle("active", b === button));
        renderPriority();
    });
    let queueSeconds = 15;
    setInterval(() => {
        if (currentPage !== "priority") return;
        queueSeconds--;
        if (queueSeconds <= 0) {
            queueSeconds = 15;
            $("queueState").textContent = "Fetching live data…";
            setTimeout(() => {
                renderPriority();
                $("queueState").textContent = "Live data";
                $("queueUpdated").textContent = `Updated ${new Date().toLocaleTimeString("en-IN")}`;
            }, 400);
        }
        $("queueCountdown").textContent = `Refresh in ${queueSeconds}s`;
    }, 1000);

    // Leads filters and views
    ["leadSearch", "leadStatusFilter", "leadSourceFilter"].forEach((id) =>
        $(id).addEventListener(id === "leadSearch" ? "input" : "change", () => {
            leadPage = 1;
            renderLeads();
        })
    );
    $("listViewBtn").addEventListener("click", () => {
        leadView = "list";
        $("listViewBtn").classList.add("active");
        $("compactViewBtn").classList.remove("active");
        renderLeads();
    });
    $("compactViewBtn").addEventListener("click", () => {
        leadView = "compact";
        $("compactViewBtn").classList.add("active");
        $("listViewBtn").classList.remove("active");
        renderLeads();
    });
    $("leadPagination").addEventListener("click", (e) => {
        const b = e.target.closest("[data-lead-page]");
        if (!b || b.disabled) return;
        leadPage = Number(b.dataset.leadPage);
        renderLeads();
    });
    $("advancedScore").addEventListener(
        "input",
        () => ($("advancedScoreLabel").textContent = `${$("advancedScore").value}+`)
    );
    $("applyAdvancedFilters").addEventListener("click", () => {
        advancedFilters = {
            city: $("advancedCity").value.trim(),
            agent: $("advancedAgent").value,
            score: $("advancedScore").value,
            date: $("advancedDate").value,
        };
        leadPage = 1;
        renderLeads();
        renderPriority();
    });
    $("resetAdvancedFilters").addEventListener("click", () => {
        ["advancedCity", "advancedAgent", "advancedDate"].forEach((id) => ($(id).value = ""));
        $("advancedScore").value = 0;
        $("advancedScoreLabel").textContent = "0+";
        advancedFilters = { city: "", agent: "", score: 0, date: "" };
        renderLeads();
        renderPriority();
    });

    // Delegated lead actions
    document.addEventListener("click", async (e) => {
        const nav = e.target.closest("[data-go],[data-page]");
        if (nav) {
            showPage(nav.dataset.go || nav.dataset.page);
            return;
        }
        const view = e.target.closest("[data-view-lead]");
        if (view) return openLeadDetail(view.dataset.viewLead);
        const edit = e.target.closest("[data-edit-lead]");
        if (edit) {
            bootstrap.Offcanvas.getInstance($("leadDetail"))?.hide();
            setTimeout(() => openEditLead(edit.dataset.editLead), 160);
            return;
        }
        const del = e.target.closest("[data-delete-lead]");
        if (del) {
            const l = getLead(del.dataset.deleteLead);
            const result = await Swal.fire({
                title: "Delete lead?",
                text: `${l.name} will be removed from this browser demo.`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Delete",
                confirmButtonColor: "#ef1b23",
            });
            if (result.isConfirmed) {
                leads = leads.filter((x) => x.id !== l.id);
                saveLeads();
                renderPage(currentPage);
                toast("Lead deleted", `${l.name} was deleted.`);
            }
            return;
        }
        const task = e.target.closest("[data-complete-task]");
        if (task) {
            const item = tasks.find((t) => t.id === task.dataset.completeTask);
            item.status = "Completed";
            renderTasks();
            toast("Task completed", item.title);
            return;
        }
        const follow = e.target.closest("[data-complete-followup]");
        if (follow) {
            const item = followups.find((f) => f.id === follow.dataset.completeFollowup);
            item.status = "Completed";
            renderFollowups();
            toast("Follow-up completed", "The follow-up is now marked completed.");
        }
    });

    // Lead form
    ["newName", "newCity", "newEmail", "newSource", "newInterest", "newBudget", "newTimeline"].forEach((id) =>
        $(id).addEventListener(
            id.startsWith("new") && ["newSource", "newInterest", "newBudget", "newTimeline"].includes(id)
                ? "change"
                : "input",
            updateLeadPreview
        )
    );
    $("addLead").addEventListener("show.bs.offcanvas", () => {
        if (openingEditDrawer) {
            openingEditDrawer = false;
            return;
        }
        resetLeadDrawer();
    });
    $("leadForm").addEventListener("submit", async (e) => {
        e.preventDefault();
        const data = getLeadFormData();
        if (!validateLeadForm(data)) return;
        const button = $("leadSubmitBtn");
        button.disabled = true;
        $("leadSubmitText").textContent = $("editLeadId").value ? "Saving…" : "Creating…";
        await new Promise((resolve) => setTimeout(resolve, 450));
        const score = calculateScore(data),
            temperature = score >= 80 ? "Hot" : score >= 60 ? "Warm" : "Cold",
            agent = data.assignee === "auto" ? autoAssign(data.city) : data.assignee;
        if ($("editLeadId").value) {
            const l = getLead($("editLeadId").value);
            Object.assign(l, data, { agent, score, temperature, updatedAt: new Date().toISOString() });
            l.activities.unshift({ type: "Lead Updated", at: l.updatedAt, text: "Lead details updated." });
            toast("Lead updated", `${l.name} was updated successfully.`);
        } else {
            const now = new Date();
            const id = `DDL-26${String(Math.max(0, ...leads.map((l) => Number(l.id.replace(/\D/g, "").slice(-4)))) + 1).padStart(4, "0")}`;
            const l = {
                id,
                ...data,
                agent,
                score,
                temperature,
                status: "New",
                createdAt: now.toISOString(),
                updatedAt: now.toISOString(),
                lastContact: now.toISOString(),
                nextFollowup: new Date(now.getTime() + 4 * 3600000).toISOString(),
                notes: "New lead created from CRM.",
                activities: [
                    { type: "Lead Created", at: now.toISOString(), text: `Lead created from ${data.source}.` },
                    { type: "Assigned", at: now.toISOString(), text: `Assigned to ${agent}.` },
                ],
            };
            leads.unshift(l);
            toast("Lead created", `${l.name} was assigned to ${agent}.`);
        }
        saveLeads();
        bootstrap.Offcanvas.getInstance($("addLead")).hide();
        button.disabled = false;
        $("leadSubmitText").textContent = "Create lead";
        renderPage(currentPage);
        renderDashboard();
    });

    // Follow-ups and tasks
    $$("#followupPageTabs button").forEach((b) =>
        b.addEventListener("click", () => {
            followupPageTab = b.dataset.followStatus;
            $$("#followupPageTabs button").forEach((x) => x.classList.toggle("active", x === b));
            renderFollowups();
        })
    );
    $("followupSearch").addEventListener("input", renderFollowups);
    $("followupTypeFilter").addEventListener("change", renderFollowups);
    $("addFollowupBtn").addEventListener("click", async () => {
        const result = await Swal.fire({
            title: "Add follow-up",
            html: `<input id="swalLead" class="swal2-input" placeholder="Lead name"><select id="swalType" class="swal2-select"><option>Call</option><option>WhatsApp</option><option>Email</option><option>Meeting</option><option>Site Visit</option></select><input id="swalDate" type="datetime-local" class="swal2-input">`,
            showCancelButton: true,
            confirmButtonText: "Save follow-up",
            confirmButtonColor: "#ef1b23",
            preConfirm: () => ({ lead: $("swalLead").value, type: $("swalType").value, date: $("swalDate").value }),
        });
        if (result.isConfirmed) toast("Follow-up added", `A ${result.value.type} follow-up was scheduled.`);
    });
    $$("#taskTabs button").forEach((b) =>
        b.addEventListener("click", () => {
            taskTab = b.dataset.taskTab;
            $$("#taskTabs button").forEach((x) => x.classList.toggle("active", x === b));
            renderTasks();
        })
    );
    $("taskCalendarBtn").addEventListener("click", () =>
        Swal.fire({
            title: "Calendar view",
            text: "Calendar mode is included as a demo interaction in this HTML build.",
            icon: "info",
            confirmButtonColor: "#ef1b23",
        })
    );

    // Priority follow-up drawer
    const openFollowupDrawer = () => {
        $("followUpShell").classList.add("show");
        $("followUpShell").setAttribute("aria-hidden", "false");
        document.body.style.overflow = "hidden";
        renderFollowupDrawer();
    };
    const closeFollowupDrawer = () => {
        $("followUpShell").classList.remove("show");
        $("followUpShell").setAttribute("aria-hidden", "true");
        document.body.style.overflow = "";
    };
    $("openFollowUps").addEventListener("click", openFollowupDrawer);
    $("closeFollowUps").addEventListener("click", closeFollowupDrawer);
    $("followUpBackdrop").addEventListener("click", closeFollowupDrawer);
    $$("#followUpTabs button").forEach((b) =>
        b.addEventListener("click", () => {
            followupDrawerTab = b.dataset.followTab;
            $$("#followUpTabs button").forEach((x) => x.classList.toggle("active", x === b));
            renderFollowupDrawer();
        })
    );
    function renderFollowupDrawer() {
        const rows = followups.filter((f) => f.status === followupDrawerTab).slice(0, 8);
        $("followUpList").innerHTML = rows.length
            ? rows
                  .map((f) => {
                      const l = getLead(f.leadId);
                      return `<article><div class="followup-card-head"><h3>${escapeHtml(l.name)} <span>(${l.phone})</span></h3><em>${f.status}</em></div><p>City: ${escapeHtml(l.city)}</p><strong>Follow-up type: <span>${f.type}</span></strong><dl><div><dt><i class="bi bi-calendar-event"></i> Scheduled:</dt><dd>${formatDateTime(f.dueAt)}</dd></div><div><dt><i class="bi bi-person-fill"></i> Assigned:</dt><dd>${f.agent}</dd></div></dl><div><button class="btn btn-info btn-sm" type="button" data-view-lead="${l.id}"><i class="bi bi-search"></i> Open lead</button><a class="btn btn-secondary btn-sm" href="tel:${l.phone}"><i class="bi bi-telephone"></i> Call</a></div></article>`;
                  })
                  .join("")
            : emptyState("No follow-ups", "This list is clear.");
    }

    // Pipeline
    $("pipelineAgentFilter").addEventListener("change", renderPipeline);
    $("mobileStageSelect").addEventListener("change", () => {
        $$(".kanban-column").forEach((c) =>
            c.classList.toggle("mobile-active", c.dataset.stage === $("mobileStageSelect").value)
        );
    });

    // Import
    $("browseFileBtn").addEventListener("click", () => $("leadFileInput").click());
    $("leadFileInput").addEventListener("change", () => handleLeadFile($("leadFileInput").files[0]));
    ["dragenter", "dragover"].forEach((event) =>
        $("leadDropzone").addEventListener(event, (e) => {
            e.preventDefault();
            $("leadDropzone").classList.add("dragover");
        })
    );
    ["dragleave", "drop"].forEach((event) =>
        $("leadDropzone").addEventListener(event, (e) => {
            e.preventDefault();
            $("leadDropzone").classList.remove("dragover");
        })
    );
    $("leadDropzone").addEventListener("drop", (e) => handleLeadFile(e.dataTransfer.files[0]));
    $("leadDropzone").addEventListener("keydown", (e) => {
        if (e.key === "Enter" || e.key === " ") $("leadFileInput").click();
    });
    $("downloadTemplateBtn").addEventListener("click", () =>
        downloadBlob(
            "Full Name,Mobile Number,Email Address,City,State,Lead Source,Interested In,Investment Budget,Status,Assigned Agent\nRahul Mehra,9876543210,rahul@example.com,New Delhi,Delhi,Website,Franchise,₹25–50 Lakh,New,Aman Sharma",
            "sample-lead-import-template.csv",
            "text/csv;charset=utf-8"
        )
    );
    $("confirmImportBtn").addEventListener("click", () => {
        const valid = importRows.filter((r) => r._status === "valid");
        const now = new Date();
        valid.forEach((r, i) => {
            const score = calculateScore({
                budget: r.budget,
                timeline: "3 Months",
                source: r.source,
                email: r.email,
                interest: r.interest,
            });
            leads.unshift({
                id: `DDL-IMP-${Date.now()}-${i}`,
                name: r.name,
                phone: r.mobile,
                whatsapp: r.mobile,
                alternate: "",
                email: r.email,
                city: r.city,
                state: r.state,
                pincode: "",
                studio: "",
                type: "Franchise",
                source: r.source,
                interest: r.interest,
                budget: r.budget,
                model: "Prime Franchise",
                timeline: "3 Months",
                score,
                temperature: score >= 80 ? "Hot" : score >= 60 ? "Warm" : "Cold",
                status: statuses.includes(r.status) ? r.status : "New",
                agent: r.agent || autoAssign(r.city),
                createdAt: now.toISOString(),
                updatedAt: now.toISOString(),
                lastContact: now.toISOString(),
                nextFollowup: new Date(now.getTime() + 4 * 3600000).toISOString(),
                notes: "Imported lead.",
                activities: [
                    { type: "Lead Imported", at: now.toISOString(), text: "Imported through CSV/Excel workflow." },
                ],
            });
        });
        saveLeads();
        importRows = [];
        $("selectedFileMeta").hidden = true;
        $("leadFileInput").value = "";
        renderImport();
        toast("Import complete", `${valid.length} leads were imported.`);
    });

    // Export
    ["exportStatus", "exportSource", "exportAgent"].forEach((id) => $(id).addEventListener("change", renderExport));
    $("exportCity").addEventListener("input", renderExport);
    $("exportFields").addEventListener("change", renderExport);
    $("exportCsvBtn").addEventListener("click", () => exportRows("csv"));
    $("exportXlsxBtn").addEventListener("click", () => exportRows("xlsx"));


    [
        ["addStudioBtn", "Add Studio"],
        ["addPropertyBtn", "Add Property"],
        ["addEmployeeBtn", "Add Employee"],
    ].forEach(([id, title]) =>
        $(id).addEventListener("click", () =>
            Swal.fire({
                title,
                input: "text",
                inputLabel: "Name",
                showCancelButton: true,
                confirmButtonColor: "#ef1b23",
            }).then((r) => {
                if (r.isConfirmed && r.value) toast(`${title.replace("Add ", "")} added`, r.value);
            })
        )
    );

    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") closeFollowupDrawer();
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === "k") {
            e.preventDefault();
            $("globalSearchBtn").click();
        }
    });
    setInterval(() => {
        const sec = Math.floor((Date.now() / 1000) % 59);
        $("lastSyncText").textContent = `Last sync ${sec || 1} seconds ago`;
    }, 1000);

    // Show drawer once per browser session, preserving the original minimal build behavior without recurring annoyance.
    if (!sessionStorage.getItem(STORAGE.drawerSeen)) {
        sessionStorage.setItem(STORAGE.drawerSeen, "1");
        setTimeout(openFollowupDrawer, 900);
    }

    showPage(location.hash.slice(1) || "dashboard", false);
})();
