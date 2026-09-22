<p align="center">
    <a href="https://laravel.com" target="_blank">
        <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
    </a>
</p>

<p align="center">
    <a href="https://github.com/laravel/framework/actions">
        <img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status">
    </a>
    <a href="https://packagist.org/packages/laravel/framework">
        <img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads">
    </a>
    <a href="https://packagist.org/packages/laravel/framework">
        <img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version">
    </a>
    <a href="https://packagist.org/packages/laravel/framework">
        <img src="https://img.shields.io/packagist/l/laravel/framework" alt="License">
    </a>
</p>

# Lead CMS

A Laravel 13+ based Lead Management System with role-based access control, permissions, user management, lead groups, and configurable application settings.

---

## Tech Stack Taken

- Laravel 13+
- PHP 8.3+
- Blade
- Bootstrap
- SQLite / MySQL
- Laravel Eloquent ORM
- Laravel Artisan
- Laravel Herd for local development

---

## Requirements

Before setting up the project, make sure you have:

- PHP 8.3+
- Composer
- Git
- Laravel Herd (recommended for local development)

---

# Local Setup

## 1. Clone the Repository

```bash
git clone <repository-url>
cd lead-cms
```

## 2. Install Project Dependencies

```bash
composer install
```

## 3. Create the Environment File

### Windows

```bash
copy .env.example .env
```

### macOS / Linux

```bash
cp .env.example .env
```

## 4. Generate the Application Key

```bash
php artisan key:generate
```

---

# Database Configuration

The application supports both SQLite and MySQL.

## SQLite

SQLite is recommended for local development.

Configure `.env`:

```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

Make sure the SQLite database file exists:

```text
database/database.sqlite
```

If the file does not exist, create an empty file named:

```text
database.sqlite
```

inside the `database` directory.

## MySQL

For MySQL environments, configure `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lead_cms
DB_USERNAME=root
DB_PASSWORD=
```

Use the credentials for your local or target environment.

Do not commit database credentials to Git.

---

# Super Admin Configuration

The application setup command creates the default Super Administrator.

Configure the credentials in `.env`:

```env
ADMIN_NAME="Super Admin"
ADMIN_EMAIL="admin@example.com"
ADMIN_PASSWORD="password"
```

Use your own local credentials.

For production or shared environments, always use a strong password.

---

# Application Setup

After configuring `.env`, run:

```bash
php artisan app:setup
```

This is the recommended setup command for a new local installation.

The command automatically:

1. Runs pending database migrations.
2. Creates the default application roles.
3. Creates the default Super Administrator.
4. Synchronizes application permissions.
5. Creates the required role-permission assignments.

The setup flow is:

```text
php artisan app:setup
        |
        +-- Database Migrations
        |
        +-- Role Seeder
        |
        +-- Super Admin Seeder
        |
        +-- Permission Synchronization
        |
        +-- Application Ready
```

After successful setup, the command displays the Super Admin login details.

Example:

```text
Application setup completed successfully.

Super Admin Login
Email:    admin@example.com
Password: password

You can now log in to the application.
```

A staging or production database is not required for a fresh local installation.

A database SQL dump is also not required.

---

# Laravel Herd

Laravel Herd is recommended for local development.

After running:

```bash
php artisan app:setup
```

open the configured Herd URL in your browser.

For example:

```text
https://lead-cms.test
```

The exact URL depends on your Herd configuration.

When using Laravel Herd, you normally do not need to run:

```bash
php artisan serve
```

---

# Fresh Database

If you need to completely rebuild your local database:

```bash
php artisan migrate:fresh
php artisan app:setup
```

This will:

1. Delete all existing database tables.
2. Recreate the database schema.
3. Create the default roles.
4. Create the Super Administrator.
5. Synchronize permissions and role assignments.

> **Warning:** `migrate:fresh` deletes all existing data from the selected database. Use it only when you intentionally want to rebuild the database.

For a normal new installation, use only:

```bash
php artisan app:setup
```

---

# Application Setup Command

The main project bootstrap command is:

```bash
php artisan app:setup
```

The command is located at:

```text
app/Console/Commands/AppSetupCommand.php
```

The command performs the following operations:

```text
app:setup
    |
    +-- migrate
    |
    +-- RoleSeeder
    |
    +-- SuperAdminSeeder
    |
    +-- permissions:sync
