<?php

namespace App\Http\Controllers\Admin\User;

use App\Exceptions\RedirectResponseException;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\Admin\User\UserRoleStoreRequest;
use App\Http\Requests\Admin\User\UserRoleUpdateRequest;
use App\Http\Resources\Admin\User\UserRoleIndexResource;
use App\Messages\UserRoleMessage;
use App\RoutePaths\Admin\User\UserRoleRoutePath;
use App\Services\UserRoleService;
use App\Models\UserRole;
use App\Services\PermissionService;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserRoleController extends AdminBaseController
{
    public function __construct(
        private UserRoleRoutePath $userRoleRoutePath,
        private UserRoleService $userRoleService,
        private PermissionService $permissionService,
        private UserRoleMessage $userRoleMessage,
    ) {
        parent::__construct(service: $userRoleService);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Renderable|UserRoleIndexResource
    {
        if ($request->ajax()) {
            $attributes = (object) $request->only(
                ['draw', 'columns', 'order', 'start', 'length', 'search']
            );

            $request->merge([
                'recordsAll' => $this->userRoleService->getAllUserRoles(),
                'recordsFiltered' => $this->userRoleService->getAllWithFilter(
                    filterColumns: ['id', 'name', 'status'],
                    filterQuery: $attributes,
                ),
            ]);

            return UserRoleIndexResource::make($attributes);
        }

        $columns = $this->tableColumns(['name']);

        $this->registerBreadcrumb();

        $this->sharePageData([
            'title' => $this->getActionTitle(),
            'createTitle' => $this->getActionTitle('create'),
        ]);

        return view($this->userRoleRoutePath::INDEX, [
            'create' => route($this->userRoleRoutePath::CREATE),
            'columnNames' => $columns,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Renderable
    {
        $this->registerBreadcrumb(
            parentRouteName: $this->userRoleRoutePath::INDEX,
        );

        $this->sharePageData([
            'title' => $this->getActionTitle(),
        ]);

        return view($this->userRoleRoutePath::CREATE, [
            'permissions' => $this->userRoleService->getAllPermissions(),
            'permissionGroups' => $this->permissionService->getPermissionGroups(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRoleStoreRequest $request): RedirectResponse|RedirectResponseException
    {
        $created = $this->userRoleService->createUserRole($request->getAttributes());

        throw_if(!$created, RedirectResponseException::class, $this->userRoleMessage->createFailed());

        return redirect()->route($this->userRoleRoutePath::EDIT, $created)->with([
            'message' => $this->userRoleMessage->createSuccess(),
            'status' => true,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserRole $userRole): Renderable
    {

        $this->registerBreadcrumb(
            parentRouteName: $this->userRoleRoutePath::INDEX,
            routeParameter: $userRole->id,
        );

        $this->sharePageData([
            'title' => $this->getActionTitle(),
        ]);

        return view($this->userRoleRoutePath::EDIT, [
            'userRole' => $userRole,
            'permissions' => $this->userRoleService->getAllPermissions(),
            'permissionGroups' => $this->permissionService->getPermissionGroups(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRole $userRole, UserRoleUpdateRequest $request): RedirectResponse|RedirectResponseException
    {
        $updated = $this->userRoleService->updateUserRole($userRole->id, $request->getAttributes());

        throw_if(!$updated, RedirectResponseException::class, $this->userRoleMessage->updateFailed());

        return redirect()->route($this->userRoleRoutePath::EDIT, $userRole)->with([
            'message' => $this->userRoleMessage->updateSuccess(),
            'status' => true,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserRole $userRole): JsonResponse|RedirectResponseException
    {
        $deleted = $this->userRoleService->deleteUserRole($userRole->id);

        throw_if(!$deleted, RedirectResponseException::class, $this->userRoleMessage->deleteFailed());

        return $this->jsonResponse()->message($this->userRoleMessage->deleteSuccess())
            ->success();
    }
}
