<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Note;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class NoteController extends Controller
{
    /**
     * GET /api/notes
     *
     * Query string yang didukung:
     * - ?category=Kuliah  -> filter kategori
     * - ?search=algo      -> cari title / file_name / course_name / lecturer_name
     * - ?mine=1           -> hanya catatan milik user (butuh login atau ?user_id=)
     * - ?is_public=1|0    -> filter berdasarkan status publik
     * - ?sort=newest|popular|rating
     */
    public function index(Request $request): JsonResponse
    {
        $query = Note::query()->active();

        if ($request->boolean('mine')) {
            $userId = $request->user()?->id ?? $request->integer('user_id');

            if (! $userId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Parameter user_id wajib diisi untuk melihat catatan pribadi.',
                ], 422, [], JSON_UNESCAPED_SLASHES);
            }

            $query->ownedBy($userId);
        }

        if ($request->has('is_public')) {
            $query->where('is_public', $request->boolean('is_public'));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->query('category'));
        }

        if ($request->filled('search')) {
            $search = '%' . $request->query('search') . '%';

            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', $search)
                    ->orWhere('file_name', 'LIKE', $search)
                    ->orWhere('course_name', 'LIKE', $search)
                    ->orWhere('lecturer_name', 'LIKE', $search);
            });
        }

        match ($request->query('sort', 'newest')) {
            'popular' => $query->orderByDesc('downloads_count'),
            'rating' => $query->orderByDesc('avg_rating'),
            default => $query->orderByDesc('created_at'),
        };

        $notes = $query->orderByDesc('id')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar catatan berhasil diambil',
            'data' => $notes,
        ], 200, [], JSON_UNESCAPED_SLASHES);
    }

    /**
     * GET /api/notes/{id}
     * Route Model Binding: ID yang tidak ada otomatis menjadi 404 JSON.
     */
    public function show(Note $note): JsonResponse
    {
        if ($note->status !== 'active') {
            return response()->json([
                'status' => 'error',
                'message' => 'Catatan tidak ditemukan.',
            ], 404, [], JSON_UNESCAPED_SLASHES);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail catatan berhasil diambil',
            'data' => $note,
        ], 200, [], JSON_UNESCAPED_SLASHES);
    }

    /**
     * POST /api/notes
     *
     * Mendukung dua bentuk request:
     * 1. multipart/form-data dengan field "file" (dipakai halaman Upload Notes)
     * 2. application/json dengan file_path/file_name/file_size/file_type
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->rules());

        $userId = $request->user()?->id
            ?? ($validated['user_id'] ?? null)
            ?? User::query()->orderBy('id')->value('id');

        if (! $userId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada user yang dapat menjadi pemilik catatan.',
                'errors' => [
                    'user_id' => ['Buat user terlebih dahulu atau sertakan user_id.'],
                ],
            ], 422, [], JSON_UNESCAPED_SLASHES);
        }

        unset($validated['user_id'], $validated['file']);

        $payload = $this->withFileData($request, $validated);

        $payload['user_id'] = $userId;
        $payload['category'] = $payload['category'] ?? Note::DEFAULT_CATEGORIES[0];
        $payload['tags'] = $this->normalizeTags($payload['tags'] ?? null);
        $payload['is_public'] = $payload['is_public'] ?? true;
        $payload['status'] = 'active';

        $note = Note::create($payload);

        $this->rememberCategory($userId, $note->category);
        User::whereKey($userId)->increment('notes_count');

        return response()->json([
            'data' => $note,
        ], 201, [], JSON_UNESCAPED_SLASHES)
            ->header('Location', route('notes.show', $note));
    }

    /**
     * PUT/PATCH /api/notes/{id}
     * Semua field memakai "sometimes" sehingga PATCH parsial aman dan
     * field lain tidak ikut kosong.
     */
    public function update(Request $request, Note $note): JsonResponse
    {
        $validated = $request->validate($this->rules(true));

        unset($validated['user_id'], $validated['file']);

        $payload = $this->withFileData($request, $validated);

        if (array_key_exists('tags', $payload)) {
            $payload['tags'] = $this->normalizeTags($payload['tags']);
        }

        $note->update($payload);

        if ($note->category) {
            $this->rememberCategory($note->user_id, $note->category);
        }

        return response()->json([
            'data' => $note->fresh(),
        ], 200, [], JSON_UNESCAPED_SLASHES);
    }

    /**
     * DELETE /api/notes/{id}
     * Hard delete: baris benar-benar hilang dari tabel notes.
     */
    public function destroy(Note $note): Response
    {
        $userId = $note->user_id;

        $note->delete();

        User::whereKey($userId)
            ->where('notes_count', '>', 0)
            ->decrement('notes_count');

        return response()->noContent();
    }

    /**
     * Aturan validasi bersama untuk store & update.
     */
    private function rules(bool $partial = false): array
    {
        $required = $partial ? 'sometimes' : 'required';

        return [
            'user_id' => ['sometimes', 'integer', 'exists:users,id'],
            'title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'course_name' => [$required, 'string', 'max:120'],
            'lecturer_name' => ['sometimes', 'nullable', 'string', 'max:120'],
            'category' => ['sometimes', 'nullable', 'string', 'max:50'],
            'tags' => ['sometimes', 'nullable'],
            'description' => ['sometimes', 'nullable', 'string'],
            'is_public' => ['sometimes', 'boolean'],
            'page_count' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'course_id' => ['sometimes', 'nullable', 'integer', 'exists:courses,id'],

            // File upload langsung.
            'file' => [
                'sometimes',
                'file',
                'mimes:' . implode(',', Note::FILE_TYPES),
                'max:10240',
            ],

            // Alternatif JSON ketika file sudah tersimpan di tempat lain.
            'file_path' => [$partial ? 'sometimes' : 'required_without:file', 'string', 'max:255'],
            'file_name' => [$partial ? 'sometimes' : 'required_without:file', 'string', 'max:255'],
            'file_size' => [$partial ? 'sometimes' : 'required_without:file', 'integer', 'min:1'],
            'file_type' => [
                $partial ? 'sometimes' : 'required_without:file',
                'string',
                Rule::in(Note::FILE_TYPES),
            ],
        ];
    }

    /**
     * Menyimpan file upload dan mengembalikan atribut file_* yang siap
     * dimasukkan ke database. Jika tidak ada file, data JSON dipakai apa adanya.
     */
    private function withFileData(Request $request, array $payload): array
    {
        $file = $request->file('file');

        if (! $file) {
            if (isset($payload['file_type'])) {
                $payload['file_type'] = strtolower($payload['file_type']);
            }

            return $payload;
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $fileName = $file->getClientOriginalName();
        $fileSize = $file->getSize();
        $storedName = Str::uuid() . '.' . $extension;

        $directory = public_path('uploads/notes');

        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $file->move($directory, $storedName);

        return array_merge($payload, [
            'file_path' => '/uploads/notes/' . $storedName,
            'file_name' => $fileName,
            'file_size' => $fileSize,
            'file_type' => $extension,
        ]);
    }

    /**
     * Tags boleh dikirim sebagai array JSON atau string dipisah koma.
     */
    private function normalizeTags(mixed $tags): ?array
    {
        if ($tags === null || $tags === '') {
            return null;
        }

        if (is_string($tags)) {
            $tags = explode(',', $tags);
        }

        $tags = array_values(array_filter(array_map('trim', (array) $tags)));

        return $tags === [] ? null : $tags;
    }

    /**
     * Simpan kategori non-default agar tetap muncul di dropdown pengguna.
     */
    private function rememberCategory(int $userId, ?string $category): void
    {
        if ($category && ! in_array($category, Note::DEFAULT_CATEGORIES, true)) {
            Category::firstOrCreate([
                'user_id' => $userId,
                'name' => $category,
            ]);
        }
    }
}
