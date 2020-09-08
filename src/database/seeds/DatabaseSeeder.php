<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserSeeder::class);

        DB::table('users')->insert(
            [
                'name' => 'Henry Cavill',
                'email' => 'henry@mail.com',
                'password' => Hash::make('henry123', ['rounds' => 12]),
            ]
        );

        DB::table('users')->insert(
            [
                'name' => 'Sarah Monroe',
                'email' => 'sarah@mail.com',
                'password' => Hash::make('sarah123', ['rounds' => 12]),
            ]
        );
            
        DB::table('users')->insert(
            [
                'name' => 'Thomas Palecki',
                'email' => 'thomas@mail.com',
                'password' => Hash::make('thomas123', ['rounds' => 12]),
            ]
        );
            
        DB::table('users')->insert(
            [
                'name' => 'Julie Anderson',
                'email' => 'julie@mail.com',
                'password' => Hash::make('julie123', ['rounds' => 12]),
            ]
        );
            
        DB::table('users')->insert(
            [
                'name' => 'Marco Phiser',
                'email' => 'marco@mail.com',
                'password' => Hash::make('marco123', ['rounds' => 12]),
            ]
        );

    }
}
