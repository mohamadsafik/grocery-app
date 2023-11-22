<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class Image extends ApiController
{
    public function show($filename)
    {
        $path = storage_path('app/public/images/' . $filename);

        if (!Storage::exists('public/images/' . $filename)) {
            abort(404);
        }

        $file = Storage::get('public/images/' . $filename);
        $type = Storage::mimeType('public/images/' . $filename);

        return Response::make($file, 200, ['Content-Type' => $type]);
    }
}
