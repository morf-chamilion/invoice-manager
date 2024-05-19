<?php

namespace App\Messages;

use App\Messages\BaseMessage;
use App\Services\PageService;

class PageMessage extends BaseMessage
{
	public function __construct(
		protected PageService $pageService,
	) {
	}

	protected function modelName(): string
	{
		return $this->pageService->modelName();
	}
}
