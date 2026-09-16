<?php

return [

    'permissions' => [

        'users' => [
            'view',
            'create',
            'update',
            'delete',
            'toggle-status',
            'export'
        ],

        'roles' => [
            'view',
            'create',
            'update',
            'delete',
        ],

        'permissions' => [
            'view',
            'assign',
        ],

        'lead-groups' => [
            'view',
            'create',
            'update',
            'delete',
            'manage-members',
        ],

        'leads' => [
            'view',
            'create',
            'update',
            'delete',
            'toggle-status',
            'export',
        ],

        'settings' => [
            'view',
            'update',
        ],

    ],

    'role_defaults' => [

        'super-admin' => '*',

        'admin' => [
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'users.toggle-status',
            'users.export',

            'roles.view',
            'roles.create',
            'roles.update',
            'roles.delete',

            'permissions.view',
            'permissions.assign',

            'lead-groups.view',
            'lead-groups.create',
            'lead-groups.update',
            'lead-groups.delete',
            'lead-groups.manage-members',

            'leads.view',
            'leads.create',
            'leads.update',
            'leads.delete',
            'leads.toggle-status',
            'leads.export',

            'settings.view',
            'settings.update',
        ],

        'sales-manager' => [

            'leads.view',
            'leads.create',
            'leads.update',
            'leads.toggle-status',
            'leads.export',

            'lead-groups.view',
            'lead-groups.manage-members',
        ],

        'sales-agent' => [

            'leads.view',
            'leads.create',
            'leads.update',
            'leads.toggle-status',

            'lead-groups.view',
        ],

        'lead-manager' => [
            'users.view',

            'leads.view',
            'leads.create',
            'leads.update',
            'leads.toggle-status',
            'leads.export',

            'lead-groups.view',
            'lead-groups.create',
            'lead-groups.update',
            'lead-groups.delete',
            'lead-groups.manage-members',
        ],

        'viewer' => [
            'users.view',

            'leads.view',
            
            'roles.view',
            'permissions.view',
            'lead-groups.view',
        ],

    ],

];