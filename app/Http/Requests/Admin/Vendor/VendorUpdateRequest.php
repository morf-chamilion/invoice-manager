<?php

namespace App\Http\Requests\Admin\Vendor;

use App\Enums\VendorStatus;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rules\Enum;

class VendorUpdateRequest extends BaseRequest
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
			'currency' => [
				'string', 'min:0', 'max:255',
			],
			'invoice_number_prefix' => [
				'required', 'string', 'min:0', 'max:24',
			],
		];
	}
}
