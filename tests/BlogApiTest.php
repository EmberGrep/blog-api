<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Blog\Models\Blog;

class BlogApiTest extends TestCase
{
    protected $blogAttrs = [
        'title' => 'My First Post',
        'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
    ];

    protected $blogAttrsTwo = [
        'title' => 'My Second Post',
        'content' => 'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
    ];

    use DatabaseMigrations, DatabaseTransactions;

    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testCreateBlog()
    {
        $this->json('POST', 'blogs', ['data' => [
            'type' => 'blogs',
            'attributes' => $this->blogAttrs,
            'relationships' => [
                'comments' => ['data' => []]
            ],
        ]])->seeJson([
            'data' => [
                'type' => 'blogs',
                'id' => '1',
                'attributes' => $this->blogAttrs,
                'relationships' => [
                    'comments' => ['data' => []]
                ],
            ],
        ]);

        $this->assertEquals($this->blogAttrs['title'], Blog::firstOrFail()->title);
    }

    public function testGetBlog()
    {
        Blog::create($this->blogAttrs);

        $this->json('GET', 'blogs/1');

        $this->assertResponseOk();

        $this->seeJson([
            'data' => [
                'type' => 'blogs',
                'id' => '1',
                'attributes' => $this->blogAttrs,
                'relationships' => [
                    'comments' => ['data' => []]
                ],
            ],
        ]);

        $this->assertEquals($this->gameName, Blog::firstOrFail()->name);
    }

    public function testBlogIndex()
    {
        Blog::create(['name' => $this->gameName]);
        Blog::create(['name' => $this->gameNameTwo]);

        $this->json('GET', 'blogs');

        $this->assertResponseOk();

        $this->seeJson([
            'data' => [
                [
                    'type' => 'blogs',
                    'id' => '1',
                    'attributes' => [
                        'name' => $this->gameName,
                    ],
                    'relationships' => [
                        'comments' => ['data' => []]
                    ],
                ],
                [
                    'type' => 'blogs',
                    'id' => '2',
                    'attributes' => [
                        'name' => $this->gameNameTwo,
                    ],
                    'relationships' => [
                        'comments' => ['data' => []]
                    ],
                ],
            ],
        ]);

        $this->assertEquals($this->gameName, Blog::firstOrFail()->name);
    }

    public function testBlogUpdate()
    {
        $game = Blog::create(['name' => $this->gameName]);

        $this->json('PATCH', "blogs/{$game->id}", ['data' => [
            'type' => 'blogs',
            'id' => $game->id,
            'attributes' => [
                'name' => $this->gameNameTwo,
            ],
            'relationships' => [
                'comments' => ['data' => []]
            ],
        ]]);

        $this->assertResponseOk();

        $this->seeJson([
            'data' => [
                'type' => 'blogs',
                'id' => '1',
                'attributes' => [
                    'name' => $this->gameNameTwo,
                ],
                'relationships' => [
                    'comments' => ['data' => []]
                ],
            ],
        ]);

        $this->assertEquals($this->gameNameTwo, Blog::firstOrFail()->name, 'Blog updates should be saved to DB');
    }

    public function testBlogDelete()
    {
        Blog::create(['name' => $this->gameName]);
        Blog::create(['name' => $this->gameNameTwo]);

        $this->json('DELETE', 'blogs/1');

        $this->assertResponseStatus(204);

        $this->assertEquals(1, Blog::count());
    }
}
