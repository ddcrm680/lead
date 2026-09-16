<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FileUploadService
{
    /**
     * Store an uploaded file.
     */
    public function store(
        UploadedFile $file,
        string $directory,
        string $prefix = 'file'
    ): string {
        $path = public_path($directory);

        File::ensureDirectoryExists($path);

        $filename = $prefix . '_' . Str::uuid() . '.' . $file->extension();
        $relativePath = $directory . '/' . $filename;

        $file->move($path, $filename);

        return $relativePath;
    }

    /**
     * Delete an uploaded file.
     */
    public function delete(?string $relativePath): void
    {
        if (!$relativePath) {
            return;
        }

        $path = public_path($relativePath);

        if (File::exists($path)) {
            File::delete($path);
        }
    }
}