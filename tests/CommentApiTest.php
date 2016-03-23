<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Blog\Models\Blog;
use Blog\Models\Comment;

class CommentApiTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    protected $blogAttrs = [
        'title' => 'My First Post',
        'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
    ];

    protected $blogAttrsTwo = [
        'title' => 'My Second Post',
        'content' => 'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
    ];

    protected $commentAttrs = [
        'username' => 'AAA',
        'content' => 'Your opinion is invalid',
    ];

    protected $blog;
    protected $blogTwo;

    public function setUp() {
        parent::setUp();

        $this->blog = Blog::create($this->blogAttrs);
        $this->blogTwo = Blog::create($this->blogAttrsTwo);
    }

    public function testCreateComment()
    {
        $this->json('POST', 'comments', ['data' => [
            'type' => 'comments',
            'attributes' => $this->commentAttrs,
            'relationships' => [
                'blog' => [
                    'data' => [
                        'type' => 'blogs',
                        'id' => (string) $this->blog->id,
                    ],
                ],
            ],
        ]]);

        $this->assertResponseOk();

        $this->seeJson([
            'data' => [
                'type' => 'comments',
                'id' => '1',
                'attributes' => $this->commentAttrs,
                'relationships' => [
                    'blog' => [
                        'data' => [
                            'type' => 'blogs',
                            'id' => (string) $this->blog->id,
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertEquals('AAA', Comment::firstOrFail()->username);
    }

    public function testCannotCreateScoreForInvalidBlog()
    {
        $id = $this->blog->id;
        $this->blog->delete();

        $this->json('POST', 'comments', ['data' => [
            'type' => 'comments',
            'attributes' => $this->commentAttrs,
            'relationships' => [
                'blog' => [
                    'data' => [
                        'type' => 'blogs',
                        'id' => (string) $id,
                    ],
                ],
            ],
        ]]);

        $this->assertResponseStatus(400);

        $this->seeJson([
            'errors' => [
                [
                    'status' => '400',
                    'title' => 'Invalid Attribute',
                    'detail' => 'The selected blog is invalid.'
                ],
            ],
        ]);
    }

    public function testGetComment()
    {
        $blogScore = Comment::create([
            'username' => $this->commentAttrs['username'],
            'content' => $this->commentAttrs['content'],
            'blog' => $this->blog->id,
        ]);

        $this->json('GET', 'comments/1');

        $this->assertResponseOk();

        $this->seeJson([
            'data' => [
                'type' => 'comments',
                'id' => '1',
                'attributes' => $this->commentAttrs,
                'relationships' => [
                    'blog' => [
                        'data' => [
                            'type' => 'blogs',
                            'id' => (string) $this->blog->id,
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function testCommentIndex()
    {
        Comment::create([
            'username' => 'AAA',
            'content' => "1000000",
            'blog' => $this->blog->id,
        ]);
        Comment::create([
            'username' => 'AAA',
            'content' => "2000000",
            'blog' => $this->blogTwo->id,
        ]);

        $this->json('GET', 'comments');

        $this->assertResponseOk();

        $this->seeJson([
            'data' => [
                [
                    'type' => 'comments',
                    'id' => '1',
                    'attributes' => [
                        'username' => 'AAA',
                        'content' => "1000000",
                    ],
                    'relationships' => [
                        'blog' => [
                            'data' => [
                                'type' => 'blogs',
                                'id' => (string) $this->blog->id,
                            ],
                        ],
                    ],
                ],
                [
                    'type' => 'comments',
                    'id' => '2',
                    'attributes' => [
                        'username' => 'AAA',
                        'content' => "2000000",
                    ],
                    'relationships' => [
                        'blog' => [
                            'data' => [
                                'type' => 'blogs',
                                'id' => (string) $this->blogTwo->id,
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function testUpdateComment()
    {
        $blogScore = Comment::create([
            'username' => 'AAA',
            'content' => "1000000",
            'blog' => $this->blog->id,
        ]);

        $this->json('PATCH', "comments/{$blogScore->id}", ['data' => [
            'type' => 'comments',
            'id' => (string) $blogScore->id,
            'attributes' => [
                'username' => 'AAA',
                'content' => "2000000",
            ],
            'relationships' => [
                'blog' => [
                    'data' => [
                        'type' => 'blogs',
                        'id' => (string) $this->blog->id,
                    ],
                ],
            ],
        ]]);

        $this->assertResponseOk();

        $this->seeJson([
            'data' => [
                'type' => 'comments',
                'id' => '1',
                'attributes' => [
                    'username' => 'AAA',
                    'content' => "2000000",
                ],
                'relationships' => [
                    'blog' => [
                        'data' => [
                            'type' => 'blogs',
                            'id' => (string) $this->blog->id,
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertEquals(2000000, Comment::firstOrFail()->content);
    }

    public function testBlogDelete()
    {
        Comment::create([
            'username' => 'AAA',
            'content' => "1000000",
            'blog' => $this->blog->id,
        ]);
        Comment::create([
            'username' => 'AAA',
            'content' => "2000000",
            'blog' => $this->blogTwo->id,
        ]);

        $this->json('DELETE', 'comments/1');

        $this->assertResponseStatus(204);

        $this->assertEquals(1, Comment::count());
    }
}
