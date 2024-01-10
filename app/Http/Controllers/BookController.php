<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\BookLoan;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    //

    public function addBook(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'publisher' => 'required|string',
            'isbn' => 'required|string',
            'category' => 'required|string',
            'sub_category' => 'required|string',
            'description' => 'required|string',
            'pages' => 'required|string',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'added_by' => 'required|string'

        ]);

        $bookData = $request->except('image');

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            Storage::disk('public')->put($imageName, file_get_contents($image));
            $bookData['image'] = $imageName;
        }

        $book = Book::create($bookData);

        return response()->json(['status_code' => 1000, 'message' => 'Book created successfully', 'book' => $book]);
    }

    public function updateBook(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'publisher' => 'required|string',
            'isbn' => 'required|string',
            'category' => 'required|string',
            'sub_category' => 'required|string',
            'description' => 'required|string',
            'pages' => 'required|string',
            'added_by' => 'required|string',
        ]);

        $book = Book::findOrFail($id);

        $bookData = $request->except('image');

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            Storage::disk('public')->put($imageName, file_get_contents($image));
            $bookData['image'] = $imageName;
        }

        $book->update($bookData);

        return response()->json(['status_code' => 1000, 'message' => 'Book updated successfully', 'book' => $book]);
    }



    public function returnBook(Request $request)
    {
        $this->validate($request, [
            'loan_id' => 'required|exists:book_loans,id',
            'book_id' => 'required|exists:books,id',
        ]);

        $book = Book::find($request['book_id']);

        $loan = BookLoan::find($request->input('loan_id'));
        if (!$loan) {
            return response()->json(['error' => 'Loan not found.'], 404);
        }

        $loan->update(['status' => 'returned', 'return_date' => now()]);
        $book->update(['status' => 'Available']);

        return response()->json(['status_code' => 1000, 'message' => 'Book returned successfully.'], 200);
    }

    public function viewLoans()
    {

        $loans = BookLoan::all();

        return response()->json(['status_code' => 1000, 'message' => 'Loans received successfully.', 'data' => $loans], 200);
    }

    public function borrowBook(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|exists:users,id',
            'book_id' => 'required|exists:books,id',
            'due_date' => 'required|date',
        ]);

        $requestData = $request->json()->all();

        $book = Book::find($requestData['book_id']);
        if (!$book) {
            return response()->json(['error' => 'Book not found.'], 404);
        }
        $book->update(['status' => 'borrowed']);


        $bookLoan = BookLoan::create([
            'user_id' => $requestData['user_id'],
            'book_id' => $requestData['book_id'],
            'due_date' => $requestData['due_date'],
            'loan_date' => now()->toDateString(),
            'status' => 'borrowed',
        ]);

        return response()->json(['status_code' => 1000, 'message' => 'Book loaned successfully.', 'book_loan' => $bookLoan], 200);
    }



    public function approveLoan(Request $request)
    {
        $this->validate($request, [
            'loan_id' => 'required|exists:book_loans,id',
        ]);

        $loan = BookLoan::find($request->input('loan_id'));
        if (!$loan) {
            return response()->json(['error' => 'Loan not found.'], 404);
        }

        $loan->update(['status' => 'approved']);
        $book = Book::find($loan['book_id']);
        if (!$book) {
            return response()->json(['error' => 'Book not found.'], 404);
        }
        $book->update(['status' => 'approved']);

        return response()->json(['status_code' => 1000, 'message' => 'Loan approved and book issued successfully.'], 200);
    }

    public function declineLoan(Request $request)
    {
        $this->validate($request, [
            'loan_id' => 'required|exists:book_loans,id',
        ]);

        $loan = BookLoan::find($request->input('loan_id'));
        if (!$loan) {
            return response()->json(['error' => 'Loan not found.'], 404);
        }

        $loan->update(['status' => 'declined']);


        $book = Book::find($loan['book_id']);
        if (!$book) {
            return response()->json(['error' => 'Book not found.'], 404);
        }
        $book->update(['status' => 'Available']);

        return response()->json(['status_code' => 1000, 'message' => 'Loan declined successfully.'], 200);
    }

    public function extendLoan(Request $request)
    {
        $this->validate($request, [
            'loan_id' => 'required|exists:book_loans,id',
        ]);



        $loan = BookLoan::find($request->input('loan_id'));
        if (!$loan) {
            return response()->json(['error' => 'Loan not found.'], 404);
        }

     


        if ($loan->status !== 'approved' || $loan->returned_at !== null) {
            return response()->json(['error' => 'Invalid loan status for extension.'], 400);
        }


        $currentDate = now();
        $dueDate = $loan->due_date;

        $extensionDays =  7;

        if ($currentDate->lte($dueDate)) {
            return response()->json(['error' => 'The loan is not eligible for extension.'], 400);
        }

        // Extend the due date
        $extendedDueDate = $currentDate->addDays($extensionDays);
        $loan->update(['due_date' => $extendedDueDate, 'extended' => 'yes', 'extension_date' => now()]);

        return response()->json(['status_code' => 1000, 'message' => 'Loan extended successfully.'], 200);
    }

    public function receiveBook(Request $request)
    {
        $this->validate($request, [
            'loan_id' => 'required|exists:book_loans,id',
            'book_id' => 'required|exists:books,id',
        ]);

        $loan = BookLoan::find($request->input('loan_id'));
        if (!$loan) {
            return response()->json(['error' => 'Loan not found.'], 404);
        }


        if ($loan->status !== 'approved' || $loan->returned_at !== null) {
            return response()->json(['error' => 'Invalid loan status for book return.'], 400);
        }
        $loan->update(['status' => 'received', 'returned_at' => now(), 'penalty_status' => 'penalized', 'penalty_amount' => 1000]);

        $book = Book::find($request['book_id']);
        $book->update(['status' => 'Available']);

        $user = User::find($loan->user_id);
        if ($user && $loan->penalty_status === 'penalized') {
            $user->update(['has_penalty' => true]);
        }


        return response()->json(['status_code' => 1000, 'message' => 'Book received back successfully.'], 200);
    }

    public function getAllBooks()
    {

        $books = Book::all();
        foreach ($books as $book) {

            $book->image_url = asset('storage/' . $book->image);
        }

        return response()->json(['status_code' => 1000, 'message' => 'Success', 'data' => $books]);
    }

    public function deleteBook($id)
    {
        try {

            $book = Book::findOrFail($id);

            if ($book->image) {
                Storage::disk('public')->delete($book->image);
            }
            $book->delete();

            return response()->json([
                'status_code' => 1000,
                'message' => 'Book deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Error deleting book.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
