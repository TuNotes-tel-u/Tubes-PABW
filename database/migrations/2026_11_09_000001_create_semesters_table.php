<?php

// CATATAN: file ini hanya dipakai kalau proyek belum punya tabel semesters.
// Kalau tim sudah punya tabel semesters (dari bagian lain), hapus file ini dari proyek.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semesters');
    }
};
