<?php

namespace App\Services;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Str;

class MediaService
{
	/** 
	 * Storage disk for media files.
	 */
	public const DISK = 'media';

	/** 
	 * The temporary storage directory for files.
	 */
	public const TEMPDIR = 'temp';

	/**
	 * Get the storage disk for media.
	 */
	public function disk(): FilesystemAdapter
	{
		return Storage::disk($this::DISK);
	}

	/**
	 * Store the uploaded file in the temporary directory
	 * and return the encrypted file identifier.
	 */
	public function store(Request $request): string
	{
		$file = $this->getUploadedFile($request);

		$hashName = Str::random(40);

		$path = $hashName . DIRECTORY_SEPARATOR . $file->getClientOriginalName();

		$this->disk()->putFileAs($this::TEMPDIR, $file, $path);

		return Crypt::encryptString($hashName);
	}

	/**
	 * Delete a specific media file from storage.
	 */
	public function delete(int $mediaId): ?bool
	{
		return Media::find($mediaId)->delete();
	}

	/**
	 * Decrypt the FilePond field value data.
	 */
	public function decrypt(string $data): mixed
	{
		try {
			return Crypt::decryptString($data);
		} catch (DecryptException $e) {
			return false;
		}

		return false;
	}

	/**
	 * Get the storage file URL of the provided file identifier.
	 */
	public function getFileUrl(string $fileId): ?string
	{
		$filePath = $this->getFilePath($fileId);

		return $filePath ? $this->disk()->url($filePath) : null;
	}

	/**
	 * Get the storage file path of the provided file identifier.
	 */
	public function getFilePath(string $fileId): ?string
	{
		$files = $this->disk()->files($this->getFileDirectory($fileId));

		return Arr::first($files);
	}

	/**
	 * Get the temporary storage file path.
	 */
	public function getTempFilePath(string $fileId): ?string
	{
		$files = $this->disk()->files($this->getTempFileDirectory($fileId));

		return Arr::first($files);
	}

	/**
	 * Get the temporary storage file url.
	 */
	public function getTempFileUrl(string $fileId): ?string
	{
		$filePath = $this->getTempFilePath($fileId);

		return $filePath ? $this->disk()->url($filePath) : null;
	}

	/**
	 * Cleanup temporary file storage directory path.
	 */
	public function deleteTempFileDirectory(string $fileId): void
	{
		$this->disk()->deleteDirectory($this->getTempFileDirectory($fileId));
	}

	/**
	 * Determine if media resource is temporary.
	 */
	public function isTempMedia(mixed $mediaResource): bool
	{
		if (\is_string($mediaResource)) {
			return $this->decrypt($mediaResource);
		}

		return false;
	}

	/**
	 * Determine if media is a valid url.
	 *
	 * Ignores explicit path checks for performance reasons.
	 */
	public function isMediaUrl(mixed $mediaResource): bool
	{
		if (\is_string($mediaResource)) {
			return Str::isUrl($mediaResource);
		}

		return false;
	}

	/**
	 * Get the file storage directory path.
	 */
	protected function getFileDirectory(string $fileId): string
	{
		return DIRECTORY_SEPARATOR . $fileId;
	}

	/**
	 * Get the temporary file storage directory path.
	 */
	protected function getTempFileDirectory(string $fileId): string
	{
		return $this::TEMPDIR . DIRECTORY_SEPARATOR . $this->decrypt($fileId);
	}

	/**
	 * Get the file from request.
	 */
	protected function getUploadedFile(Request $request): UploadedFile
	{
		$field = array_key_first(Arr::dot($request->all()));

		return $request->file($field);
	}
}
