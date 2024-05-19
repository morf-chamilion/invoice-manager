<?php

namespace App\Models\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasUpdatedBy
{
	/**
	 * Get the user that recently updated the model.
	 */
	public function updatedByUser(): BelongsTo
	{
		return $this->belongsTo(User::class, 'updated_by');
	}
}
