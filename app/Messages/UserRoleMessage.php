<?php

namespace App\Messages;

use App\Messages\BaseMessage;
use App\Services\UserRoleService;

class UserRoleMessage extends BaseMessage
{
	public function __construct(
		protected UserRoleService $userRoleService,
	) {
	}

	protected function modelName(): string
	{
		return $this->userRoleService->modelName();
	}
}
