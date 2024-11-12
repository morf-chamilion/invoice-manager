<?php

namespace App\Http\Requests\Admin\Invoice;

use App\Enums\InvoiceItemType;
use App\Enums\InvoicePaymentMethod;
use App\Enums\InvoiceStatus;
use App\Http\Requests\BaseRequest;
use App\Models\Customer;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class InvoiceStoreRequest extends BaseRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
	 */
	public function rules(): array
	{
		return [
			'status' => [
				'integer',
				new Enum(InvoiceStatus::class),
			],
			'date' => [
				'nullable',
				'date',
			],
			'due_date' => [
				'nullable',
				'date',
			],
			'customer_id' => [
				'required',
				Rule::exists(Customer::class, 'id'),
			],
			'payment_method' => [
				'required',
				'integer',
				new Enum(InvoicePaymentMethod::class),
			],
			'payment_date' => [
				'nullable',
				'date',
			],
			'payment_reference' => [
				'nullable',
				'string',
				'max:510',
			],
			'payment_reference_receipt' => [
				'nullable',
				'string',
			],
			'discount_type' => [
				'nullable',
				'string',
			],
			'discount_value' => [
				'nullable',
				'numeric',
				'min:0',
				function ($attribute, $value, $fail) {
					if (empty($this->input('invoice_items')) && $value != 0) {
						$fail('The discount value must be 0 if there are no invoice items.');
					}
				},
			],
			'invoice_items' => [
				'nullable',
				'array',
				'min:1',
			],
			'invoice_items.*.type_id' => [
				'required',
				'integer',
				new Enum(InvoiceItemType::class),
			],
			'invoice_items.*.item_id' => [
				'required',
			],
			'invoice_items.*.title' => [
				'nullable',
				'string',
			],
			'invoice_items.*.description' => [
				'nullable',
				'string',
			],
			'invoice_items.*.quantity' => [
				'required',
				'numeric',
				'min:0',
				'max:9999999',
			],
			'invoice_items.*.unit_price' => [
				'required',
				'numeric',
				'min:0',
				'max:9999999.99',
			],
			'invoice_items.*.amount' => [
				'required',
				'numeric',
				'min:0',
				'max:9999999.99',
			],
			'total_price' => [
				'required',
				'numeric',
				'min:0',
				'max:9999999.99',
			],
			'notes' =>	[
				'nullable',
				'string',
				'max:510'
			],
			'notification' => [
				'sometimes',
				'bool',
			],
		];
	}

	/**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array<string, string>
	 */
	public function messages(): array
	{
		return [
			'invoice_items.*.type.required' => 'The type field for invoice item is required.',
			'invoice_items.*.content.required' => 'This content is required for this item.',
			'invoice_items.*.quantity.required' => 'The quantity is required for this item.',
			'invoice_items.*.unit_price.required' => 'The unit price is required for this item.',
			'invoice_items.*.unit_price.max' => 'The unit price field must be at most 9999999.99.',
			'invoice_items.*.amount.required' => 'The amount is required for this item.',
			'invoice_items.*.amount.max' => 'The amount field must be at most 9999999.99.',
		];
	}
}
