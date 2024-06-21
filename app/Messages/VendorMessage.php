<?php

namespace App\Messages;

use App\Messages\BaseMessage;
use App\Services\VendorService;

class VendorMessage extends BaseMessage
{
	public function __construct(
		protected VendorService $vendorService,
	) {
	}

	protected function modelName(): string
	{
		return $this->vendorService->modelName();
	}
}
