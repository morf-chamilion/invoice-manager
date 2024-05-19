<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface BaseRepositoryInterface
{
	/** Get all records */
	public function getAll(): Collection|LengthAwarePaginator;

	/** Get a record */
	public function getById(int $id): ?Model;

	/** Delete a record */
	public function delete(int $id): bool|QueryException;

	/** Create new a record */
	public function create(array $attributes): Model;

	/** Update an existing record */
	public function update(int $id, array $newAttributes): bool;
}
