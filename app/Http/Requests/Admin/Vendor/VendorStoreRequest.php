<?php

namespace App\Http\Requests\Admin\Vendor;

use App\Enums\VendorStatus;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;

class VendorStoreRequest extends BaseRequest
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
				'string', 'min:3', 'max:255',
			],
			'status' => [
				'integer', new Enum(VendorStatus::class),
			],
			'password' => [
				Password::defaults(), 'max:255',
			],
		];
	}
}
