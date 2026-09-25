@extends('layouts.app')

@section('title', 'Leads')

@section('page-eyebrow', 'LEAD DATABASE')

@section('page-title', 'Leads')

@push('css')

@endpush

@section('content')

      <section id="leads"
      aria-labelledby="leadsHeading"
      data-leads-data-url="{{ route('leadsData') }}"
      data-create-url="{{ route('createLead') }}"
      data-view-url="{{ route('viewLead', ['lead' => '__LEAD__']) }}"
      data-edit-url="{{ route('editLead', ['lead' => '__LEAD__']) }}"
      >

        <div class="page-stack">

          <section class="page-intro">
            <div>
              <span class="eyebrow">LEAD DATABASE</span>
              <h2 id="leadsHeading">All Leads</h2>
              <p>Search, filter and manage every lead.</p>
            </div>

            <div class="action-row">
              <button class="btn btn-outline-dark" type="button" data-go="import">
                <i class="bi bi-upload"></i> Import </button>
              <button class="btn btn-dark" type="button" data-go="export">
                <i class="bi bi-download"></i> Export </button>
            </div>

          </section>

          <section class="panel">
            <div class="table-toolbar leads-toolbar">

              <div class="searchbox">
                <i class="bi bi-search"></i>
                <input id="leadSearch" type="search" placeholder="Search by name, mobile, city or lead ID" autocomplete="off">
              </div>


                <select
                    class="form-select"
                    id="leadStatusFilter"
                >
                    <option value="">
                        All statuses
                    </option>

                    @foreach ($statuses as $status)
                        <option value="{{ $status->id }}">
                            {{ $status->name }}
                        </option>
                    @endforeach
                </select>

                <select
                    class="form-select"
                    id="leadSourceFilter"
                >
                    <option value="">
                        All sources
                    </option>

                    @foreach ($sources as $source)
                        <option value="{{ $source->id }}">
                            {{ $source->name }}
                        </option>
                    @endforeach
                </select>

              <button class="btn btn-outline-dark" type="button" data-bs-toggle="offcanvas" data-bs-target="#leadFilterDrawer">
                <i class="bi bi-funnel"></i> Advanced </button>

              <div class="view-toggle" role="group" aria-label="Lead view">
                <button class="active" id="listViewBtn" type="button" aria-label="List view">
                  <i class="bi bi-list-ul"></i>
                </button>

                <button id="compactViewBtn" type="button" aria-label="Compact card view">
                  <i class="bi bi-grid"></i>
                </button>

              </div>
            </div>

            <div id="leadList"></div>

          </section>

        </div>
      </section>


{{-- lead advanced filter --}}
@include('pages.leads.components.advanced-filter')

@endsection

@push('js')

@endpush
