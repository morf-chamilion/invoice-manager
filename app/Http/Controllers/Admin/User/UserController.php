<?php

namespace App\Http\Controllers\Admin\User;

use App\Exceptions\RedirectResponseException;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\Admin\User\UserStoreRequest;
use App\Http\Requests\Admin\User\UserUpdateRequest;
use App\Http\Resources\Admin\User\UserIndexResource;
use App\Messages\UserMessage;
use App\Models\User;
use App\RoutePaths\Admin\User\UserRoutePath;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class UserController extends AdminBaseController
{
    public function __construct(
        private UserRoutePath $userRoutePath,
        private UserService $userService,
        private UserMessage $userMessage,
    ) {
        parent::__construct(service: $userService);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): UserIndexResource|Renderable
    {
        if ($request->ajax()) {
            $attributes = (object) $request->only(
                ['draw', 'columns', 'order', 'start', 'length', 'search']
            );

            $request->merge([
                'recordsAll' => $this->userService->getAllUsers(),
                'recordsFiltered' => $this->userService->getAllWithFilter(
                    filterColumns: ['id', 'name', 'email', 'status'],
                    filterQuery: $attributes,
                ),
            ]);

            return UserIndexResource::make($attributes);
        }

        $columns = $this->tableColumns(
            ['name', 'email', 'role']
        );

        $this->registerBreadcrumb();

        $this->sharePageData([
            'title' => $this->getActionTitle(),
            'createTitle' => $this->getActionTitle('create'),
        ]);

        return view($this->userRoutePath::INDEX, [
            'create' => route($this->userRoutePath::CREATE),
            'columnNames' => $columns,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Renderable
    {
        $this->registerBreadcrumb(
            parentRouteName: $this->userRoutePath::INDEX,
        );

        $this->sharePageData([
            'title' => $this->getActionTitle(),
        ]);

        return view($this->userRoutePath::CREATE, [
            'roles' => $this->userService->getAllRoles(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request): RedirectResponse|RedirectResponseException
    {
        $created = $this->userService->createUser($request->getAttributes());

        throw_if(!$created, RedirectResponseException::class, $this->userMessage->createFailed());

        $this->registerBreadcrumb();

        return redirect()->route($this->userRoutePath::EDIT, $created)->with([
            'message' => $this->userMessage->createSuccess(),
            'status' => true,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): Renderable
    {
        $this->registerBreadcrumb(
            parentRouteName: $this->userRoutePath::INDEX,
            routeParameter: $user->id,
        );

        $this->sharePageData([
            'title' => $this->getActionTitle(),
        ]);

        return view($this->userRoutePath::EDIT, [
            'user' => $user,
            'roles' => $this->userService->getAllRoles(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(User $user, UserUpdateRequest $request): RedirectResponse|RedirectResponseException
    {
        $updated = $this->userService->updateUser($user->id, $request->getAttributes());

        throw_if(!$updated, RedirectResponseException::class, $this->userMessage->updateFailed());

        return redirect()->route($this->userRoutePath::EDIT, $user)->with([
            'message' => $this->userMessage->updateSuccess(),
            'status' => true,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): JsonResponse|RedirectResponseException
    {
        $deleted = $this->userService->deleteUser($user->id);

        throw_if(!$deleted, RedirectResponseException::class, $this->userMessage->deleteFailed());

        return $this->jsonResponse()->message($this->userMessage->deleteSuccess())
            ->success();
    }
}
