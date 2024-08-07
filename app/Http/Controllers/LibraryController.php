<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Book;
use App\Models\Borrow;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


/**
     * @OA\Post(
     *     path="/api/borrow",
     *     summary="Meminjam buku",
     *     tags={"Borrow"},
     *     operationId="borrowBook",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"code_member","code_book"},
     *             @OA\Property(property="code_member", type="string", example="M001"),
     *             @OA\Property(property="code_book", type="string", example="B001")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Buku berhasil dipinjam",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Buku berhasil dipinjam"),
     *             @OA\Property(property="book", type="object",
     *                 @OA\Property(property="title", type="string", example="Book Title"),
     *                 @OA\Property(property="author", type="string", example="Author Name"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Member atau buku tidak ditemukan"
     *     )
     * )
     */

class LibraryController extends Controller
{
    //Borrow Book
    public function borrowBook(Request $request)
    {
        $code_member = $request->input('code_member');
        $code_book = $request->input('code_book');

        $member = Member::where('code', $code_member)->first();
        $book = Book::where('code', $code_book)->first();

        if (!$member || !$book) {
            return response()->json(['message' => 'Member atau buku tidak ditemukan'], 404);
        }

        // Periksa apakah member sudah meminjam lebih dari 2 buku
        $jumlahBukuDipinjam  = Borrow::where('code_member', $member->code)
            ->whereNull('return_date')
            ->count();

        if ($jumlahBukuDipinjam >= 2) {
            return response()->json(['message' => 'Hei!!.. anda sudah meminjam 2 buku dan belum dikembalikan,tidak boleh meminjam lebih dari 2 buku'], 400);
        }


        $checkPinjam = Borrow::where('code_member', $member->code)
            ->where('code_book', $book->code)
            ->whereNull('return_date')
            ->first();

        if ($checkPinjam) {
            return response()->json(['message' => 'Buku sudah dipinjam dan belum dikembalikan'], 400);
        }

        //Validate member pinalty status
        if ($member->is_penalized == 1 && Carbon::now()->lt($member->penalty_end_date)) {
            return response()->json(['message' => 'Anda Dalam Masa Penalti'], 400);
        }

        // dd($book);

        //validate book availability
        if ($book->stock <= 0) {
            return response()->json(['message' => 'Buku tidak tersedia'], 400);
        }

        //Borrow the book

        Borrow::create([
            'code_member' => $member->code,
            'code_book' => $book->code,
            'borrow_date' => Carbon::now()
        ]);

        $book->decrement('stock');

        // dd($member); 


        return response()->json([
            'message' => 'Buku berhasil di pinjam',
            'book' => [
                'title' => $book->title, // Ganti dengan atribut yang sesuai
                'author' => $book->author, // Ganti dengan atribut yang sesuai
            ]

        ], 200);
    }


     /**
     * @OA\Post(
     *     path="/api/return",
     *     summary="Mengembalikan buku",
     *     tags={"Return"},
     *     operationId="returnBook",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"code_member","code_book","return_date"},
     *             @OA\Property(property="code_member", type="string", example="M001"),
     *             @OA\Property(property="code_book", type="string", example="B001"),
     *             @OA\Property(property="return_date", type="string", format="date", example="2024-08-07")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Buku berhasil dikembalikan",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Buku berhasil dikembalikan"),
     *             @OA\Property(property="book", type="object",
     *                 @OA\Property(property="title", type="string", example="Book Title"),
     *                 @OA\Property(property="author", type="string", example="Author Name")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Permintaan pengembalian tidak valid"
     *     )
     * )
     */

    //return book
    public function returnBook(Request $request)
    {
        // Validasi input untuk memastikan return_date ada di request
        $request->validate([
            'code_member' => 'required|string',
            'code_book' => 'required|string',
            'return_date' => 'required|date',
        ]);

        // Cari peminjaman yang cocok dengan code_member, code_book, dan yang belum dikembalikan
        $borrowedBook = Borrow::where('code_member', $request->code_member)
            ->where('code_book', $request->code_book)
            ->whereNull('return_date')
            ->first();

        if (!$borrowedBook) {
            return response()->json(['message' => 'Permintaan pengembalian tidak valid'], 400);
        }

        $borrowedBook->return_date = $request->return_date;

        // Hitung penalti jika durasi peminjaman lebih dari 7 hari
        $borrowDuration = Carbon::parse($borrowedBook->borrow_date)->diffInDays($request->return_date);
        if ($borrowDuration > 7) {
            $member = $borrowedBook->member;
            $member->is_penalized = true;
            $member->penalty_end_date =  Carbon::parse($request->return_date)->addDays(3);
            $member->save();
        }

        // Mengatur return_date dari request
        $borrowedBook->save();

        // Menambah stok buku yang dikembalikan
        $book = $borrowedBook->book;
        $book->increment('stock');

        return response()->json([
            'message' => 'Buku berhasil kembalikan',
            'book' => [
                'title' => $book->title, // Ganti dengan atribut yang sesuai
                'author' => $book->author, // Ganti dengan atribut yang sesuai
            ]

        ], 200);
    }


     /**
     * @OA\Get(
     *     path="/api/books",
     *     summary="Cek daftar buku",
     *     tags={"Books"},
     *     operationId="checkBooks",
     *     @OA\Response(
     *         response=200,
     *         description="Daftar buku berhasil diambil",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="code", type="string", example="B001"),
     *                 @OA\Property(property="title", type="string", example="Book Title"),
     *                 @OA\Property(property="author", type="string", example="Author Name"),
     *                 @OA\Property(property="stock", type="integer", example=10)
     *             )
     *         )
     *     )
     * )
     */

    //Check book
    public function checkBooks()
    {
        $books = Book::select('code', 'title', 'author', 'stock')->get();
        return response()->json($books, 200);
    }


         /**
     * @OA\Get(
     *     path="/api/members",
     *     summary="Cek Member",
     *     tags={"Members"},
     *     operationId="checkMembers",
     *     @OA\Response(
     *         response=200,
     *         description="Daftar memeber berhasil diambil",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="code", type="string", example="M001"),
     *                 @OA\Property(property="nama", type="string", example="Name Member"),
     *                 @OA\Property(property="borrowed_books_count", type="integer", example="1"),
     *             )
     *         )
     *     )
     * )
     */

    //Check Members
    public function checkMembers()
    {
        $members = Member::all();

        $memberBorrowCounts = [];

        foreach ($members as $member) {
            $jumlahBukuDipinjam = Borrow::where('code_member', $member->code)
                ->whereNull('return_date')
                ->count();


            $memberBorrowCounts[] = [
                'member_code' => $member->code,
                'member_name' => $member->name,
                'borrowed_books_count' => $jumlahBukuDipinjam
            ];
        }


        return response()->json($memberBorrowCounts, 200);
    }
}
