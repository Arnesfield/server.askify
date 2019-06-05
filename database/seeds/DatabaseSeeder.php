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
        $this->command->info('Starting database seeding.');
        $this->call('RolesTableSeeder');
        $this->call('UsersTableSeeder');
    }
}
