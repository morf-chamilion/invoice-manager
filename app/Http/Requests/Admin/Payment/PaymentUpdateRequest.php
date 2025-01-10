<?php

namespace App\Http\Requests\Admin\Payment;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Http\Requests\BaseRequest;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class PaymentUpdateRequest extends BaseRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
	 */
	public function rules(): array
	{
		return [
			'date' => [
				'nullable',
				'date',
			],
			'customer_id' => [
				'required',
				Rule::exists(Customer::class, 'id'),
			],
			'invoice_id' => [
				'required',
				Rule::exists(Invoice::class, 'id'),
			],
			'method' => [
				'required',
				'integer',
				new Enum(PaymentMethod::class),
			],
			'amount' => [
				'required',
				'numeric',
				'min:0',
				'max:9999999.99',
			],
			'notes' =>	[
				'nullable',
				'string',
				'max:255'
			],
			'reference_receipt' => [
				'nullable',
				'string',
			],
			'status' => [
				'integer',
				new Enum(PaymentStatus::class),
			],
		];
	}
}
