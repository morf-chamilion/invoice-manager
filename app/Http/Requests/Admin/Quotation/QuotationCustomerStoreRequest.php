<?php

namespace App\Http\Requests\Admin\Quotation;

use App\Http\Requests\BaseRequest;
use App\Models\Customer;
use Illuminate\Validation\Rule;

class QuotationCustomerStoreRequest extends BaseRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
	 */
	public function rules(): array
	{
		return [
			'name' => [
				'required',
				'string',
				Rule::unique(Customer::class),
				'min:3',
				'max:255',
			],
			'email' => [
				'email',
				'max:255',
				Rule::unique(Customer::class),
			],
			'phone' => [
				'required',
				'string',
				'min:3',
				'max:255',
			],
			'address' => [
				'required',
				'string',
				'min:3',
				'max:255',
			],
		];
	}
}
