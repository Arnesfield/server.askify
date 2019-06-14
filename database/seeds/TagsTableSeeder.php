<?php

use App\Tag;
use Illuminate\Database\Seeder;

class TagsTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->data() as $data) {
            $tag = ['name' => $data];
            Tag::create($tag);
        }
    }

    // make into a variable instead??
    private function data()
    {
        return [
            'html',
            'css',
            'javascript',
            'jquery',
            'vue.js',
            'react.js',
            'angular',
            'android programming',
            'iOS programming',
            'java',
            'c#',
            'swift',
            'kotlin',
        ];
    }
}
