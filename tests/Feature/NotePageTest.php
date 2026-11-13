<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_semua_halaman_notes_dapat_dirender_untuk_user_login(): void
    {
        $user = User::factory()->create();

        $note = Note::create([
            'user_id' => $user->id,
            'course_name' => 'Algoritma',
            'lecturer_name' => 'Dr. Ahmad',
            'title' => 'Catatan Sorting',
            'category' => 'Kuliah',
            'file_path' => '/uploads/notes/sample.pdf',
            'file_name' => 'sample.pdf',
            'file_size' => 1024,
            'file_type' => 'pdf',
            'is_public' => true,
        ]);

        $this->actingAs($user)->get('/dashboard')
            ->assertOk()
            ->assertSee('Catatan Sorting');

        $this->actingAs($user)->get('/notes')
            ->assertOk()
            ->assertSee('My Notes');

        $this->actingAs($user)->get('/notes/upload')
            ->assertOk()
            ->assertSee('Upload Your Notes');

        $this->actingAs($user)->get('/notes/category/Kuliah')
            ->assertOk()
            ->assertSee('Catatan Sorting');

        $this->actingAs($user)->get('/notes/' . $note->id . '/edit')
            ->assertOk()
            ->assertSee('Update Notes');
    }

    public function test_guest_dialihkan_ke_halaman_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }
}
