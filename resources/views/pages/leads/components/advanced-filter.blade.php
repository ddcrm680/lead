<div
    class="offcanvas offcanvas-end filter-drawer"
    tabindex="-1"
    id="leadFilterDrawer"
    aria-labelledby="filterDrawerTitle"
>
    <div class="offcanvas-header">
        <div>
            <span class="eyebrow">REFINE RESULTS</span>
            <h2 id="filterDrawerTitle">Advanced Filters</h2>
        </div>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="offcanvas"
            aria-label="Close"
        ></button>
    </div>

    <div class="offcanvas-body">
        <div class="form-section">

            <label>
                Status
                <select class="form-select" id="advancedStatus">
                    <option value="">Any status</option>

                    @foreach ($statuses as $status)
                        <option value="{{ $status->id }}">
                            {{ $status->name }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label>
                Source
                <select class="form-select" id="advancedSource">
                    <option value="">Any source</option>

                    @foreach ($sources as $source)
                        <option value="{{ $source->id }}">
                            {{ $source->name }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label>
                City
                <input
                    class="form-control"
                    id="advancedCity"
                    type="text"
                    placeholder="Any city"
                    autocomplete="off"
                >
            </label>

            <label>
                State
                <input
                    class="form-control"
                    id="advancedState"
                    type="text"
                    placeholder="Any state"
                    autocomplete="off"
                >
            </label>

            <label>
                Country
                <input
                    class="form-control"
                    id="advancedCountry"
                    type="text"
                    placeholder="Any country"
                    autocomplete="off"
                >
            </label>

            <label>
                Stage
                <select class="form-select" id="advancedPipelineStage">
                    <option value="">Any stage</option>

                    @foreach ($pipelines as $pipeline)
                        @foreach ($pipeline->stages as $stage)
                            <option
                                value="{{ $stage->id }}"
                                data-pipeline-id="{{ $pipeline->id }}"
                            >
                            {{ $stage->name }}
                            </option>
                        @endforeach
                    @endforeach
                </select>
            </label>

            <label>
                Assigned agent
                <select class="form-select" id="advancedAgent">
                    <option value="">Any agent</option>

                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label>
                Priority
                <select class="form-select" id="advancedPriority">
                    <option value="">Any priority</option>

                    @foreach ($priorities as $priority)
                        <option value="{{ $priority->value }}">
                            {{ $priority->name }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label>
                Tags
                <select
                    class="form-select"
                    id="advancedTags"
                    multiple
                >
                    @foreach ($tags as $tag)
                        <option value="{{ $tag->id }}">
                            {{ $tag->name }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label>
                Follow-up type
                <select class="form-select" id="advancedFollowUpType">
                    <option value="">Any follow-up type</option>

                    @foreach ($followUpTypes as $followUpType)
                        <option value="{{ $followUpType->id }}">
                            {{ $followUpType->name }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label>
                Follow-up status
                <select class="form-select" id="advancedFollowUpStatus">
                    <option value="">Any follow-up status</option>

                    @foreach ($followUpStatuses as $followUpStatus)
                        <option value="{{ $followUpStatus->id }}">
                            {{ $followUpStatus->name }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label>
                Created after
                <input
                    class="form-control"
                    id="advancedCreatedAfter"
                    type="date"
                >
            </label>

            <label>
                Created before
                <input
                    class="form-control"
                    id="advancedCreatedBefore"
                    type="date"
                >
            </label>

        </div>
    </div>

    <div class="modal-foot">
        <button
            class="btn btn-light"
            id="resetAdvancedFilters"
            type="button"
        >
            Reset
        </button>

        <button
            class="btn btn-danger"
            id="applyAdvancedFilters"
            type="button"
            data-bs-dismiss="offcanvas"
        >
            Apply filters
        </button>
    </div>
</div>
