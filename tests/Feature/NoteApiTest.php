<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteApiTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()->create();
    }

    private function note(User $user, array $overrides = []): Note
    {
        return Note::create(array_merge([
            'user_id' => $user->id,
            'course_name' => 'Algoritma dan Struktur Data',
            'lecturer_name' => 'Dr. Ahmad',
            'title' => 'Catatan Sorting',
            'category' => 'Kuliah',
            'tags' => ['sorting', 'algoritma'],
            'description' => 'Ringkasan algoritma sorting.',
            'file_path' => '/uploads/notes/sample.pdf',
            'file_name' => 'sample.pdf',
            'file_size' => 1024,
            'file_type' => 'pdf',
            'is_public' => true,
        ], $overrides));
    }

    public function test_index_mengembalikan_json_dan_bisa_filter(): void
    {
        $user = $this->user();
        $this->note($user);
        $this->note($user, ['title' => 'Catatan Privat', 'is_public' => false, 'category' => 'Tugas']);

        $response = $this->getJson('/api/notes?category=Kuliah&search=Sorting');

        $response->assertOk()
            ->assertHeader('content-type', 'application/json')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Catatan Sorting');
    }

    public function test_show_mengembalikan_404_json_untuk_id_tidak_ada(): void
    {
        $response = $this->getJson('/api/notes/999');

        $response->assertNotFound()
            ->assertHeader('content-type', 'application/json')
            ->assertJsonPath('status', 'error');
    }

    public function test_store_mengembalikan_422_dengan_error_per_field(): void
    {
        $response = $this->postJson('/api/notes', [
            'title' => 'Tanpa course name',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['course_name', 'file_path', 'file_name', 'file_size', 'file_type']);
    }

    public function test_store_menyimpan_note_dan_mengembalikan_201_dengan_location(): void
    {
        $this->user();

        $response = $this->postJson('/api/notes', [
            'title' => 'Sorting',
            'course_name' => 'Algoritma',
            'lecturer_name' => 'Dr. Ahmad',
            'category' => 'Kuliah',
            'tags' => ['sorting', 'algoritma'],
            'description' => 'Catatan lengkap.',
            'file_path' => '/uploads/notes/sample.pdf',
            'file_name' => 'sample.pdf',
            'file_size' => 1024,
            'file_type' => 'pdf',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.title', 'Sorting')
            ->assertJsonPath('data.display_title', 'Sorting')
            ->assertHeader('Location');

        $this->assertDatabaseHas('notes', [
            'title' => 'Sorting',
            'category' => 'Kuliah',
            'file_type' => 'pdf',
        ]);
    }

    public function test_patch_parsial_tidak_mengosongkan_field_lain(): void
    {
        $user = $this->user();
        $note = $this->note($user);

        $response = $this->patchJson('/api/notes/' . $note->id, [
            'title' => 'Sorting Versi Baru',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.title', 'Sorting Versi Baru')
            ->assertJsonPath('data.course_name', 'Algoritma dan Struktur Data')
            ->assertJsonPath('data.lecturer_name', 'Dr. Ahmad');

        $this->assertDatabaseHas('notes', [
            'id' => $note->id,
            'title' => 'Sorting Versi Baru',
            'course_name' => 'Algoritma dan Struktur Data',
        ]);
    }

    public function test_delete_mengembalikan_204_dan_baris_hilang(): void
    {
        $user = $this->user();
        $note = $this->note($user);

        $response = $this->deleteJson('/api/notes/' . $note->id);

        $response->assertNoContent();
        $this->assertDatabaseMissing('notes', ['id' => $note->id]);
    }

    public function test_update_dan_delete_mengembalikan_404_untuk_id_tidak_ada(): void
    {
        $this->patchJson('/api/notes/999', ['title' => 'X'])
            ->assertNotFound()
            ->assertJsonPath('status', 'error');

        $this->deleteJson('/api/notes/999')
            ->assertNotFound()
            ->assertJsonPath('status', 'error');
    }
}
