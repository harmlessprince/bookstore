<?php

namespace App\Http\Controllers;

use App\Book;
use App\Http\Resources\AuthorsIdentifierResource;
use Illuminate\Http\Request;

class BooksAuthorsRelationshipsController extends Controller
{
    //
    public function index(Book $book)
    {
        return AuthorsIdentifierResource::collection($book->authors);
    }

      //
      public function update(Request $request,Book $book)
      {
          $ids = $request->input('data.*.id');
          $book->authors()->sync($ids);

        //   dd($book->authors);
          return response(null, 204);
      }
}
