<?php

namespace App\Services\Traits;

use App\Services\MediaService;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\MediaCollections\FileAdder;
use Spatie\MediaLibrary\InteractsWithMedia;

trait HandlesMedia
{
	public function __construct(
		private MediaService $mediaService
	) {
	}

	/**
	 * Synchronize model media.
	 *
	 * The service class is required to implement the HasMedia
	 * interface and should use the trait InteractsWithMedia.
	 */
	public function syncMedia(Model $model, string $collectionName, string|array|null $newMedia): void
	{
		$existingMedia = $model->getMedia($collectionName)
			->pluck('original_url')
			->toArray();

		$newMedia = is_array($newMedia) ? $newMedia : [$newMedia];

		$resolvedMedia = array_filter($newMedia, function ($fileId) {
			return $this->mediaService->isMediaUrl($fileId);
		});

		// Recognise deletions via a simple diffing method which
		// stands to be the safest method to handle deletions.
		$mediaToDelete = array_diff($existingMedia, $resolvedMedia);

		foreach ($mediaToDelete as $delete) {
			$this->mediaService->delete(basename(dirname($delete)));
		}

		$this->persistMedia($model, $collectionName, $newMedia);
	}

	/**
	 * Persist media from temporary storage.
	 */
	protected function persistMedia(
		Model $model,
		string $collectionName,
		array|string|null $fileIds
	): void {
		if (\is_array($fileIds)) {
			foreach ($fileIds as $fileId) {
				if ($this->mediaService->isTempMedia($fileId)) {
					$this->addMediaToModel($model, $fileId)?->toMediaCollection(
						$collectionName
					);

					$this->mediaService->deleteTempFileDirectory($fileId);
				}
			}
		} else {
			if ($this->mediaService->isTempMedia($fileIds)) {
				$this->addMediaToModel($model, $fileIds)?->toMediaCollection(
					$collectionName
				);

				$this->mediaService->deleteTempFileDirectory($fileIds);
			}
		}
	}

	/**
	 * Associate media to the model.
	 */
	protected function addMediaToModel(Model $model, string $fileId): ?FileAdder
	{
		$tempFilePath = $this->mediaService->getTempFilePath($fileId);

		if (!$tempFilePath) {
			return null;
		};

		/** @var InteractsWithMedia $model **/
		return $model->addMediaFromDisk(
			$tempFilePath,
			'media'
		);
	}
}
