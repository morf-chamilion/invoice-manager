<?php

namespace App\Http\Requests\Admin\User;

use App\Enums\UserRoleStatus;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use App\Models\UserRole;

class UserRoleStoreRequest extends BaseRequest
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
				'required', 'string', 'min:3', 'max:255', Rule::unique(UserRole::class),
			],
			'status' => [
				'required', 'integer', new Enum(UserRoleStatus::class),
			],
			'permissions' => [
				'required', 'array',
			],
		];
	}
}
