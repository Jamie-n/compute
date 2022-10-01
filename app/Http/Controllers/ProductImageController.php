<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    public function upload(Request $request)
    {
        $file = $request->file('filepond');

        $name = Crypt::encryptString($file->getClientOriginalName());

        $request
            ->file('filepond')
            ->storeAs(
                '',
                $name,
                config('filesystems.default_disk.product.temporary'));

        return response($name, 200, ['Content-Type' => 'text/plain']);
    }

    public function revert(Request $request)
    {
        $fileId = $request->getContent();

        Storage::disk(config('filesystems.default_disk.product.temporary'))->delete($fileId);

        return response('', 200);
    }

    public function load(Request $request)
    {
        $fileName = $request->image_name;

        $file = Storage::disk(config('filesystems.default_disk.product.storage'))->get($fileName);

        return response($file, 200, ['Content-Disposition' => 'inline', 'filename' => $fileName]);
    }

    public function remove(Request $request)
    {
        $fileName = $request->image_name;

        Storage::disk(config('filesystems.default_disk.product.storage'))->delete($fileName);

        return response('', 200);
    }
}