```

The command intentionally uses:

```bash
php artisan migrate
```

instead of:

```bash
php artisan migrate:fresh
```

This means running:

```bash
php artisan app:setup
```

does not delete an existing database.

It only applies pending migrations and initializes the required application data.

---

# Default Roles

The application includes the following default roles:

| Role | Description |
|---|---|
| Super Administrator | Full application access |
| Administrator | Administrative access |
| Sales Manager | Sales management access |
| Sales Agent | Sales and lead access |
| Lead Manager | Lead management access |
| Viewer | Read-only access |

Roles are created through:

```text
database/seeders/RoleSeeder.php
```

---

# Super Administrator

The default Super Administrator is created through:

```text
database/seeders/SuperAdminSeeder.php
```

The credentials come from:

```env
ADMIN_NAME="Super Admin"
ADMIN_EMAIL="admin@example.com"
ADMIN_PASSWORD="password"
```

The user is automatically assigned the:

```text
Super Administrator
```

role.

The password is securely hashed before being stored in the database.

---

# Permissions

Application permissions are managed through:

```bash
php artisan permissions:sync
```

The permission synchronization process:

- Creates missing permissions.
- Updates existing permissions when required.
- Creates missing role-permission assignments.

The `app:setup` command automatically runs permission synchronization.

You normally do not need to run it manually during a new installation.

To synchronize permissions manually:

```bash
php artisan permissions:sync
```

---

# Settings

Application settings are stored in the database.

Settings are intentionally not included as fixed seed data because they may vary between environments.

After logging in as Super Administrator, application settings can be configured through the application.

Different environments can therefore maintain their own settings:

```text
Local
  |
  +-- Local Settings

Staging
  |
  +-- Staging Settings

Production
  |
  +-- Production Settings
```

---

# Database Architecture

The project separates database schema, required application data, permissions, and business data.

```text
Migrations
    |
    +-- Database Schema

RoleSeeder
    |
    +-- Application Roles

SuperAdminSeeder
    |
    +-- Super Administrator

permissions:sync
    |
    +-- Permissions
    +-- Role Assignments

Application
    |
    +-- Business Data
```

Production and staging business data are not required to initialize a new development environment.

A production or staging SQL dump is not required for a fresh local installation.

---

# Database Commands

## Check Migration Status

```bash
php artisan migrate:status
```

## Run Pending Migrations

```bash
php artisan migrate
```

## Create a Migration

```bash
php artisan make:migration create_example_table
```

## Rebuild the Database

```bash
php artisan migrate:fresh
```

After rebuilding the database, run:

```bash
php artisan app:setup
```

> `migrate:fresh` deletes all existing tables and data.

---

# Seeder Commands

## Run Role Seeder

```bash
php artisan db:seed --class=RoleSeeder
```

## Run Super Admin Seeder

```bash
php artisan db:seed --class=SuperAdminSeeder
```

Normally, developers should use:

```bash
php artisan app:setup
```

instead of manually running the individual seeders.

---

# Permission Commands

## Synchronize Permissions

```bash
php artisan permissions:sync
```

The recommended application setup already performs this automatically:

```bash
php artisan app:setup
```

---

# Useful Artisan Commands

## Application Setup

```bash
php artisan app:setup
```

## Run Pending Migrations

```bash
php artisan migrate
```

## Rebuild Local Database

```bash
php artisan migrate:fresh
php artisan app:setup
```

## Synchronize Permissions

```bash
php artisan permissions:sync
```

## Check Migration Status

```bash
php artisan migrate:status
```

## Clear Laravel Cache

```bash
php artisan optimize:clear
```

## View Application Information

```bash
php artisan about
```

## View All Artisan Commands

```bash
php artisan list
```

## Start Laravel Development Server

If Laravel Herd is not being used:

```bash
php artisan serve
```

When using Laravel Herd, this is normally not required.

---

# Development Workflow

## Normal Development

When new migrations are added:

```bash
php artisan migrate
```

## Permission Changes

After changing permission definitions:

```bash
php artisan permissions:sync
```

## Application Setup

When application setup data needs to be initialized:

```bash
php artisan app:setup
```

## Complete Local Rebuild

When a completely clean local database is required:

```bash
php artisan migrate:fresh
php artisan app:setup
```

---

# Fresh Project Workflow

A developer cloning the repository for the first time should follow these steps.

## Clone the Repository

```bash
git clone <repository-url>
cd lead-cms
```

## Install Dependencies

```bash
composer install
```

## Create Environment File

### Windows

```bash
copy .env.example .env
```

### macOS / Linux

```bash
cp .env.example .env
```

## Generate Application Key

```bash
php artisan key:generate
```

## Configure Environment

Configure the database and Super Admin credentials in `.env`.

## Run Application Setup

```bash
php artisan app:setup
```

## Open the Application

If using Laravel Herd, open the configured Herd URL.

Example:

```text
https://lead-cms.test
```

The application is now ready to use.

---

# Environment Configuration

Example local `.env` configuration:

```env
APP_NAME="Lead CMS"

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

