<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;

trait ImageUpload
{
    protected function handleImageUpload($image, $folder)
    {
        $ext = $image->getClientOriginalExtension();
        $filename = uniqid($folder).'.'.$ext;
        $image->move(public_path('assets/img/'.$folder.'/'), $filename);

        return $filename;
    }

    protected function handleImageDelete($image, $folder)
    {
        $imagePath = public_path('assets/img/'.$folder.'/'.$image);
        if (File::exists($imagePath)) {
            return File::delete($imagePath);
        }
    }
}
