<?php

namespace App\Messages;

use App\Messages\BaseMessage;
use App\Services\SettingService;

class SettingMessage extends BaseMessage
{
	public function __construct(
		protected SettingService $settingService,
	) {
	}

	protected function modelName(): string
	{
		return $this->settingService->modelName();
	}
}
