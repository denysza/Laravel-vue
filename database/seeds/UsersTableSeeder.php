<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'email'=>'ohashi@smile-again.co.jp','password'=>bcrypt('tosou2021'),'name1'=>'テスト','name2'=>'個人','message_key'=>md5(uniqid(rand(), true))
        ];

        DB::table('users')->insert($data);
    }
}
