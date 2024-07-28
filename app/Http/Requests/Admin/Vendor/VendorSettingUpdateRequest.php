<?php

namespace App\Http\Requests\Admin\Vendor;

use App\Http\Requests\BaseRequest;

class VendorSettingUpdateRequest extends BaseRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
	 */
	public function rules(): array
	{
		return [
			'logo' => [
				'nullable', 'string',
			],
			'company_content' => [
				'nullable', 'string', 'max:5000',
			],
			'footer_content' => [
				'nullable', 'string', 'max:5000',
			],
		];
	}
}
