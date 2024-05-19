<?php

namespace App\Models\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasCreatedBy
{
	/**
	 * Get the user that created the model.
	 */
	public function createdByUser(): BelongsTo
	{
		return $this->belongsTo(User::class, 'created_by');
	}
}
