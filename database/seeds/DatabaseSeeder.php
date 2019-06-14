<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //! only seed important stuff
        // data for development should not be included here
        // include instead in seed file in /scripts/
        $this->command->info('Starting database seeding.');
        $this->call('RolesTableSeeder');
        // $this->call('UsersTableSeeder');
    }
}
