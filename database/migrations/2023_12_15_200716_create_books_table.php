<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
           
            $table->id();
            $table->string('name', 200)->notnull();
            $table->string('publisher', 50)->notnull();
            $table->string('isbn', 50)->notnull();
            $table->string('category', 100)->notnull();
            $table->string('sub_category', 100)->notnull();
            $table->integer('pages')->notnull();
            $table->text('description')->notnull();
            $table->string('image', 200)->nullable();
            $table->integer('added_by')->notnull();
            $table->string('status',20)->default('Available');
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
