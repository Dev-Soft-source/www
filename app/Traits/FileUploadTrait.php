<?php

namespace App\Traits;

trait FileUploadTrait
{
    public $folderName;

    public function removeFile($files)
    {
        if (is_array($files)) {
            foreach ($files as $file) {
                if (file_exists(config('filesystems.disks.public.root') . '/' . $file)) {
                    $url = pathinfo($file, PATHINFO_DIRNAME);
                    $url_var = explode('/', $url);
                    $folderName = end($url_var);
                    unlink('storage/' . $file);
                    rmdir('storage/media/temp/' . $folderName);
                }
            }
            return 1;
        } else {
            $url = pathinfo($files, PATHINFO_DIRNAME);
            $url_var = explode('/', $url);
            $folderName = end($url_var);
            unlink('storage/' . $files);
            rmdir('storage/media/temp/' . $folderName);
            return 1;
        }
        return 0;
    }
}