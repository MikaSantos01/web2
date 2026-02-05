<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class BooksControllerApi extends Controller
{

    public function index()
    {
        return response()->json(Book::all());
    }

    public function store(Request $request)
    {
        $book = Book::create($request->all());

        return response()->json($book, 201);
    }

    public function show(string $id)
    {
        $book = Book::find($id);

        if(!$book){
            return response()->json(['message' => 'Livro não encontrado'], 404);
        }

        return response()->json($book);
    }

    public function update(Request $request, string $id)
    {
        $book = Book::find($id);

        if(!$book) {
            return response()->json(['message' => 'Livro não encontrado'], 404);
        }

        $book->update($request->all());

        return response()->json($book);
    }

    public function destroy(string $id)
    {
        $book = Book::find($id);

        if(!$book){
            return response()->json(['message' => 'Livro não encontrado'], 404);
        }

        $book->delete();

        return response()->json(['message' => 'Livro removido']);
    }
}
