<?php

use App\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // nvm if it runs individually lol
        foreach ($this->data() as $value) {
            Role::create($value);
        }
    }

    private function data()
    {
        return [
            ['name' => 'Administrator', 'description' => 'Manages the application.'],
            ['name' => 'Moderator', 'description' => 'Monitors events happening.'],
            ['name' => 'Student', 'description' => 'Asks the questions.'],
            ['name' => 'Expert', 'description' => 'Answers the questions.'],
        ];
    }
}
