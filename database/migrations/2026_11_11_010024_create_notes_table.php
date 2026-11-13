<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Skema notes sudah ada pada database lama, jangan dibuat ulang.
        if (Schema::hasTable('notes')) {
            return;
        }

        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->string('title', 255)->nullable(); // fallback ke file_name di logika aplikasi jika null
            $table->string('course_name', 120);
            $table->string('lecturer_name', 120)->nullable();
            $table->string('category', 50)->nullable();
            $table->json('tags')->nullable();
            $table->text('description')->nullable();
            $table->string('file_path', 255);
            $table->string('file_name', 255);
            $table->unsignedInteger('file_size');
            $table->string('file_type', 10);
            $table->unsignedInteger('page_count')->nullable();
            $table->boolean('is_public')->default(true);
            $table->unsignedInteger('downloads_count')->default(0);
            $table->unsignedInteger('view_count')->default(0);
            $table->decimal('avg_rating', 2, 1)->default(0.0);
            $table->unsignedInteger('rating_count')->default(0);
            $table->enum('status', ['active', 'deleted'])->default('active');
            $table->timestamps();

            // Indexing untuk optimalisasi query katalog & filter
            $table->index(['user_id', 'status'], 'idx_notes_user_status');
            $table->index(['is_public', 'status', 'created_at'], 'idx_notes_public_created');
            $table->index('course_id', 'idx_notes_course');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};