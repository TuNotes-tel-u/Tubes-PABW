<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel courses dibuat lebih dulu karena tabel notes memiliki
     * foreign key course_id yang mengarah ke courses.id.
     */
    public function up(): void
    {
        // Tabel courses bisa saja sudah dibuat manual pada database lama.
        if (Schema::hasTable('courses')) {
            return;
        }

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->nullable()->unique();
            $table->string('name', 120);
            $table->string('lecturer_name', 120)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
