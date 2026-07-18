<?php

use Illuminate\Support\Facades\Storage;

if (!function_exists('storage_url')) {
    /**
     * Generate a public URL for a stored file.
     *
     * Works for both local (public disk with symlink) and cloud (S3/R2).
     * Use this instead of asset('storage/'.$path) everywhere.
     *
     * @param  string|null  $path
     * @return string|null
     */
    function storage_url(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        return Storage::disk('public')->url($path);
    }
}
