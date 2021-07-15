<?php

namespace Tests\Feature;

use App\Book;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class BooksTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     * 
     */
    public function it_returns_a_book_as_a_resource_object()
    {
        $book = factory(Book::class)->create();
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $this->getJson('api/v1/books/1', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200)->assertJson([
            "data" => [
                'id' => '1',
                'type' => "books",
                "attributes" => [
                    "title" => $book->title,
                    "description" => $book->description,
                    "publication_year" => $book->publication_year,
                    "created_at" => $book->created_at->toJson(),
                    "updated_at" => $book->updated_at->toJson(),
                ]
            ]
        ]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     * 
     * 
     */
    public function it_returns_all_books_as_a_collection_of_resource_objects()
    {

        $book = factory(Book::class, 2)->create();
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $this->getJson('api/v1/books', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200)->assertJson([
            "data" => [
                [
                    'id' => '1',
                    'type' => "books",
                    "attributes" => [
                        "title" => $book[0]->title,
                        "description" => $book[0]->description,
                        "publication_year" => $book[0]->publication_year,
                        "created_at" => $book[0]->created_at->toJson(),
                        "updated_at" => $book[0]->updated_at->toJson(),
                    ]
                ],
                [
                    'id' => '2',
                    'type' => "books",
                    "attributes" => [
                        "title" => $book[1]->title,
                        "description" => $book[1]->description,
                        "publication_year" => $book[1]->publication_year,
                        "created_at" => $book[1]->created_at->toJson(),
                        "updated_at" => $book[1]->updated_at->toJson(),
                    ]
                ]
            ]
        ]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     * 
     * 
     */
    public function it_can_sort_books_by_title_through_a_sort_query_parameter()
    {
        // $books = factory(Book::class, 2)->create();
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $books = collect([
            'Building an API with Laravel',
            'Classes are our blueprint',
            'Adhering to json spec',
        ])->map(function ($title) {
            return factory(Book::class)->create([
                'title' => $title,
            ]);
        });

        $this->getJson('api/v1/books?sort=title', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200)->assertJson([
            "data" => [
                [
                    'id' => '3',
                    'type' => "books",
                    "attributes" => [
                        "title" => $books[2]->title,
                        "description" => $books[2]->description,
                        "publication_year" => $books[2]->publication_year,
                        "created_at" => $books[2]->created_at->toJson(),
                        "updated_at" => $books[2]->updated_at->toJson(),
                    ]
                ],
                [
                    'id' => '1',
                    'type' => "books",
                    "attributes" => [
                        "title" => $books[0]->title,
                        "description" => $books[0]->description,
                        "publication_year" => $books[0]->publication_year,
                        "created_at" => $books[0]->created_at->toJson(),
                        "updated_at" => $books[0]->updated_at->toJson(),
                    ]
                ],
                [
                    'id' => '2',
                    'type' => "books",
                    "attributes" => [
                        "title" => $books[1]->title,
                        "description" => $books[1]->description,
                        "publication_year" => $books[1]->publication_year,
                        "created_at" => $books[1]->created_at->toJson(),
                        "updated_at" => $books[1]->updated_at->toJson(),
                    ]
                ]
            ]

        ]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     * 
     * 
     */
    public function it_can_paginate_books_through_a_page_query_parameter()
    {
        $books = factory(Book::class, 10)->create();
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->getJson('api/v1/books?page[size]=5&page[number]=1', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200)->assertJson([
            "data" => [
                [
                    'id' => '1',
                    'type' => "books",
                    "attributes" => [
                        "title" => $books[0]->title,
                        "description" => $books[0]->description,
                        "publication_year" => $books[0]->publication_year,
                        "created_at" => $books[0]->created_at->toJson(),
                        "updated_at" => $books[0]->updated_at->toJson(),
                    ]
                ],
                [
                    'id' => '2',
                    'type' => "books",
                    "attributes" => [
                        "title" => $books[1]->title,
                        "description" => $books[1]->description,
                        "publication_year" => $books[1]->publication_year,
                        "created_at" => $books[1]->created_at->toJson(),
                        "updated_at" => $books[1]->updated_at->toJson(),
                    ]
                ],
                [
                    'id' => '3',
                    'type' => "books",
                    "attributes" => [
                        "title" => $books[2]->title,
                        "description" => $books[2]->description,
                        "publication_year" => $books[2]->publication_year,
                        "created_at" => $books[2]->created_at->toJson(),
                        "updated_at" => $books[2]->updated_at->toJson(),
                    ]
                ],
                [
                    'id' => '4',
                    'type' => "books",
                    "attributes" => [
                        "title" => $books[3]->title,
                        "description" => $books[3]->description,
                        "publication_year" => $books[3]->publication_year,
                        "created_at" => $books[3]->created_at->toJson(),
                        "updated_at" => $books[3]->updated_at->toJson(),
                    ]
                ],

                [
                    'id' => '5',
                    'type' => "books",
                    "attributes" => [
                        "title" => $books[4]->title,
                        "description" => $books[4]->description,
                        "publication_year" => $books[4]->publication_year,
                        "created_at" => $books[4]->created_at->toJson(),
                        "updated_at" => $books[4]->updated_at->toJson(),
                    ]
                ],
            ],
            'links' => [
                'first' => route('books.index', ['page[size]' => 5, 'page[number]' => 1]),
                'last' => route('books.index', ['page[size]' => 5, 'page[number]' => 2]),
                'prev' => null,
                'next' => route('books.index', ['page[size]' => 5, 'page[number]' => 2]),
            ]
        ]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     * 
     */
    public function it_can_create_a_book_from_a_resource_object()
    {
        // $book = factory(Book::class)->create();
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $this->postJson('api/v1/books', [
            "data" => [
                "type" => "books",
                "attributes" => [
                    "title" => "James Bond",
                    "description" => "Action movie",
                    "publication_year" => "2010"
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(201)->assertJson([
            "data" => [
                "id" => "1",
                "type" => "books",
                "attributes" => [
                    "title" => "James Bond",
                    "description" => "Action movie",
                    "publication_year" => "2010",
                    'created_at' => now()->setMilliseconds(0)->toJSON(),
                    'updated_at' => now()->setMilliseconds(0)->toJSON(),
                ],
            ]
        ])->assertHeader('Location', url('/api/v1/books/1'));

        $this->assertDatabaseHas('books', [
            "id" => "1",
            "title" => "James Bond",
            "description" => "Action movie",
            "publication_year" => "2010",
        ]);
    }


    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     * 
     */
    public function it_validates_that_type_member_is_given_when_creating_a_book()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->postJson('api/v1/books', [
            "data" => [
                "type" => "",
                "attributes" => [
                    "title" => "James Bond",
                    "description" => "Action movie",
                    "publication_year" => "2010",
                ],
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)->assertJson([
            'errors' => [
                [
                    'title' => 'Validation Error',
                    'details' => 'The data.type field is required.',
                    'source' => [
                        'pointer' => '/data/type',
                    ]
                ]
            ]
        ]);

        $this->assertDatabaseMissing('books', [
            "id" => 1,
            "title" => "James Bond",
            "description" => "Action movie",
            "publication_year" => "2010",
        ]);
    }


    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     * 
     */
    public function it_validates_that_type_member_has_the_value_of_books_when_creating_an_book()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->postJson('api/v1/books', [
            "data" => [
                "type" => "book",
                "attributes" => [
                    "title" => "James Bond",
                    "description" => "Action movie",
                    "publication_year" => "2010",
                ],
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)->assertJson([
            'errors' => [
                [
                    'title' => 'Validation Error',
                    'details' => 'The selected data.type is invalid.',
                    'source' => [
                        'pointer' => '/data/type',
                    ]
                ]
            ]
        ]);

        $this->assertDatabaseMissing('books', [
            "id" => 1,
            "title" => "James Bond",
            "description" => "Action movie",
            "publication_year" => "2010",
        ]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     *  
     * 
     */
    public function it_validates_that_attributes_member_has_been_given_when_creating_a_book()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->postJson('api/v1/books', [
            "data" => [
                "type" => "books",
                "attributes" => "",
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)->assertJson([
            'errors' => [
                [
                    'title' => 'Validation Error',
                    'details' => 'The data.attributes field is required.',
                    'source' => [
                        'pointer' => '/data/attributes',
                    ]
                ]
            ]
        ]);

        $this->assertDatabaseMissing('books', [
            "id" => 1,
            "title" => "James Bond",
            "description" => "Action movie",
            "publication_year" => "2010",
        ]);
    }


    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     *  
     * 
     */
    public function it_validates_that_attributes_member_is_an_object_when_creating_a_book()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->postJson('api/v1/books', [
            "data" => [
                "type" => "books",
                "attributes" => "not an object",
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)->assertJson([
            'errors' => [
                [
                    'title' => 'Validation Error',
                    'details' => 'The data.attributes must be an array.',
                    'source' => [
                        'pointer' => '/data/attributes',
                    ]
                ]
            ]
        ]);

        $this->assertDatabaseMissing('books', [
            "id" => 1,
            "title" => "James Bond",
            "description" => "Action movie",
            "publication_year" => "2010",
        ]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     *  
     * 
     */
    public function it_validates_that_the_title_attribute_is_given_when_creating_a_book()
    {

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->postJson('api/v1/books', [
            "data" => [
                "type" => "books",
                "attributes" => [
                    "description" => "Action movie",
                    "publication_year" => "2010",
                ],
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)->assertJson([
            'errors' => [
                [
                    'title' => 'Validation Error',
                    'details' => 'The data.attributes.title field is required.',
                    'source' => [
                        'pointer' => '/data/attributes/title',
                    ]
                ]
            ]
        ]);

        $this->assertDatabaseMissing('books', [
            "id" => 1,
            "title" => "James Bond",
            "description" => "Action movie",
            "publication_year" => "2010",
        ]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     *  
     * 
     */
    public function it_validates_that_the_title_attribute_is_a_string_when_creating_a_book()
    {

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->postJson('api/v1/books', [
            "data" => [
                "type" => "books",
                "attributes" => [
                    "title" => 233,
                    "description" => "Action movie",
                    "publication_year" => "2010",
                ],
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)->assertJson([
            'errors' => [
                [
                    'title' => 'Validation Error',
                    'details' => 'The data.attributes.title must be a string.',
                    'source' => [
                        'pointer' => '/data/attributes/title',
                    ]
                ]
            ]
        ]);

        $this->assertDatabaseMissing('books', [
            "id" => 1,
            "title" => "James Bond",
            "description" => "Action movie",
            "publication_year" => "2010",
        ]);
    }


    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     * 
     * 
     */
    public function it_validates_that_the_description_attribute_is_given_when_creating_a_book()
    {

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->postJson('api/v1/books', [
            "data" => [
                "type" => "books",
                "attributes" => [
                    "title" => "James Bond",
                    "publication_year" => "2010",
                ],
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)->assertJson([
            'errors' => [
                [
                    'title' => 'Validation Error',
                    'details' => 'The data.attributes.description field is required.',
                    'source' => [
                        'pointer' => '/data/attributes/description',
                    ]
                ]
            ]
        ]);

        $this->assertDatabaseMissing('books', [
            "id" => 1,
            "title" => "James Bond",
            "description" => "Action movie",
            "publication_year" => "2010",
        ]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     * 
     */
    public function it_validates_that_the_description_attribute_is_a_string_when_creating_a_book()
    {

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->postJson('api/v1/books', [
            "data" => [
                "type" => "books",
                "attributes" => [
                    "title" => "James Bond",
                    "description" => 1222,
                    "publication_year" => "2010",
                ],
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)->assertJson([
            'errors' => [
                [
                    'title' => 'Validation Error',
                    'details' => 'The data.attributes.description must be a string.',
                    'source' => [
                        'pointer' => '/data/attributes/description',
                    ]
                ]
            ]
        ]);

        $this->assertDatabaseMissing('books', [
            "id" => 1,
            "title" => "James Bond",
            "description" => "Action movie",
            "publication_year" => "2010",
        ]);
    }


    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     * 
     */
    public function it_validates_that_the_publication_year_attribute_is_given_when_creating_a_book()
    {

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->postJson('api/v1/books', [
            "data" => [
                "type" => "books",
                "attributes" => [
                    "title" => "James Bond",
                    "description" => "Action movie",
                ],
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)->assertJson([
            'errors' => [
                [
                    'title' => 'Validation Error',
                    'details' => 'The data.attributes.publication year field is required.',
                    'source' => [
                        'pointer' => '/data/attributes/publication_year',
                    ]
                ]
            ]
        ]);

        $this->assertDatabaseMissing('books', [
            "id" => 1,
            "title" => "James Bond",
            "description" => "Action movie",
            "publication_year" => "2010",
        ]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     * 
     */
    public function it_validates_that_the_publication_year_attribute_is_a_string_when_creating_a_book()
    {

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->postJson('api/v1/books', [
            "data" => [
                "type" => "books",
                "attributes" => [
                    "title" => "James Bond",
                    "description" => "Action movie",
                    "publication_year" => 2010,
                ],
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)->assertJson([
            'errors' => [
                [
                    'title' => 'Validation Error',
                    'details' => 'The data.attributes.publication year must be a string.',
                    'source' => [
                        'pointer' => '/data/attributes/publication_year',
                    ]
                ]
            ]
        ]);

        $this->assertDatabaseMissing('books', [
            "id" => 1,
            "title" => "James Bond",
            "description" => "Action movie",
            "publication_year" => "2010",
        ]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     * @
     * 
     */
    public function it_can_update_a_book_from_a_resource_object()
    {
        $book = factory(Book::class)->create();
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $this->patchJson('api/v1/books/1', [
            "data" => [
                "id" => "1",
                "type" => "books",
                "attributes" => [
                    "title" => "James Bond",
                    "description" => "A book by james bond",
                    "publication_year" => "2010",
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200)->assertJson([
            "data" => [
                "id" => "1",
                "type" => "books",
                "attributes" => [
                    "title" => "James Bond",
                    "description" => "A book by james bond",
                    "publication_year" => "2010",
                    'created_at' => now()->setMilliseconds(0)->toJSON(),
                    'updated_at' => now()->setMilliseconds(0)->toJSON(),
                ],
            ]
        ]);

        $this->assertDatabaseHas('books', [
            "id" => "1",
            "title" => "James Bond",
            "description" => "A book by james bond",
            "publication_year" => "2010",
        ]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     * @
     */
    public function it_validates_that_an_id_member_is_given_when_updating_a_book()
    {
        $book = factory(Book::class)->create();
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $this->patchJson('api/v1/books/1', [
            "data" => [
                "type" => "books",
                "attributes" => [
                    "title" => "James Bond",
                    "description" => "A book by james bond",
                    "publication_year" => "2010",
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)->assertJson([
            "errors" => [
                [
                    "title" => "Validation Error",
                    "details" => "The data.id field is required.",
                    "source" => [
                        "pointer" => "/data/id"
                    ]
                ]
            ]
        ]);

        $this->assertDatabaseMissing('books', [
            "id" => "1",
            "title" => "James Bond",
            "description" => "A book by james bond",
            "publication_year" => "2010",
        ]);
    }


    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     * @
     */
    public function it_validates_that_an_id_member_is_a_string_when_updating_a_book()
    {

        $book = factory(Book::class)->create();
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $this->patchJson('api/v1/books/1', [
            "data" => [
                "id" => 1,
                "type" => "books",
                "attributes" => [
                    "title" => "James Bond",
                    "description" => "A book by james bond",
                    "publication_year" => "2010",
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)->assertJson([
            "errors" => [
                [
                    "title" => "Validation Error",
                    "details" => "The data.id must be a string.",
                    "source" => [
                        "pointer" => "/data/id"
                    ]
                ]
            ]
        ]);

        $this->assertDatabaseMissing('books', [
            "id" => "1",
            "title" => "James Bond",
            "description" => "A book by james bond",
            "publication_year" => "2010",
        ]);
    }



    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     * @
     */
    public function it_validates_that_type_member_is_given_when_updating_a_book()
    {

        $book = factory(Book::class)->create();
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $this->patchJson('api/v1/books/1', [
            "data" => [
                "id" => "1",
                "attributes" => [
                    "title" => "James Bond",
                    "description" => "A book by james bond",
                    "publication_year" => "2010",
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)->assertJson([
            "errors" => [
                [
                    "title" => "Validation Error",
                    "details" => "The data.type field is required.",
                    "source" => [
                        "pointer" => "/data/type"
                    ]
                ]
            ]
        ]);

        $this->assertDatabaseMissing('books', [
            "id" => "1",
            "title" => "James Bond",
            "description" => "A book by james bond",
            "publication_year" => "2010",
        ]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     * @
     */
    public function it_validates_that_type_member_has_the_value_of_books_when_updating_a_book()
    {

        $book = factory(Book::class)->create();
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $this->patchJson('api/v1/books/1', [
            "data" => [
                "id" => "1",
                "type" => "book",
                "attributes" => [
                    "title" => "James Bond",
                    "description" => "A book by james bond",
                    "publication_year" => "2010",
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)->assertJson([
            "errors" => [
                [
                    "title" => "Validation Error",
                    "details" => "The selected data.type is invalid.",
                    "source" => [
                        "pointer" => "/data/type"
                    ]
                ]
            ]
        ]);

        $this->assertDatabaseMissing('books', [
            "id" => "1",
            "title" => "James Bond",
            "description" => "A book by james bond",
            "publication_year" => "2010",
        ]);
    }


    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     * @
     */
    public function it_validates_that_type_attributes_has_been_given_when_updating_a_book()
    {

        $book = factory(Book::class)->create();
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $this->patchJson('api/v1/books/1', [
            "data" => [
                "id" => "1",
                "type" => "books",
                "attributes" => "",
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)->assertJson([
            "errors" => [
                [
                    "title" => "Validation Error",
                    "details" => "The data.attributes field is required.",
                    "source" => [
                        "pointer" => "/data/attributes"
                    ]
                ]
            ]
        ]);

        $this->assertDatabaseMissing('books', [
            "id" => "1",
            "title" => "James Bond",
            "description" => "A book by james bond",
            "publication_year" => "2010",
        ]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     * @
     */
    public function it_validates_that_attributes_is_an_object_when_updating_a_book()
    {

        $book = factory(Book::class)->create();
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $this->patchJson('api/v1/books/1', [
            "data" => [
                "id" => "1",
                "type" => "books",
                "attributes" => "not an object",
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)->assertJson([
            "errors" => [
                [
                    "title" => "Validation Error",
                    "details" => "The data.attributes must be an array.",
                    "source" => [
                        "pointer" => "/data/attributes"
                    ]
                ]
            ]
        ]);

        $this->assertDatabaseMissing('books', [
            "id" => "1",
            "title" => "James Bond",
            "description" => "A book by james bond",
            "publication_year" => "2010",
        ]);
    }


    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     * @
     */
    public function it_validates_that_title_is_a_string_when_updating_a_book()
    {
        $book = factory(Book::class)->create();
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $this->patchJson('api/v1/books/1', [
            "data" => [
                "id" => "1",
                "type" => "books",
                "attributes" => [
                    "title" => 122,
                    "description" => "A book by james bond",
                    "publication_year" => "2010",
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)->assertJson([
            "errors" => [
                [
                    "title" => "Validation Error",
                    "details" => "The data.attributes.title must be a string.",
                    "source" => [
                        "pointer" => "/data/attributes/title"
                    ]
                ]
            ]
        ]);

        $this->assertDatabaseMissing('books', [
            "id" => "1",
            "title" => "James Bond",
            "description" => "A book by james bond",
            "publication_year" => "2010",
        ]);
    }

     /**
     * A basic feature test example.
     *
     * @return void
     * @test
     * @
     */
    public function it_validates_that_description_is_a_string_when_updating_a_book()
    {
        $book = factory(Book::class)->create();
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $this->patchJson('api/v1/books/1', [
            "data" => [
                "id" => "1",
                "type" => "books",
                "attributes" => [
                    "title" => "James Bond",
                    "description" => 1222,
                    "publication_year" => "2010",
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)->assertJson([
            "errors" => [
                [
                    "title" => "Validation Error",
                    "details" => "The data.attributes.description must be a string.",
                    "source" => [
                        "pointer" => "/data/attributes/description"
                    ]
                ]
            ]
        ]);

        $this->assertDatabaseMissing('books', [
            "id" => "1",
            "title" => "James Bond",
            "description" => "A book by james bond",
            "publication_year" => "2010",
        ]);
    }

     /**
     * A basic feature test example.
     *
     * @return void
     * @test
     * @
     */
    public function it_validates_that_publication_year_is_a_string_when_updating_a_book()
    {
        $book = factory(Book::class)->create();
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $this->patchJson('api/v1/books/1', [
            "data" => [
                "id" => "1",
                "type" => "books",
                "attributes" => [
                    "title" => "James Bond",
                    "description" => "A book by james bond",
                    "publication_year" => 2010,
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)->assertJson([
            "errors" => [
                [
                    "title" => "Validation Error",
                    "details" => "The data.attributes.publication year must be a string.",
                    "source" => [
                        "pointer" => "/data/attributes/publication_year"
                    ]
                ]
            ]
        ]);

        $this->assertDatabaseMissing('books', [
            "id" => "1",
            "title" => "James Bond",
            "description" => "A book by james bond",
            "publication_year" => "2010",
        ]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     * @
     */
    public function it_can_delete_a_book_from_a_resource_object()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $book = factory(Book::class)->create();
        $this->delete('api/v1/books/1', [], [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ])->assertStatus(204);
        //checking if data was deleted
        $this->assertDatabaseMissing('authors', [
            'id' => '1',
            'title' => $book->title,
        ]);
    }
}
