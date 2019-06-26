<?php

namespace App\Utils\FileUploadable;

use App\Utils\FileUpload;

use Illuminate\Http\Request;

trait FileUploadable
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
    ) {
        // use $paramKey for $colKey if not defined
        $colKey = $colKey === null ? $paramKey : $colKey;

        // do actual stuff
        $result = FileUpload::upload($request, $paramKey, $appendPath, $prefix);

        // save to model
        if ($result && $save) {
            return $this->update([ $colKey => $result ]);
        }
        return $result;
    }

    public function uploadImage(
        Request $request,
        $paramKey,
        $colKey = null,
        $appendPath = '',
        $prefix = 'IMG',
        $save = true
    ) {
        $appendPath = 'img/' . $appendPath;
        return $this->uploadFile($request, $paramKey, $colKey, $appendPath, $prefix, $save);
    }
}
