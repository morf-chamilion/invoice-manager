<?php

namespace App\Messages;

use Illuminate\Support\Str;

abstract class BaseMessage
{
	/**
	 * Get the model base name.
	 */
	protected function modelName(): string
	{
		return class_basename(Str::lower($this->modelName()));
	}

	/**
	 * Get all resources success message.
	 */
	public function getAllSuccess(): string
	{
		return "Sucessfully got all {$this->modelName()}.";
	}

	/**
	 * Get all resources failed message.
	 */
	public function getAllFailed(): string
	{
		return "An error occured while getting all the {$this->modelName()}.";
	}

	/**
	 * Get resource success message.
	 */
	public function getSuccess(): string
	{
		return "Got the specified {$this->modelName()}.";
	}

	/**
	 * Get resource failed message.
	 */
	public function getFailed(): string
	{
		return "An error occured while getting this {$this->modelName()}.";
	}

	/**
	 * Create resource success message.
	 */
	public function createSuccess(): string
	{
		return "Successfully created a new {$this->modelName()}.";
	}

	/**
	 * Create resource failed message.
	 */
	public function createFailed(): string
	{
		return "An error occurred while saving this {$this->modelName()}.";
	}

	/**
	 * Update resource success message.
	 */
	public function updateSuccess(): string
	{
		return "Successfully updated this {$this->modelName()}.";
	}

	/**
	 * Update resource failed message.
	 */
	public function updateFailed(): string
	{
		return "An error occurred while updating this {$this->modelName()}.";
	}

	/**
	 * Delete resource success message.
	 */
	public function deleteSuccess(): string
	{
		return "The {$this->modelName()} has been successfully deleted.";
	}

	/**
	 * Delete resource failed message.
	 */
	public function deleteFailed(): string
	{
		return "An error occurred while deleting this {$this->modelName()}.";
	}

	/**
	 * Delete resource used message.
	 */
	public function deleteUsed(): string
	{
		return "The selected influencer cannot be deleted because this {$this->modelName()} is already used in the system.";
	}
}
