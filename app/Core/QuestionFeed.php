<?php

namespace App\Core;

use App\User;
use App\Tag;
use App\Question;
use App\Http\Resources\QuestionResource;

use Illuminate\Http\Request;

class QuestionFeed
{

    private $user;

    public function __construct(User $user) {
        $this->user = $user;
    }

    private function showTags($ids)
    {
        $tags = Tag::whereIn('id', $ids)->pluck('name', 'id');
        dd($tags->toArray());
    }

    //!! im just so sad :((((
    // it works anyway (i think)
    public function get(Request $request, $builder = null)
    {
        $tagIds = $this->getTags();
        $map = $this->getTagsMap();
        $builder = !$builder ? Question::with([]) : $builder;

        // dd($tagIds);
        // dd($map);

        // $this->showTags($tagIds);

        $cb = function($q) use ($tagIds) {
            $q->whereIn('tags.id', $tagIds);
        };

        $questions = $builder
            ->with(['tags' => $cb])
            ->whereHas('tags', $cb)
            // ->withCount(['tags' => $cb])
            // ->orderBy('tags_count', 'DESC')
            ->get()
            ->sortBy(function($e) use ($map) {
                // a question has tags
                // add total depending on $map
                $total = $e->tags->pluck('id')->map(function($tagId) use ($map) {
                    // this returns the count of tags
                    return $map[$tagId];
                })->sum();

                $e->priority += $total;
                return $total;
            });

        // use resource
        $questions = QuestionResource::collection($questions);
        $questions = $questions->toArray($request);

        usort($questions, function($a, $b) {
            return $b['priority'] - $a['priority'];
        });
        // return $questions->pluck('priority', 'id');
        // return $questions->pluck('priority');
        // $questions = json_encode($questions);
        return $questions;
    }

    public function getTags()
    {
        // get user tags
        // get all the answers and those tags
        $tagIds = $this->user->tags->pluck('id')->toArray();
        $answerIds = $this->user->answers->pluck('id')->toArray();
        $answerTagIds = Tag::whereHas('questions', function($q) use ($answerIds) {
            $q->whereHas('answers', function($q) use ($answerIds) {
                $q->whereIn('id', $answerIds);
            });
        })->pluck('id')->toArray();

        return array_merge($tagIds, $answerTagIds);
    }

    public function getTagsMap()
    {
        // create map
        $all = array_count_values($this->getTags());
        return $all;
    }
}
