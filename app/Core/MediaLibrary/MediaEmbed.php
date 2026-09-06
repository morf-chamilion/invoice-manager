<?php

namespace App\Core\MediaLibrary;

use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

/**
 * Embeds media directly into rendered documents.
 *
 * dompdf resolves <img src> itself. A local path only works while media lives
 * on a local disk, and a remote URL costs an HTTP round trip per render and
 * requires the object to be publicly readable. Inlining the bytes as a data
 * URI works the same on every disk and needs neither.
 */
class MediaEmbed
{
    /**
     * Build a base64 data URI for the given media, or null when it cannot be read.
     */
    public static function dataUri(?Media $media): ?string
    {
        if (! $media) {
            return null;
        }

        try {
            $contents = Storage::disk($media->disk)->get($media->getPathRelativeToRoot());
        } catch (Throwable $e) {
            // A missing or unreadable file must not take the whole document
            // down: the logo is decoration, the invoice is the point.
            report($e);

            return null;
        }

        if ($contents === null) {
            return null;
        }

        return 'data:' . $media->mime_type . ';base64,' . base64_encode($contents);
    }
}
