<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('users')->insert([
          'nickname' => 'aaa',
          'email' => 'a@gmail.com',
          'password' => bcrypt('aaaaaa'),
          "confirmation_token" => "",
          "confirmed_at" => Carbon::now(),
      ]);

      DB::table('users')->insert([
          'nickname' => 'bbb',
          'email' => 'b@gmail.com',
          'password' => bcrypt('aaaaaa'),
          "confirmation_token" => "",
          "confirmed_at" => Carbon::now(),
      ]);

      DB::table('users')->insert([
          'nickname' => 'ccc',
          'email' => 'c@gmail.com',
          'password' => bcrypt('aaaaaa'),
          "confirmation_token" => "",
          "confirmed_at" => Carbon::now(),
      ]);

      DB::table('users')->insert([
          'nickname' => 'ddd',
          'email' => 'd@gmail.com',
          'password' => bcrypt('aaaaaa'),
          "confirmation_token" => "",
          "confirmed_at" => Carbon::now(),
      ]);
    }
}
