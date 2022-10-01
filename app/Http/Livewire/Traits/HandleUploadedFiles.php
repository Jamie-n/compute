<?php

namespace App\Http\Livewire\Traits;

use App\Models\Product;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

trait HandleUploadedFiles
{
    public $file;

    public function handleAttachedFiles(Product $product, $tempDiskName, $storageDiskName, $savedFileName): void
    {
        if (!$this->hasImageToHandle())
            return;

        $filename = self::moveUploadedFileFromTempDirectory(
            $tempDiskName,
            $storageDiskName,
            $savedFileName
        );

        $product->image = $filename;
    }

    public function moveUploadedFileFromTempDirectory(string $tempDiskName, string $diskName, string $savedFileName): string
    {
        $path = Storage::disk($tempDiskName)->path($this->file);
        $file = Storage::disk($tempDiskName)->get($this->file);

        $fileObject = new File($path);

        $savedFileName = "{$savedFileName}.{$fileObject->extension()}";

        Storage::disk($diskName)->put($savedFileName, $file);

        Storage::disk($tempDiskName)->delete($this->file);

        return $savedFileName;
    }

    public function hasImageToHandle(): bool
    {
        return !is_null($this->file);
    }
}
