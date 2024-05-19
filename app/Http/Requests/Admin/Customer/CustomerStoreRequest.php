<?php

namespace App\Http\Requests\Admin\Customer;

use App\Enums\CustomerStatus;
use App\Http\Requests\BaseRequest;
use App\Models\Customer;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;

class CustomerStoreRequest extends BaseRequest
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
				'required', 'string', Rule::unique(Customer::class), 'min:3', 'max:255',
			],
			'status' => [
				'integer', new Enum(CustomerStatus::class),
			],
			'name' => [
				'required', 'string', 'min:3', 'max:255',
			],
			'email' => [
				'email', 'max:255', Rule::unique(Customer::class),
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
				Password::defaults(), 'max:255',
			],
			'notification' =>	[
				'sometimes', 'bool',
			],
		];
	}
}
