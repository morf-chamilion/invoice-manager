<?php

namespace App\Http\Requests\Front\Customer;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;
use App\Models\Customer;
use Illuminate\Validation\Rules\Password;

class CustomerUpdateRequest extends BaseRequest
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
				'required', 'string', Rule::unique(Customer::class)->ignore($this->customer), 'min:3', 'max:255',
			],
			'email' => [
				'email', 'max:255', Rule::unique(Customer::class)->ignore($this->customer),
			],
			'phone' => [
				'required', 'string', 'min:3', 'max:255',
			],
			'address' => [
				'required', 'string', 'min:3', 'max:255',
			],
			'password' => [
				'string', 'nullable', Password::defaults(), 'confirmed',
			],
		];
	}
}
