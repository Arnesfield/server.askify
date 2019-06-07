<?php

namespace App\Utils\FileUploadable;

use Illuminate\Http\Request;

interface FileUploadableContract
{

    // $appendPath appends to destinationPath
    // returns true | false | null | name of file
    public function uploadFile(
        Request $request,
        $paramKey,
        $colKey = null,
        $appendPath = '',
        $prefix = 'F',
        $save = true
    );

    public function uploadImage(
        Request $request,
        $paramKey,
        $colKey = null,
        $appendPath = 'img/',
        $prefix = 'IMG',
        $save = true
    );
}
