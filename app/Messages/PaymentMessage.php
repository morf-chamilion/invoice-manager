<?php

namespace App\Messages;

use App\Messages\BaseMessage;
use App\Services\PaymentService;

class PaymentMessage extends BaseMessage
{
	public function __construct(
		protected PaymentService $paymentService,
	) {}

	protected function modelName(): string
	{
		return $this->paymentService->modelName();
	}
}
