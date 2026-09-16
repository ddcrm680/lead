/**
 * ----------------------------------------
 * DOM Helpers
 * ----------------------------------------
 */

window.$ = function (selector, context = document) {
    return context.querySelector(selector);
};

window.$$ = function (selector, context = document) {
    return context.querySelectorAll(selector);
};

/**
 * Escape HTML special characters.
 */
window.escapeHtml = function (value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
};

/**
 * Render reusable pagination.
 */
window.renderPagination = function ({
    paginationElement,
    showingElement,
    perPageElement,
    currentPage = 1,
    lastPage = 1,
    perPage = 25,
    total = 0,
    from = 0,
    to = 0,
    attribute = 'data',
    perPageOptions = [25, 50, 100],
}) {
    /**
     * ----------------------------------------
     * Showing Count
     * ----------------------------------------
     */

    if (showingElement) {
        showingElement.textContent = total
            ? `Showing ${from}-${to} of ${total}`
            : 'No records';
    }

    /**
     * ----------------------------------------
     * Per Page Options
     * ----------------------------------------
     */

    if (perPageElement) {
        const currentOptions = Array.from(
            perPageElement.options
        ).map((option) => Number(option.value));

        const optionsChanged =
            JSON.stringify(currentOptions) !==
            JSON.stringify(perPageOptions);

        const selectedValue =
            Number(perPageElement.value);

        if (
            optionsChanged ||
            selectedValue !== Number(perPage)
        ) {
            perPageElement.innerHTML = perPageOptions
                .map((option) => `
                    <option
                        value="${option}"
                        ${Number(option) === Number(perPage) ? 'selected' : ''}
                    >
                        ${option}
                    </option>
                `)
                .join('');
        }
    }

    /**
     * ----------------------------------------
     * Pagination Controls
     * ----------------------------------------
     */

    if (!paginationElement) {
        return;
    }

    if (lastPage <= 1) {
        paginationElement.innerHTML = '';

        return;
    }

    const pageWindow = Math.min(
        5,
        lastPage
    );

    const startPage = Math.max(
        1,
        Math.min(
            currentPage - Math.floor(pageWindow / 2),
            lastPage - pageWindow + 1
        )
    );

    const pages = Array.from(
        {
            length: pageWindow,
        },
        (_, index) => startPage + index
    );

    paginationElement.innerHTML = `
        <button
            type="button"
            data-${attribute}-page="${currentPage - 1}"
            ${currentPage <= 1 ? 'disabled' : ''}
        >
            <i class="bi bi-chevron-left"></i>
        </button>

        ${pages
            .map((page) => `
                <button
                    type="button"
                    class="${page === currentPage ? 'active' : ''}"
                    data-${attribute}-page="${page}"
                >
                    ${page}
                </button>
            `)
            .join('')}

        <button
            type="button"
            data-${attribute}-page="${currentPage + 1}"
            ${currentPage >= lastPage ? 'disabled' : ''}
        >
            <i class="bi bi-chevron-right"></i>
        </button>
    `;
};



/**
 * ----------------------------------------
 * Render Reusable Loading Skeleton
 * ----------------------------------------
 */

window.renderSkeleton = function ({
    element,
    type = 'card',
    count = 4,
    columns = 5,
}) {
    if (!element) {
        return;
    }

    if (type === 'card') {
        element.innerHTML = `
            <div class="priority-grid">
                ${Array.from(
                    { length: count },
                    () => `
                        <article class="panel skeleton-card">

                            <div class="skeleton-card-top">
                                <div class="skeleton skeleton-card-header"></div>
                                <div class="skeleton skeleton-card-status"></div>
                            </div>

                            <div class="skeleton skeleton-card-title"></div>

                            <div class="skeleton skeleton-card-text"></div>

                            <div class="skeleton skeleton-card-text short"></div>

                            <div class="skeleton skeleton-card-content"></div>

                            <div class="skeleton skeleton-card-footer"></div>

                        </article>
                    `
                ).join('')}
            </div>
        `;

        return;
    }
    if (type === 'row') {
        element.innerHTML = Array.from(
            { length: count },
            () => `
                <div
                    class="skeleton-row"
                    style="--skeleton-columns: ${columns};"
                >
                    ${Array.from(
                        { length: columns },
                        () => `
                            <div class="skeleton skeleton-cell"></div>
                        `
                    ).join('')}
                </div>
            `
        ).join('');
    }
};


