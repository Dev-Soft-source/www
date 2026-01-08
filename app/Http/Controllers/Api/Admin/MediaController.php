<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Traits\FileUploadTrait;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

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
            'file' => 'required|image|mimes:jpg,png,jpeg,gif,svg',
        ]);

        $image = $request->file('file');
        $name = time() . '-' . $image->getClientOriginalName();
        $destinationPath = public_path('/home_page_icons');
        $image->move($destinationPath, $name);

        return $name;
    }
}
