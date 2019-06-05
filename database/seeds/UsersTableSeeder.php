<?php

use App\User;
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
        $users = factory(User::class, 4)->make();
        foreach ($users as $i => $user) {
            // change email here
            $user->email = $this->emails[$i];

            // save
            $user->save();
            $user->roles()->sync([$i + 1]);
        }
    }
}
