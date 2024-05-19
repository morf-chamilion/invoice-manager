<?php

namespace App\Messages;

class CheckoutMessage
{
	/**
	 * Invalid session identifier.
	 */
	public function sessionInvalid(): string
	{
		return 'Session is invalid or expired.';
	}

	/**
	 * General checkout session create.
	 */
	public function checkoutSessionCreate(): string
	{
		return 'Checkout session initiated successfully.';
	}

	/**
	 * General checkout failure.
	 */
	public function checkoutFailure(): string
	{
		return 'Checkout failed with error.';
	}

	/**
	 * General checkout success.
	 */
	public function checkoutSuccess(): string
	{
		return 'Checkout completed successfully.';
	}

	/**
	 * General payment gateway error.
	 */
	public function paymentGatewayFailure(): string
	{
		return 'Payment gateway response error.';
	}

	/**
	 * Checkout update failed error.
	 */
	public function checkoutUpdateFailure(): string
	{
		return 'Checkout update failure.';
	}

	/**
	 * Transaction failure error.
	 */
	public function paymentGatewayTransactionFailure(): string
	{
		return 'Payment transaction failed.';
	}

	/**
	 * Payment gateway invalid callback data.
	 */
	public function paymentGatewayCallbackFailure(): string
	{
		return 'Error retrieving checkout session callback data.';
	}
}
