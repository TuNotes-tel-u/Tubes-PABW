<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();
            $table->string('kode_mk', 10);
            $table->string('nama_mk', 100);
            $table->unsignedTinyInteger('sks');
            $table->string('dosen', 100)->nullable();
            $table->timestamps();

            // satu kode mata kuliah hanya boleh sekali di tiap semester
            $table->unique(['semester_id', 'kode_mk']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
