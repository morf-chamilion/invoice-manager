<?php

namespace App\Repositories;

use App\Exceptions\JsonResponseException;
use App\Models\Interfaces\HasRelationsInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

abstract class BaseRepository implements BaseRepositoryInterface
{
	public string $modelName;

	public function __construct(
		protected Model $model,
	) {
	}

	/**
	 * Get the model instance.
	 */
	public function getModel()
	{
		return $this->model;
	}

	/**
	 * Get the model class name.
	 */
	public function modelClassName(): string
	{
		return $this->model::class;
	}

	/**
	 * Get the model human readable name.
	 */
	public function modelName(): string
	{
		return Str::of(
			class_basename($this->modelName ?? $this->model)
		)->snake(' ')->lower();
	}

	/**
	 * Override the default model name.
	 */
	public function setModelName(string $modelName): void
	{
		$this->modelName = $modelName;
	}

	/**
	 * Check for parent relations and throw exceptions if they exists.
	 */
	public function checkModelHasParentRelations(Model $record): void
	{
		if (!$this->model instanceof HasRelationsInterface) {
			return;
		}

		foreach ($this->model->defineHasRelationships() as $relationName) {
			$relation = $record->{$relationName}();

			if ($relation instanceof Relation && $relation->exists()) {
				$relationName = Str::of($relationName)->snake(' ');
				$relatedIds = $relation->pluck('id')->implode(',');

				throw new JsonResponseException("Record is associated with {$relationName} (IDs: {$relatedIds})");
			}
		}
	}
}
