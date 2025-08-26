<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrow;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BorrowController extends Controller
{
    public function borrow(Request $r, Book $book)
    {
        $user = $r->user();

        if ($book->available_copies < 1) {
            return response()->json(['message' => 'No copies available'], 422);
        }

        $exists = Borrow::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->whereNull('returned_at')
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Already borrowed this book'], 422);
        }

        $borrow = Borrow::create([
            'user_id'    => $user->id,
            'book_id'    => $book->id,
            'borrowed_at'=> Carbon::now(),
            'due_at'     => Carbon::now()->addDays(14),
        ]);

        $book->decrement('available_copies');

        return response()->json([
            'message' => 'Book borrowed successfully',
            'borrow'  => $borrow->load('book'),
        ], 201);
    }

    public function returnBook(Request $r, Borrow $borrow)
    {
        $user = $r->user();

        if ($borrow->user_id !== $user->id && !$user->is_admin) {
            return response()->json(['message' => 'Not allowed'], 403);
        }

        if ($borrow->returned_at) {
            return response()->json(['message' => 'Already returned'], 422);
        }

        $borrow->returned_at = now();
        $borrow->save();

        $borrow->book()->update([
            'available_copies' => DB::raw('available_copies + 1')
        ]);

        return response()->json([
            'message' => 'Book returned successfully',
            'borrow'  => $borrow->fresh()->load('book'),
        ]);
    }

    public function myBorrows(Request $r)
    {
        $borrows = Borrow::with('book')
            ->where('user_id', $r->user()->id)
            ->orderByDesc('id')
            ->paginate(10);

        return response()->json([
            'borrows' => $borrows,
        ]);
    }

    public function all(Request $r)
    {
        if (!$r->user()->is_admin) {
            return response()->json(['message' => 'Admin only'], 403);
        }

        $borrows = Borrow::with(['book','user'])
            ->orderByDesc('id')
            ->paginate(15);

        return response()->json([
            'borrows' => $borrows,
        ]);
    }
}
