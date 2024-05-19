<?php

namespace App\Messages;

use App\Messages\BaseMessage;
use App\Services\UserService;

class UserMessage extends BaseMessage
{
	public function __construct(
		protected UserService $userService,
	) {
	}

	protected function modelName(): string
	{
		return $this->userService->modelName();
	}
}
