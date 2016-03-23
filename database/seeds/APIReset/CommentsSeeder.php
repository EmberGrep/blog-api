<?php
namespace APIReset;

use Illuminate\Database\Seeder;
use Blog\Models\Comment;

class CommentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Comment::truncate();

        $comments = [
            [
                'username' => 'AAA',
                'content' => 1000,
                'blog' => 1,
            ],
            [
                'username' => 'CDC',
                'content' => 1500,
                'blog' => 1,
            ],
            [
                'username' => 'RFT',
                'content' => 2000,
                'blog' => 1,
            ],
        ];

        foreach ($comments as $comment) {
            Comment::create($comment);
        }
    }
}
