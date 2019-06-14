<?php

use App\User;
use App\Question;
use App\Answer;
use App\Tag;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    // use this to avoid random emails by faker
    private $emails = [
        'admin@email.com',
        'mod@email.com',
        'student@email.com',
        'expert@email.com',
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // make the rest experts
        $users = factory(User::class, 15)->make();
        $separator = 9;

        foreach ($users as $i => $user) {
            // get the role first
            $role = $i + 1;
            $exceed = $role > 4;
            $isStudent = $exceed && $role < $separator;
            $isExpert = $exceed && $role >= $separator;

            $role = $isStudent ? 3 : ($isExpert ? 4 : $role);

            $emailPrefix = $role == 3 ? 'student' : 'expert';

            // change email here
            $n = $i - 3;
            $user->email = $i < 4
                ? $this->emails[$i]
                : "{$emailPrefix}{$n}@email.com";

            // save
            $user->save();

            // relationships
            $user->roles()->sync([$role]);
            $user->syncTags($this->generateTagIds());

            if ($role == 3) {
                // if student, just yah
                $this->askQuestions($user);
            } else if ($role == 4) {
                // if expert, answer questions
                $this->answerQuestions($user);
            }
        }
    }

    private function generateTagIds($min = 0)
    {
        $tags = Tag::all();
        $noOfTags = rand($min, $tags->count());
        $allTags = $tags->random($noOfTags);
        return $allTags->pluck('id')->toArray();
    }

    private function askQuestions(User $user, $max = 10)
    {
        // a student asks, so yea add questions and tags
        $questions = factory(Question::class, rand(0, $max))->make();
        foreach ($questions as $q) {
            $user->questions()->save($q);

            // then add tags to the questions
            $q->syncTags($this->generateTagIds());
        }

        return $questions;
    }

    private function answerQuestions(User $user)
    {
        $questions = Question::all();

        $noOfAnswers = rand(1, $questions->count());
        $qToBeAnswered = $questions->random($noOfAnswers);
        $answers = factory(Answer::class, $noOfAnswers)->make();

        foreach ($answers as $i => $a) {
            $a->user()->associate($user);
            $a->question()->associate($qToBeAnswered[$i]);
            $a->save();
        }

        return $answers;
    }
}
