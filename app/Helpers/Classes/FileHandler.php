<?php

namespace App\Helpers\Classes;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Class FileHandler
 * @package App\Helpers\Classes
 */
class FileHandler
{
    /**
     * @param UploadedFile|null $file
     * @param string|null $dir
     * @param string|null $fileName
     * @return string|null Stored file name, null if uploaded file is null or unable to upload
     */
    public static function storePhoto(?UploadedFile $file, ?string $dir = '', ?string $fileName = ''): ?string
    {
        if (!$file) {
            return null;
        }
        $fileName = $fileName ?: md5(time()) . '.' . $file->getClientOriginalExtension();
        if ($dir) {
            $dir=$dir."/".date('Y/F');
            if (file_exists($dir)) {
                mkdir($dir, 0777);
            }
        }
        //TODO: add image resizer to store thumbnails
        try {
            $path = Storage::putFileAs(
                $dir, $file, $fileName
            );
        } catch (Throwable $exception) {
            return $exception;
        }

        return $path;
    }

    /**
     * @param string|null $path
     * @return bool
     */
    public static function deleteFile(?string $path): bool
    {
        if (is_null($path)) {
            return false;
        }

        try {
            /** @var Storage $path */
            if (Storage::exists($path)) {
                Storage::delete($path);
            }
        } catch (\Exception $exception) {
            Log::debug($exception->getMessage());
            return false;
        }

        return true;
    }
}
