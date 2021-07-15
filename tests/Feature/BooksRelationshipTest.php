<?php

namespace Tests\Feature;

use App\Author;
use App\Book;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class BooksRelationshipTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic feature test example.
     * @test @watch
     * @return void
     */
    public function it_returns_a_relationship_to_authors_adhering_to_json_api_spec()
    {

        $book = factory(Book::class)->create();
        $authors = factory(Author::class, 2)->create();
        // print_r($authors->pluck('id')->toArray());
        $book->authors()->sync($authors->pluck('id'));

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->getJson('/api/v1/books/1', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200)->assertJson([
            'data' => [
                'id' => '1',
                'type' => 'books',
                'relationships' => [
                    'authors' => [
                        'links' => [
                            'self' => route(
                                'books.relationships.authors',
                                ['book' => $book->id]
                            ),
                            'related' => route(
                                'books.authors',
                                ['book' => $book->id]
                            ),
                        ],
                        'data' => [
                            [
                                'id' => $authors->get(0)->id,
                                'type' => 'authors'
                            ],
                            [
                                'id' => $authors->get(1)->id,
                                'type' => 'authors'
                            ]
                        ]
                    ]
                ]
            ]
        ]);


        // $response->assertStatus(200);
    }

    /**
     * A basic feature test example.
     * @test @watch
     * @return void
     */
    public function a_relationship_link_to_authors_returns_all_related_authors_as_resource_id_object()
    {

        $book = factory(Book::class)->create();
        $authors = factory(Author::class, 2)->create();
        // print_r($authors->pluck('id')->toArray());
        $book->authors()->sync($authors->pluck('id'));

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->getJson('/api/v1/books/1/relationships/authors', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200)->assertJson([
            'data' => [

                [
                    'id' => $authors->get(0)->id,
                    'type' => 'authors'
                ],
                [
                    'id' => $authors->get(1)->id,
                    'type' => 'authors'
                ]
            ]

        ]);


        // $response->assertStatus(200);
    }
    /**
     * A basic feature test example.
     * @test @watch
     * @return void
     */
    public function it_can_modify_relationships_to_authors_and_add_new_relationships()
    {
        $book = factory(Book::class)->create();
        $authors = factory(Author::class, 10)->create();
        // print_r($authors->pluck('id')->toArray());
        $book->authors()->sync($authors->pluck('id'));

        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $this->patchJson('api/v1/books/1/relationships/authors', [
            'data' => [
                [
                    'id' => '5',
                    'type' => 'authors'
                ],
                [
                    'id' => '6',
                    'type' => 'authors'
                ]
            ],
        ],  [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(204);

        $this->assertDatabaseHas(
            'author_book',
            [
                'book_id' => 1,
                'author_id' => 5,
                "created_at" => null,
                "updated_at" => null,
            ]
        )->assertDatabaseHas('author_book',   [
            'book_id' => 1,
            'author_id' => 6,
        ]);
    }

    /**
     * @test
     * @
     */
    public function test_case()
    {
        $this->withoutExceptionHandling();
    }
}
