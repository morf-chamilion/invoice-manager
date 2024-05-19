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
				'integer', new Enum(InvoiceStatus::class),
			],
			'date' => [
				'nullable', 'date',
			],
			'due_date' => [
				'nullable', 'date',
			],
			'customer_id' => [
				'required', Rule::exists(Customer::class, 'id'),
			],
			'payment_method' => [
				'required', 'integer', new Enum(InvoicePaymentMethod::class),
			],
			'payment_date' => [
				'nullable', 'date',
			],
			'payment_reference' => [
				'nullable', 'string', 'max:510',
			],
			'invoice_items' => [
				'nullable', 'array', 'min:1',
			],
			'invoice_items.*.type' => [
				'required', 'integer', new Enum(InvoiceItemType::class),
			],
			'invoice_items.*.content' => [
				'required', 'string',
			],
			'invoice_items.*.quantity' => [
				'required_if:invoice_items.*.type,' . InvoiceItemType::DESCRIPTION->value, 'numeric', 'min:0', 'max:9999999',
			],
			'invoice_items.*.unit_price' => [
				'required_if:invoice_items.*.type,' . InvoiceItemType::DESCRIPTION->value, 'numeric', 'min:0', 'max:9999999.99',
			],
			'invoice_items.*.amount' => [
				'required_if:invoice_items.*.type,' . InvoiceItemType::DESCRIPTION->value, 'numeric', 'min:0', 'max:9999999.99',
			],
			'total_price' => [
				'required', 'numeric', 'min:0', 'max:9999999.99',
			],
			'notes' =>	[
				'nullable', 'string', 'max:510'
			],
			'notification' =>	[
				'sometimes', 'bool',
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
			'invoice_items.*.quantity.required_if' => 'The quantity is required for this item.',
			'invoice_items.*.unit_price.required_if' => 'The unit price is required for this item.',
			'invoice_items.*.unit_price.max' => 'The unit price field must be at most 9999999.99.',
			'invoice_items.*.amount.required_if' => 'The amount is required for this item.',
			'invoice_items.*.amount.max' => 'The amount field must be at most 9999999.99.',
		];
	}
}
