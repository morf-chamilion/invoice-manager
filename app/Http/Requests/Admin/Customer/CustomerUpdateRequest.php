<?php

namespace App\Http\Requests\Admin\Customer;

use App\Enums\CustomerStatus;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;
use App\Models\Customer;
use Illuminate\Validation\Rules\Enum;
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
			'status' => [
				'integer', new Enum(CustomerStatus::class),
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
			'company' => [
				'nullable', 'string', 'min:3', 'max:255',
			],
			'password' => [
				'nullable', Password::defaults(), 'confirmed',
			],
		];
	}
}
