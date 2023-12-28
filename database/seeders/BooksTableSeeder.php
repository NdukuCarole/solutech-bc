<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Book;

class BooksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Book::create([
            'name' => 'Sample Book 1',
            'publisher' => 'Sample Publisher 1',
            'isbn' => '1234567890',
            'category' => 'Fiction',
            'sub_category' => 'Adventure',
            'pages' => 300,
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'image' => 'sample_image.jpg',
            'added_by' => 1, 
            'deleted_at' => null, // or 'deleted_at' => now(),
        ]);
    }
}
