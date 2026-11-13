<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Menambah kolom Study List (modul Naufal) ke tabel courses yang dibuat modul notes.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->foreignId('semester_id')->nullable()->constrained('semesters')->nullOnDelete();
            $table->unsignedTinyInteger('sks')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('semester_id');
            $table->dropColumn('sks');
        });
    }
};
