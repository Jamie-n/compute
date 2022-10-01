<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;

class ClearTempDirectoryCommand extends Command
{
    protected $signature = 'files:clear-temp-directory';

    protected $description = 'Clear filepond temp directory when the created at date of the file is 12 hours or older';

    public function handle()
    {
        $cutoff = Date::now()->subHours(12);

        $tempDisk = Storage::disk(config('filesystems.default_disk.product.temporary'));
        $allTempFiles = collect($tempDisk->allFiles());

        $filesToDelete = $allTempFiles->map(function ($filename) use ($cutoff, $tempDisk) {
            $modifiedDate = Date::createFromTimestamp($tempDisk->lastModified($filename));

            if ($modifiedDate->isBefore($cutoff))
                return $filename;

            return '';

        })->filter();

        $tempDisk->delete($filesToDelete->toArray());

        return 1;
    }
}
