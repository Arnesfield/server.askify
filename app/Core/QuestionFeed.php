<?php

namespace App\Core;

use App\User;
use App\Tag;
use App\Question;
use App\Http\Resources\QuestionResource;

use Illuminate\Http\Request;

/**
 * 
 * QuestionFeed class
 * 
 * this class specifically returns filtered questions
 * based on the $user's:
 * - preferences (tags)
 * - history of answers (tags of questions)
 * 
 * once these tags are collected,
 * a map is created with its id and the number of occurrences of the tags.
 * depending on the question being checked,
 * its tags are compared to the map created
 * and each tag of the question that exists in the map
 * will have a score that is equivalent to the number of occurrences.
 * The total of the said score of a tag and all the other tags of the question
 * become the $priority value of the question.
 * Questions filtered by this process are then sorted by their urgency and priority.
 * The greater the $priority, the more likely it is to be suggested first.
 * 
 */
class QuestionFeed
{

    private $user;

    public function __construct(User $user) {
        $this->user = $user;
    }

    // the only thing you'll need ;)
    public function get(Request $request, $builder = null)
    {
        $tagIds = $this->getTags();
        $map = $this->getTagsMap();
        $builder = !$builder ? Question::with([]) : $builder;

        $cb = function($q) use ($tagIds) {
            $q->whereIn('tags.id', $tagIds);
        };

        $questions = $builder
            ->with(['tags' => $cb])
            ->whereHas('tags', $cb)
            // ->withCount(['tags' => $cb])
            // ->orderBy('tags_count', 'DESC')
            ->orderBy('urgent_at', 'DESC')
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

        // finally sort according to priority
        usort($questions, function($a, $b) {
            return $b['priority'] - $a['priority'];
        });

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
