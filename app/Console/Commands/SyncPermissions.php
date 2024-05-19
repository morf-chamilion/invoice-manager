<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PermissionService;
use App\Services\UserRoleService;
use Exception;
use Illuminate\Support\Facades\App;
use Spatie\Permission\PermissionRegistrar;

class SyncPermissions extends Command
{
    /**
     * Create a new command instance.
     */
    public function __construct(
        private PermissionService $permissionService,
        private UserRoleService $userRoleService,
    ) {
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate permissions for routes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $routes = $this->permissionService->getFormattedMappings();

        if (empty($routes)) {
            $this->error('Error: No routes found.');

            return Command::FAILURE;
        }

        if (
            App::isProduction() &&
            !$this->confirm('Permissions table will be truncated, Do you wish to continue?')
        ) {
            $this->warn('Warning: Permission synchronization canceled.');

            return Command::FAILURE;
        }

        $this->truncatePermissions();
        $this->forgetCachedPermissions();
        $this->generatePermissions($routes);
        $this->assignSuperAdminPermissions();

        $this->info('Success: Permissions generated.');

        return Command::SUCCESS;
    }

    /**
     * Truncate table permissions.
     */
    protected function truncatePermissions(): bool | Exception
    {
        $this->info('Task: Truncating permissions table.');

        return $this->permissionService->truncate();
    }

    /**
     * Reset the permissions cache.
     */
    protected function forgetCachedPermissions(): void
    {
        // We populate permission data directly in the database instead of calling
        // the supplied methods, to be consistent we manually reset the cache.
        App::make(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Generate permissions from registered routes.
     */
    protected function generatePermissions(array $resources): void
    {
        foreach ($resources as $resourceName => $resource) {
            $this->generatePermissionsForResource($resourceName, $resource);
        }
    }

    /**
     * Generate permissions for a specific resource.
     */
    protected function generatePermissionsForResource(string $resourceName, array $actions): void
    {
        foreach ($actions as $action => $routeNames) {
            $this->generatePermissionsForAction($resourceName, $action, $routeNames);
        }
    }

    /**
     * Generate permissions for a specific action.
     */
    protected function generatePermissionsForAction(string $resourceName, string $action, array $routeNames): void
    {
        foreach ($routeNames as $routeName) {
            $this->permissionService->createPermission([
                'name' => $routeName,
                'resource' => $resourceName,
                'action' => $action,
            ]);
        }
    }

    /**
     * Assign permissions to super admin role.
     */
    protected function assignSuperAdminPermissions(): int
    {
        $superAdmin = $this->userRoleService->getUserRoleWhere('name', 'Super Admin');

        if ($superAdmin) {
            $this->info('Task: Assigning role super admin permissions.');

            $superAdmin->syncPermissions(
                $this->permissionService->getAllPermissions()->pluck('name')
            );
        } else {
            $this->error('Error: Role Super Admin not found, seed the database and retry.');

            return Command::FAILURE;
        }

        return true;
    }
}
