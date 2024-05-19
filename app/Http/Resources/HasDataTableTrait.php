<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

trait HasDataTableTrait
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		return [
			'draw' => intval($request->draw),
			'recordsFiltered' => $request->recordsFiltered['count'],
			'recordsTotal' => $request->recordsAll->count(),
			'data' => $this->transformRecords($request->recordsFiltered['data']),
		];
	}
}
