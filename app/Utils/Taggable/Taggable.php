<?php

namespace App\Utils\Taggable;

use App\Tag;

use Illuminate\Http\Request;

trait Taggable
{
    public function syncTags($request, $key = 'tags', $return = false)
    {
        // set defaults
        $tags = $request instanceof Request
            ? requestGetArray($request, $key)
            : (is_array($request) ? $request : false);

        if (is_bool($key)) {
            $return = $key;
            $key = 'tags';
        }

        if ($tags === false) {
            return false;
        }

        // retain ids, if object, add it then append the id ;)
        // complicated sht right

        $ids = [];

        foreach ($tags as $tag) {
            // if not an id
            if ( !(is_numeric($tag)) ) {
                // convrt tag to assoc array if string
                if (is_string($tag)) {
                    $tag = ['name' => $tag];
                }

                // find Tag, else create one
                $mTag = Tag::where('name', $tag['name'])->first();
                if (!$mTag) {
                    $R = Tag::getValidationRules();
                    $validator = \Validator::make($tag, $R['rules'], $R['errors']);

                    // disregard if validation fails
                    if ($validator->fails()) {
                        continue;
                    }

                    $nRequest = new Request;
                    $nRequest->replace($tag);

                    $mTag = Tag::makeMe($nRequest);
                    // if not created, just skip it mygosh
                    if (!$mTag) {
                        continue;
                    }
                }
                
                // assign to $tag to append id
                $tag = $mTag->id;
            }

            // obviously
            $ids[] = $tag;
        }

        return $return
            ? $ids
            : $this->tags()->sync($ids);
    }
}
