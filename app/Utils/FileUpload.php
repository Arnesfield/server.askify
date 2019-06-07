<?php

namespace App\Utils;

use Illuminate\Http\Request;

class FileUpload
{

    // $appendPath appends to destination $path
    // returns null | false | name of file
    public static function upload(
        Request $request,
        $key,
        $appendPath = '',
        $prefix = 'F'
    ) {
        // don't continue if it does not have the file anyway lol
        if ( ! $request->hasFile($key) ) {
            return null;
        }

        // do actual stuff
        $file = $request->file($key);
        $fileName = static::createFileName($file, $prefix);
        $path = static::getUploadsPath($appendPath);

        // move
        $moved = $file->move($path, $fileName);

        return $moved ? $fileName : false;
    }

    // helpers
    public static function getUploadsPath($append = '')
    {
        return base_path(env('APP_PATH_UPLOADS')) . $append;
    }

    // not sure if this is the best solution for uniqueness
    public static function createFileName($file, $prefix = 'F')
    {
        // $fileName = $file->getClientOriginalName();
        $datetime = date('Y-m-d_h-i-s_A');
        $ext = $file->getClientOriginalExtension();
        $rand = str_random();

        $result = "{$prefix}_{$datetime}_{$rand}.{$ext}";
        return $result;
    }
}
