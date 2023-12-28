<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BookLoan;

class BookLoansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BookLoan::create([
            'user_id' => 1, // Assuming user_id 1 is borrowing the book
            'book_id' => 1, // Assuming book_id 1 is being borrowed
            'loan_date' => now(),
            'due_date' => now()->addDays(14), // Due date is 14 days from now
            'return_date' => null, // Book has not been returned yet
            'extended' => 'No',
            'extension_date' => null,
            'penalty_amount' => 0,
            'penalty_status' => 'Not Applicable',
            'status' => 'Borrowed',
            'added_by' => 1, // Assuming user_id 1 added this loan
        ]);
    }
}
