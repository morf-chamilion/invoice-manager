<?php

namespace App\Services\PaymentGateways;

use App\Handlers\MoneyHandler;
use App\Models\Invoice;
use App\RoutePaths\Front\Checkout\CheckoutRoutePath;
use Illuminate\Support\Carbon;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class CyberSourcePaymentGateway extends MoneyHandler
{
	public const GATEWAY_SUCCESS_CODE = 100;

	/**
	 * Get the data for the checkout session request.
	 */
	public function getCheckoutData(Invoice $invoice): array
	{;
		$params = [
			'access_key' => env('CHECKOUT_ACCESS_KEY'),
			'profile_id' => env('CHECKOUT_MERCHANT_ID'),
			'currency' => $this->currencyCode(),
			'transaction_uuid' => Str::uuid(),
			'signed_field_names' => 'access_key,profile_id,transaction_uuid,signed_field_names,unsigned_field_names,signed_date_time,locale,transaction_type,reference_number,amount,currency,override_custom_cancel_page,override_custom_receipt_page',
			'unsigned_field_names' => 'signature,bill_to_forename,bill_to_surname,bill_to_email,bill_to_address_line1,bill_to_address_city,bill_to_address_country',
			'signed_date_time' => Carbon::now()->utc()->format("Y-m-d\TH:i:s\Z"),
			'locale' => 'en-US',
			'amount' => number_format($invoice->total_price, 2, '.', ''),
			'reference_number' => $invoice->id,
			'transaction_type' => 'sale',
			"override_custom_cancel_page" => $this->checkoutUrl($invoice),
			"override_custom_receipt_page" => $this->callbackUrl($invoice),
			"bill_to_forename" => $invoice->customer->name,
			"bill_to_surname" => $invoice->customer->name,
			"bill_to_email" =>  $invoice->customer->email,
			'bill_to_address_line1' => $invoice->customer->address,
			'bill_to_address_city' => env('CHECKOUT_BILL_TO_ADDRESS_CITY', 'colombo'),
			'bill_to_address_country' => 'LK',
		];

		$fields = [];
		foreach ($params as $name => $value) {
			$fields[] = '<input type="hidden" name="' . $name . '" value="' . $value . '" />';
		}

		$fields[] = '<input type="hidden" name="signature" value="' . $this->sign($params) . '" />';

		return $fields;
	}

	/**
	 * Get the URL for the checkout view.
	 */
	public function checkoutUrl(Invoice $invoice): string
	{
		$sessionId = Crypt::encryptString($invoice->id);

		return route(CheckoutRoutePath::SHOW, $sessionId);
	}

	/**
	 * Callback to handle events that may occur during the checkout.
	 */
	public function callbackUrl(Invoice $invoice): string
	{
		$sessionId = Crypt::encryptString($invoice->id);

		return route(CheckoutRoutePath::STORE, $sessionId);
	}

	/**
	 * Payment gateway hosted checkout URL.
	 */
	public function checkoutGatewayUrl(): string
	{
		return Env::get('CHECKOUT_GATEWAY_URL');
	}

	/**
	 * Transaction data signature.
	 */
	protected function sign(array $params): string
	{
		return $this->signData($this->buildDataToSign($params), Env::get('CHECKOUT_SECRET_KEY'));
	}

	/**
	 * Generate signature.
	 */
	private function signData(string $data, string $secretKey): string
	{
		return base64_encode(hash_hmac('sha256', $data, $secretKey, true));
	}

	/**
	 * Build the payload required for the signature.
	 */
	private function buildDataToSign(array $params): string
	{
		$signedFieldNames = explode(",", $params["signed_field_names"]);

		foreach ($signedFieldNames as $field) {
			$dataToSign[] = $field . "=" . $params[$field];
		}

		return implode(",", $dataToSign);
	}
}