ADMIN_NAME="Super Admin"
ADMIN_EMAIL="admin@example.com"
ADMIN_PASSWORD="password"
```

For MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lead_cms
DB_USERNAME=root
DB_PASSWORD=
```

Environment configuration should always be specific to the environment.

---

# Security

Never commit the `.env` file to Git.

Do not commit:

- Database passwords
- Super Admin passwords
- API keys
- Access tokens
- Application secrets
- Production credentials

Use `.env.example` for safe configuration examples.

Example:

```env
ADMIN_NAME="Super Admin"
ADMIN_EMAIL=admin@example.com
ADMIN_PASSWORD=
```

Production credentials must be configured through the production environment.

---

# Git Workflow

Check the current Git status:

```bash
git status
```

Add changes:

```bash
git add .
```

Commit changes:

```bash
git commit -m "Describe your changes"
```

Push changes:

```bash
git push
```

Before committing, make sure sensitive files such as `.env` and local database files are excluded by `.gitignore`.

---

# Troubleshooting

## Migration Already Ran

Laravel does not rerun a migration that has already been executed.

Check the migration status:

```bash
php artisan migrate:status
```

If the local database is disposable and you want to rebuild it:

```bash
php artisan migrate:fresh
php artisan app:setup
```

> Do not use `migrate:fresh` on a database containing data that must be preserved.

---

## Permission Issues

If permissions or role assignments are missing:

```bash
php artisan permissions:sync
```

---

## `.env` Changes Not Taking Effect

Clear Laravel's cached configuration:

```bash
php artisan optimize:clear
```

---

## SQLite Database Does Not Exist

Make sure this file exists:

```text
database/database.sqlite
```

Then run:

```bash
php artisan app:setup
```

---

## Database Schema Is Out of Sync

For a disposable local database:

```bash
php artisan migrate:fresh
php artisan app:setup
```

---

# Project Structure

Important project directories:

```text
app/
├── Console/
│   └── Commands/
│       └── AppSetupCommand.php
│
├── Models/
│
└── ...

database/
├── migrations/
│
└── seeders/
    ├── RoleSeeder.php
    └── SuperAdminSeeder.php

resources/
├── views/
│
└── ...

routes/
├── web.php
└── ...
```

---

# Laravel Resources

- Laravel Documentation: https://laravel.com/docs
- Laravel Learn: https://laravel.com/learn
- Laracasts: https://laracasts.com

---

# Agentic Development

This project can be used with AI coding assistants such as GitHub Copilot, Cursor, and Claude Code.

Laravel Boost can be installed for AI-assisted Laravel development:

```bash
composer require laravel/boost --dev
php artisan boost:install
```

---

# Contributing

Before submitting changes:

1. Create a feature branch.
2. Make your changes.
3. Verify migrations and application setup.
4. Run the relevant tests.
5. Review your Git changes.
6. Submit a pull request.

---

# Code of Conduct

Please maintain a respectful and professional environment when contributing to this project.

---

# Security Vulnerabilities

If you discover a security vulnerability in this application, please report it privately to the project maintainers.

Do not publicly disclose security vulnerabilities before they have been reviewed and addressed.

---

# License

Proprietary software. All rights reserved.

