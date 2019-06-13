<?php

namespace App\Utils\Taggable;

use App\Tag;

use Illuminate\Http\Request;

trait Taggable
{
    public function syncTags(Request $request, $key = 'tags', $return = false)
    {
        $tags = requestGetArray($request, $key);
        if ($tags === false) {
            return false;
        }

        // retain ids, if object, add it then append the id ;)
        // complicated sht right

        $ids = [];

        foreach ($tags as $tag) {
            // if not an id
            if ( !(is_numeric($tag) || is_string($tag)) ) {
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
            : $me->tags()->sync($ids);
    }
}
