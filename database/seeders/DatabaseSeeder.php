<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Comment;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        $user = User::create(['name' => 'Garik', 'email' => 'garik@com.ua',
        'password' => Hash::make('12345678')]);
        $user2 = User::create(['name' => 'Vitalik', 'email' => 'vitalik@com.ua',
        'password' => Hash::make('12345678')]);
        $comment = Comment::create(['content'=>"My first comment","user_id"=> $user->id,
        "level"=>0]);
        Comment::create(["content"=> "My first comment subcomment","user_id"=> $user2->id,
        "level"=>1, "parent_id"=>$comment->id, "commented_comment_id"=>$comment->id]);
        Comment::create(["content"=> "My second comment", "user_id"=> $user2->id,
        "level"=>0,]);
    }
}
