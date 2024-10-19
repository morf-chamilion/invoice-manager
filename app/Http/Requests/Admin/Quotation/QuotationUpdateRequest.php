<?php

namespace App\Http\Requests\Admin\Quotation;

use App\Enums\QuotationItemType;
use App\Enums\QuotationStatus;
use App\Http\Requests\BaseRequest;
use App\Models\Customer;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class QuotationUpdateRequest extends BaseRequest
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
				new Enum(QuotationStatus::class),
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
			'quotation_items' => [
				'nullable',
				'array',
				'min:1',
			],
			'quotation_items.*.type_id' => [
				'required',
				'integer',
				new Enum(QuotationItemType::class),
			],
			'quotation_items.*.item_id' => [
				'required',
			],
			'quotation_items.*.title' => [
				'nullable',
				'string',
			],
			'quotation_items.*.description' => [
				'nullable',
				'string',
			],
			'quotation_items.*.quantity' => [
				'required',
				'numeric',
				'min:0',
				'max:9999999',
			],
			'quotation_items.*.unit_price' => [
				'required',
				'numeric',
				'min:0',
				'max:9999999.99',
			],
			'quotation_items.*.amount' => [
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
			'quotation_items.*.type.required' => 'The type field for quotation item is required.',
			'quotation_items.*.content.required' => 'This content is required for this item.',
			'quotation_items.*.quantity.required' => 'The quantity is required for this item.',
			'quotation_items.*.unit_price.required' => 'The unit price is required for this item.',
			'quotation_items.*.unit_price.max' => 'The unit price field must be at most 9999999.99.',
			'quotation_items.*.amount.required' => 'The amount is required for this item.',
			'quotation_items.*.amount.max' => 'The amount field must be at most 9999999.99.',
		];
	}
}
