<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $book = Book::query();
        if($request->filled('q')){
            $book->where(function($x) use($request){
                $x->where('title','like','%'.$request->q.'%')
                    ->orWhere('author','like','%'.$request->q.'%')
                    ->orWhere('isbn','like','%'.$request->q.'%');
            });
        }

        return $book->orderBy('id','desc')->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BookRequest $request)
    {

        $book = Book::create([
            'title' => $request->title,
            'author' => $request->author,
            'isbn' =>$request->isbn,
            'total_copies'=>$request->total_copies,
            'available_copies'=>$request->total_copies,
        ]);

        return response()->json([
            'book'=>$book,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        return response()->json(['book' => $book]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BookRequest $request, Book $book)
    {
        $this->authorizeAdmin($request);
        if($request->has('total_copies')){
            $diff = $request->total_copies - $book->total_copies;
            $book->available_copies = max(0, $book->available_copies + $diff);
        }

        $book->fill($request->validated());
        $book->save();

        return response()->json(['message' => 'succsesful']);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BookRequest $request, Book $book)
    {
        $this->authorizeAdmin($request);
        $book->delete();
        return response()->noContent();
    }

    private function authorizeAdmin(BookRequest $r)
    {
        if (!$r->user() || !$r->user()->is_admin) {
            abort(403,'Admin only');
        }
    }
}
