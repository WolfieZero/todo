<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Setup a known user first.
        DB::table('users')->insert([
            'name' => 'Neil Sweeney',
            'email' => 'me@wolfiezero.com',
            'password' => bcrypt('password'),
        ]);

        // With some todos.
        $items = rand(2, 5);
        for ($i=0;  $i<$items; $i++) {
            App\User::find(1)->todos()->save(factory(App\Todo::class)->make());
        }

        // Some random users.
        factory(App\User::class, 3)->create()->each(function($user) {
            // And some random todos.
            $items = rand(1, 5);
            for ($i=0;  $i<$items; $i++) {
                $user->todos()->save(factory(App\Todo::class)->make());
            }
        });
    }
}
