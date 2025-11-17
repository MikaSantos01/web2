<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Publisher;
use App\Models\Author;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;


class BookController extends Controller
{
 // Formulário com input de ID
    public function createWithId()
    {
        return view('books.create-id');
    }

    // Salvar livro com input de ID
    public function storeWithId(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'publisher_id' => 'required|exists:publishers,id',
            'author_id' => 'required|exists:authors,id',
            'category_id' => 'required|exists:categories,id',

        ]);

        Book::create($request->all());

        return redirect()->route('books.index')->with('success', 'Livro criado com sucesso.');
    }

    // Formulário com input select
    public function createWithSelect()
    {
        $publishers = Publisher::all();
        $authors = Author::all();
        $categories = Category::all();

        return view('books.create-select', compact('publishers', 'authors', 'categories'));
    }

    // Salvar livro com input select
    public function storeWithSelect(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'publisher_id' => 'required|exists:publishers,id',
        'author_id' => 'required|exists:authors,id',
        'category_id' => 'required|exists:categories,id',
        'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    ]);

    $data = $request->all(); // pega todos os dados do request

    if ($request->hasFile('cover_image')) {
        $data['cover_image'] = $request->file('cover_image')->store('books', 'public');
    } else {
        $data['cover_image'] = null; // ou 'books/default.jpg'
    }

    Book::create($data); // salva o livro com todos os dados, incluindo cover_image

    return redirect()->route('books.index')->with('success', 'Livro criado com sucesso.');
}


    
        public function edit(Book $book)
    {
        $publishers = Publisher::all();
        $authors = Author::all();
        $categories = Category::all();

        return view('books.edit', compact('book', 'publishers', 'authors', 'categories'));
    }

            public function update(Request $request, Book $book)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'publisher_id' => 'required|exists:publishers,id',
            'author_id' => 'required|exists:authors,id',
            'category_id' => 'required|exists:categories,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Atualiza campos normais
        $book->title = $request->title;
        $book->publisher_id = $request->publisher_id;
        $book->author_id = $request->author_id;
        $book->category_id = $request->category_id;

        // Atualiza a capa apenas se houver upload
        if ($request->hasFile('cover_image')) {
            // Apaga antiga se existir
            if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
                Storage::disk('public')->delete($book->cover_image);
            }

            // Salva nova
            $book->cover_image = $request->file('cover_image')->store('books', 'public');
        }

        $book->save();

        return redirect()->route('books.show', $book)
                        ->with('success', 'Livro atualizado com sucesso.');
    }



        public function index()
    {
        // Carregar os livros com autores usando eager loading e paginação
        $books = Book::with('author')->paginate(20);

        return view('books.index', compact('books'));

    }

        public function show(Book $book)
    {
        // Carregando autor, editora e categoria do livro com eager loading
        $book->load(['author', 'publisher', 'category']);
        $users = User::all();

        return view('books.show', compact('book','users'));

    }

        public function destroy(Book $book)
    {
        if ($book->cover_image &&
            Storage::disk('public')->exists($book->cover_image)) 
        {
            Storage::disk('public')->delete($book->cover_image);
        }

        $book->delete();

        return redirect()->route('books.index')->with('success', 'Livro excluído com sucesso.');
    }

}
