<?php

namespace Tests\Feature;

use App\Author;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class AuthorsTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     * 
     */
    public function it_returns_an_author_as_a_resource_object()
    {
        $author = factory(Author::class)->create();
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $this->getJson('api/v1/authors/1', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200)->assertJson([
            "data" => [
                'id' => (string) $author->id,
                'type' => 'authors',
                'attributes' => [
                    'name' => $author->name,
                    'created_at' => $author->created_at,
                    'updated_at' => $author->updated_at,
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
     */
    public function it_returns_all_authors_as_a_collection_of_resource_objects()
    {

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $author = factory(Author::class, 2)->create();

        $this->getJson('api/v1/authors', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200)->assertJson([
            "data" => [
                [
                    'id' => (string) $author[0]->id,
                    'type' => 'authors',
                    'attributes' => [
                        'name' => $author[0]->name,
                        'created_at' => $author[0]->created_at,
                        'updated_at' => $author[0]->updated_at,
                    ]
                ],
                [
                    'id' => (string) $author[1]->id,
                    'type' => 'authors',
                    'attributes' => [
                        'name' => $author[1]->name,
                        'created_at' => $author[1]->created_at,
                        'updated_at' => $author[1]->updated_at,
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
    public function it_can_sort_authors_by_name_through_a_sort_query_parameter()
    {

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $authors = collect([
            'Bertram',
            'Claus',
            'Anna'
        ])->map(function ($name) {
            return factory(Author::class)->create([
                'name' => $name
            ]);
        });
        // echo $authors[0];
        $this->getJson('/api/v1/authors?sort=name', [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200)->assertJson([
            "data" => [
                [
                    'id' => '3',
                    'type' => 'authors',
                    'attributes' => [
                        'name' => 'Anna',
                        'created_at' => $authors[2]->created_at,
                        'updated_at' => $authors[2]->updated_at,
                    ]
                ],
                [
                    'id' => '1',
                    'type' => 'authors',
                    'attributes' => [
                        'name' => 'Bertram',
                        'created_at' => $authors[0]->created_at,
                        'updated_at' => $authors[0]->updated_at,
                    ]
                ],
                [
                    'id' => '2',
                    'type' => 'authors',
                    'attributes' => [
                        'name' => 'Claus',
                        'created_at' => $authors[1]->created_at,
                        'updated_at' => $authors[1]->updated_at,
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
     */
    public function it_can_create_an_author_from_a_resource_object()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        // $author = factory(Author::class)->create();

        $this->postJson('api/v1/authors', [
            "data" => [
                "type" => "authors",
                "attributes" => [
                    "name" => "Author Test"
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(201)->assertJson([
            "data" => [
                'id' => "1",
                'type' => 'authors',
                'attributes' => [
                    'name' => "Author Test",
                    'created_at' => now()->format('Y-m-d H:m:i'),
                    'updated_at' => now()->format('Y-m-d H:m:i'),
                ]
            ]
        ])->assertHeader('Location', url('/api/v1/authors/1'));
        //checking if data was saved
        $this->assertDatabaseHas('authors', [
            'id' => '1',
            'name' => 'Author Test'
        ]);
    }


    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     * 
     */
    public function it_validates_that_type_member_is_given_when_creating_an_author()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        // $author = factory(Author::class)->create();

        $this->postJson('api/v1/authors', [
            "data" => [
                "type" => "",
                "attributes" => [
                    "name" => "Author Test"
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
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

        $this->assertDatabaseMissing('authors', [
            'id' => '1',
            'name' => 'Author Test',
        ]);
    }


    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     *
     */
    public function it_validates_that_type_member_has_the_value_of_authors_when_creating_an_author()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        // $author = factory(Author::class)->create();

        $this->postJson('api/v1/authors', [
            "data" => [
                "type" => "author",
                "attributes" => [
                    "name" => "Author Test"
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
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

        $this->assertDatabaseMissing('authors', [
            'id' => '1',
            'name' => 'Author Test',
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
    public function it_validates_that_attributes_member_has_been_given_when_creating_an_author()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        // $author = factory(Author::class)->create();

        $this->postJson('api/v1/authors', [
            "data" => [
                "type" => "authors",
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
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

        $this->assertDatabaseMissing('authors', [
            'id' => '1',
            'name' => 'Author Test',
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
    public function it_validates_that_attributes_member_is_an_object_when_creating_an_author()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        // $author = factory(Author::class)->create();

        $this->postJson('api/v1/authors', [
            "data" => [
                "type" => "authors",
                "attributes" => "not an object"
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
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

        $this->assertDatabaseMissing('authors', [
            'id' => '1',
            'name' => 'Author Test',
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
    public function it_validates_that_a_name_attribute_is_given_when_creating_an_author()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $this->postJson('/api/v1/authors', [
            'data' => [
                'type' => 'authors',
                'attributes' => [
                    'name' => '',
                ],
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
                        'details' => 'The data.attributes.name field is required.',
                        'source' => [
                            'pointer' => '/data/attributes/name',
                        ]
                    ]
                ]
            ]);
        $this->assertDatabaseMissing('authors', [
            'id' => 1,
            'name' => 'John Doe'
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
    public function it_validates_that_a_name_attribute_is_string_when_creating_an_author()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $this->postJson('/api/v1/authors', [
            'data' => [
                'type' => 'authors',
                'attributes' => [
                    'name' => 45,
                ],
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
                        'details' => 'The data.attributes.name must be a string.',
                        'source' => [
                            'pointer' => '/data/attributes/name',
                        ]
                    ]
                ]
            ]);
        $this->assertDatabaseMissing('authors', [
            'id' => 1,
            'name' => 'John Doe'
        ]);
    }


    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     * 
     */
    public function it_can_update_an_author_from_a_resource_object()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $author = factory(Author::class)->create();
        $creationTimestamp = now();
        sleep(1);
        $this->patchJson('api/v1/authors/1', [
            "data" => [
                "id" => "1",
                "type" => "authors",
                "attributes" => [
                    "name" => "Author Test"
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(200)->assertJson([
            "data" => [
                'id' => "1",
                'type' => 'authors',
                'attributes' => [
                    'name' => "Author Test",
                    'created_at' => $creationTimestamp->format('Y-m-d H:m:i'),
                    'updated_at' => now()->format('Y-m-d H:m:i'),
                ]
            ]
        ]);
        //checking if data was saved
        $this->assertDatabaseHas('authors', [
            'id' => '1',
            'name' => 'Author Test'
        ]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     * 
     */
    public function it_validates_that_an_id_member_is_given_when_updating_an_author()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $author = factory(Author::class)->create();
        $this->patchJson('api/v1/authors/1', [
            "data" => [
                "type" => "authors",
                "attributes" => [
                    "name" => "Author Test"
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
                        'details' => 'The data.id field is required.',
                        'source' => [
                            'pointer' => '/data/id',
                        ]
                    ]
                ]
            ]);

        $this->assertDatabaseMissing('authors', [
            'id' => '1',
            'name' => 'Author Test',
        ]);
    }


    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     * 
     */
    public function it_validates_that_an_id_member_is_a_string_when_updating_an_author()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $author = factory(Author::class)->create();
        $this->patchJson('api/v1/authors/1', [
            "data" => [
                "id" => 2,
                "type" => "authors",
                "attributes" => [
                    "name" => "Author Test"
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
                        'details' => 'The data.id must be a string.',
                        'source' => [
                            'pointer' => '/data/id',
                        ]
                    ]
                ]
            ]);

        $this->assertDatabaseMissing('authors', [
            'id' => '1',
            'name' => 'Author Test',
        ]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     * 
     */
    public function it_validates_that_type_member_is_given_when_updating_an_author()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $author = factory(Author::class)->create();
        $this->patchJson('api/v1/authors/1', [
            "data" => [
                "id" => "1",
                "type" => "",
                "attributes" => [
                    "name" => "Author Test"
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)
            ->assertJson([
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

        $this->assertDatabaseMissing('authors', [
            'id' => '1',
            'name' => 'Author Test',
        ]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     * 
     */
    public function it_validates_that_type_member_has_the_value_of_authors_when_updating_an_author()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $author = factory(Author::class)->create();
        $this->patchJson('api/v1/authors/1', [
            "data" => [
                "id" => "1",
                "type" => "author",
                "attributes" => [
                    "name" => "Author Test"
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)
            ->assertJson([
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

        $this->assertDatabaseMissing('authors', [
            'id' => '1',
            'name' => 'Author Test',
        ]);
    }


    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     * 
     */
    public function it_validates_that_type_attributes_has_been_given_when_updating_an_author()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $author = factory(Author::class)->create();
        $this->patchJson('api/v1/authors/1', [
            "data" => [
                "id" => "1",
                "type" => "authors",
                "attributes" => ""
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)
            ->assertJson([
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

        $this->assertDatabaseMissing('authors', [
            'id' => '1',
            'name' => 'Author Test',
        ]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     * 
     */
    public function it_validates_that_attributes_is_an_object_when_updating_an_author()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $author = factory(Author::class)->create();
        $this->patchJson('api/v1/authors/1', [
            "data" => [
                "id" => "1",
                "type" => "authors",
                "attributes" => "not an object"
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)
            ->assertJson([
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

        $this->assertDatabaseMissing('authors', [
            'id' => '1',
            'name' => 'Author Test',
        ]);
    }


    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     * 
     */
    public function it_validates_that_name_is_a_string_when_updating_an_author()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $author = factory(Author::class)->create();
        $this->patchJson('api/v1/authors/1', [
            "data" => [
                "id" => "1",
                "type" => "authors",
                "attributes" => [
                    "name" => 2,
                ],
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
                        'details' => 'The data.attributes.name must be a string.',
                        'source' => [
                            'pointer' => '/data/attributes/name',
                        ]
                    ]
                ]
            ]);

        $this->assertDatabaseMissing('authors', [
            'id' => '1',
            'name' => 'Author Test',
        ]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     * 
     */
    public function it_can_delete_an_author_from_a_resource_object()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $author = factory(Author::class)->create();
        $this->delete('api/v1/authors/1', [], [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ])->assertStatus(204);
        //checking if data was deleted
        $this->assertDatabaseMissing('authors', [
            'id' => '1',
            'name' => $author->name,
        ]);
    }
}
