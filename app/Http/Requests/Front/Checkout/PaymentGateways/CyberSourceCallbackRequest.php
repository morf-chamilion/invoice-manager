<?php

namespace App\Http\Requests\Front\Checkout\PaymentGateways;

use App\Http\Requests\BaseRequest;
use App\RoutePaths\Front\Checkout\CheckoutRoutePath;

class CyberSourceCallbackRequest extends BaseRequest
{
	/**
	 * The route to redirect to if validation fails.
	 *
	 * @var string
	 */
	protected $redirectRoute = CheckoutRoutePath::FAILURE_SHOW;

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
	 */
	public function rules(): array
	{
		return [
			'utf8' => [
				'nullable', 'string',
			],
			'req_card_number' => [
				'nullable', 'string',
			],
			'req_locale' => [
				'nullable', 'string',
			],
			'signature' => [
				'nullable', 'string',
			],
			'req_card_type_selection_indicator' => [
				'nullable', 'string',
			],
			'auth_trans_ref_no' => [
				'nullable', 'string',
			],
			'req_bill_to_surname' => [
				'nullable', 'string',
			],
			'req_bill_to_address_city' => [
				'nullable', 'string',
			],
			'req_card_expiry_date' => [
				'nullable', 'string',
			],
			'card_type_name' => [
				'nullable', 'string',
			],
			'reason_code' => [
				'required', 'string',
			],
			'auth_amount' => [
				'required', 'numeric',
			],
			'auth_response' => [
				'nullable', 'string',
			],
			'bill_trans_ref_no' => [
				'nullable', 'string',
			],
			'req_bill_to_forename' => [
				'nullable', 'string',
			],
			'req_payment_method' => [
				'nullable', 'string',
			],
			'request_token' => [
				'nullable', 'string',
			],
			'auth_time' => [
				'nullable', 'string',
			],
			'req_amount' => [
				'nullable', 'numeric',
			],
			'req_bill_to_email' => [
				'nullable', 'string', 'email',
			],
			'auth_avs_code_raw' => [
				'nullable', 'string',
			],
			'transaction_id' => [
				'required', 'string',
			],
			'req_currency' => [
				'nullable', 'string',
			],
			'req_card_type' => [
				'nullable', 'string',
			],
			'decision' => [
				'nullable', 'string',
			],
			'req_override_custom_receipt_page' => [
				'nullable', 'url',
			],
			'message' => [
				'nullable', 'string',
			],
			'signed_field_names' => [
				'nullable', 'string',
			],
			'req_transaction_uuid' => [
				'nullable', 'string',
			],
			'auth_avs_code' => [
				'nullable', 'string',
			],
			'auth_code' => [
				'nullable', 'string',
			],
			'req_bill_to_address_country' => [
				'nullable', 'string',
			],
			'req_transaction_type' => [
				'nullable', 'string',
			],
			'req_access_key' => [
				'nullable', 'string',
			],
			'req_profile_id' => [
				'nullable', 'string',
			],
			'req_reference_number' => [
				'nullable', 'string',
			],
			'req_override_custom_cancel_page' => [
				'nullable', 'url',
			],
			'signed_date_time' => [
				'nullable', 'date_format:Y-m-d\TH:i:s\Z',
			],
			'req_bill_to_address_line1' => [
				'nullable', 'string',
			],
		];
	}
}
