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
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->foreignId('rack_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title', 255);
            $table->string('author', 255);
            $table->string('isbn', 20)->unique();
            $table->string('publisher', 255)->nullable();
            $table->year('year')->nullable();
            $table->integer('stock')->default(1);
            $table->integer('available')->default(1);
            $table->string('cover_image', 255)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
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
