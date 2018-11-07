<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        $path = $this->save($request->file('upload'), 'uploads', 'laravel');

        return response()->json($path);
    }


    public function save($file, $folder, $file_prefix)
    {
        $allowed_ext = ["png", "jpg", "gif", 'jpeg'];

        $folder_name = "uploads/images/$folder/" . date("Ym/d", time());

        // example：/home/vagrant/Code/laravel/public/uploads/images/avatars/201811/07/
        $upload_path = public_path() . '/' . $folder_name;

        $extension = strtolower($file->getClientOriginalExtension()) ?: 'png';

        // example：laravel_1493521050_7BVc9v9ujP.png
        $filename = $file_prefix . '_' . time() . '_' . str_random(10) . '.' . $extension;

        if (!in_array($extension, $allowed_ext)) {
            return false;
        }

        $file->move($upload_path, $filename);

        return [
            'path' => config('app.url') . "/$folder_name/$filename",
        ];
    }
}
