<?php

namespace App\Core\MediaLibrary;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

/**
 * Stores media under its UUID rather than its auto-increment id.
 *
 * The default generator produces keys like `25/Receipt.pdf`. On a public
 * bucket that is enumerable: ids are sequential and file names are often
 * predictable, so payment receipts could be walked by anyone. Keying on the
 * UUID that medialibrary already assigns to every row removes that.
 */
class UuidPathGenerator implements PathGenerator
{
    /**
     * Get the path for the given media, relative to the root storage path.
     */
    public function getPath(Media $media): string
    {
        return $this->getBasePath($media) . '/';
    }

    /**
     * Get the path for conversions of the given media.
     */
    public function getPathForConversions(Media $media): string
    {
        return $this->getBasePath($media) . '/conversions/';
    }

    /**
     * Get the path for responsive images of the given media.
     */
    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getBasePath($media) . '/responsive-images/';
    }

    /**
     * Get a unique, unguessable base path for the given media.
     */
    protected function getBasePath(Media $media): string
    {
        $prefix = config('media-library.prefix', '');

        if ($prefix !== '') {
            return $prefix . '/' . $media->uuid;
        }

        return $media->uuid;
    }
}
