<?php

namespace App\Http\Requests\Admin\Invoice;

use App\Http\Requests\BaseRequest;

class InvoiceCustomerStoreRequest extends BaseRequest
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
				'min:3',
				'max:255',
			],
			'email' => [
				'email',
				'max:255',
			],
			'phone' => [
				'nullable',
				'string',
				'min:3',
				'max:255',
			],
			'address' => [
				'nullable',
				'string',
				'min:3',
				'max:255',
			],
		];
	}
}
