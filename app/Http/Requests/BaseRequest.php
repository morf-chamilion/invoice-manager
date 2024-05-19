<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseRequest extends FormRequest
{
	/**
	 * Get all the safe attributes after validation.
	 */
	public function getAttributes(): array
	{
		return array_merge(
			$this->safe()->all()
		);
	}
}
