<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Traits\FileUploadTrait;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;

class MediaController extends Controller
{
    use StatusResponser;
    use FileUploadTrait;

    public function process(Request $request)
    {
        // Save the uploaded file to the destination folder
        $file = $request->file('media');
        $fileName = $file->getClientOriginalName(); // Get the original file name
        $destination_path =  public_path('/flag_icons');
        $file->move($destination_path,$fileName);

        // Return the path to the uploaded file
        return $fileName;
    }

    public function revert(Request $request)
    {
        $media = $request->media;
        $media = json_decode($media, 1);
        $result = $this->removeFile($media);
        if ($result) {
            return $result;
        }
        return false;
    }

    public function uploadImage(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file|max:10240',
        ]);

        $image = $request->file('file');
        $extension = strtolower($image->getClientOriginalExtension());
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'avif'];

        if (!in_array($extension, $allowedExtensions, true)) {
            throw ValidationException::withMessages([
                'file' => ['The file must be an image of type: jpg, jpeg, png, gif, svg, webp, or avif.'],
            ]);
        }

        $name = time() . '-' . preg_replace('/[^A-Za-z0-9._-]/', '-', $image->getClientOriginalName());
        $destinationPath = public_path('/home_page_icons');

        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        $image->move($destinationPath, $name);

        return response()->json($name);
    }
}
