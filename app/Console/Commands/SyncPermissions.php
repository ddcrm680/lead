<?php

namespace App\Console\Commands;

use App\Services\PermissionService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('permissions:sync')]
#[Description('Synchronize application permissions and ensure default role assignments')]
class SyncPermissions extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(PermissionService $permissionService): int
    {
        $result = $permissionService->sync();

        $this->components->info('Permissions synchronized successfully.');

        $this->components->twoColumnDetail(
            'Permissions created',
            $result['permissions_created']
        );

        $this->components->twoColumnDetail(
            'Permissions updated',
            $result['permissions_updated']
        );

        $this->components->twoColumnDetail(
            'Role assignments created',
            $result['role_assignments_created']
        );

        return self::SUCCESS;
    }
}