<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:setup')]
#[Description('Set up the application database and default data')]
class AppSetupCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Setting up application...');
        $this->newLine();

        $this->info('Running migrations...');

        if ($this->call('migrate') !== self::SUCCESS) {
            $this->error('Migration failed. Application setup stopped.');

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Seeding roles...');

        if ($this->call('db:seed', [
            '--class' => 'RoleSeeder',
        ]) !== self::SUCCESS) {
            $this->error('Role seeding failed. Application setup stopped.');

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Creating Super Admin...');

        if ($this->call('db:seed', [
            '--class' => 'SuperAdminSeeder',
        ]) !== self::SUCCESS) {
            $this->error('Super Admin seeding failed. Application setup stopped.');

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Synchronizing permissions...');

        if ($this->call('permissions:sync') !== self::SUCCESS) {
            $this->error('Permission synchronization failed. Application setup stopped.');

            return self::FAILURE;
        }

        $this->newLine();

        if ($this->confirm(
            'Install default Lead CRM configuration?',
            true,
        )) {
            $this->info('Seeding Lead defaults...');

            if ($this->call('db:seed', [
                '--class' => 'LeadDefaultsSeeder',
            ]) !== self::SUCCESS) {
                $this->error('Lead defaults seeding failed. Application setup stopped.');

                return self::FAILURE;
            }
        } else {
            $this->line('Skipping default Lead CRM configuration.');
        }

        $this->newLine();
        $this->info('Application setup completed successfully.');

        $this->newLine();
        $this->info('Super Admin Login');
        $this->line('Email:    ' . env('ADMIN_EMAIL', 'admin@example.com'));
        $this->line('Password: ' . env('ADMIN_PASSWORD', 'password'));

        $this->newLine();
        $this->info('You can now log in to the application.');

        return self::SUCCESS;
    }
}