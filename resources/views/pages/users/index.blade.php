@extends('layouts.app')

@section('title', 'Users')

@section('page-eyebrow', 'MANAGE USERS')

@section('page-title', 'Users')

@push('css')

@endpush

@section('content')

<section id="users" aria-labelledby="usersHeading">
    <div class="page-stack">
        <section class="page-intro">
            <div>
                <span class="eyebrow">MANAGE USERS</span>
                <h2 id="usersHeading">Users</h2>
                <p>Manage user accounts, roles, access and account status.</p>
            </div>

            <div class="action-row">
                @if (auth()->user()?->hasPermission('users.view'))
                    <button class="btn btn-outline-dark" type="button" id="exportUsersBtn">
                        <i class="bi bi-download"></i>
                        Export
                    </button>
                @endif

                @if (auth()->user()?->hasPermission('users.create'))
                    <button class="btn btn-danger" type="button" data-bs-toggle="offcanvas" data-bs-target="#userForm">
                        <i class="bi bi-plus-lg"></i>
                        Add User
                    </button>
                @endif
            </div>
        </section>

        <section class="panel filter-panel">
            <div class="filter-row">
                <div class="searchbox">
                    <i class="bi bi-search"></i>
                    <input id="userSearch" type="search" placeholder="Search name, email or phone">
                </div>

                <select class="form-select" id="userRoleFilter">
                    <option value="">All roles</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>

                <select class="form-select" id="userStatusFilter">
                    <option value="">All statuses</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
        </section>

        <div id="userGrid"></div>

    </div>
</section>

{{-- User Create / Edit Form --}}
@include('pages.users.components.user-add')

@endsection
@push('js')
<script src="{{ asset('assets/js/modules/user.js') }}"></script>
@endpush