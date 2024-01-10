<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookLoan extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'book_id', 'due_date', 'loan_date', 'status', 'return_date', 'extended', 'extension_date', 'penalty_status', 'penalty_amount'];
}
