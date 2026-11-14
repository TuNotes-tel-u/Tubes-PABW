<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    // GET /api/study-list?semester_id=1&q=web&per_page=100
    public function index(Request $request): JsonResponse
    {
        // per_page opsional (default 10, maksimal 100) untuk halaman Study List
        $perPage = min(max((int) $request->input('per_page', 10), 1), 100);

        $courses = Course::query()
            ->withCount('notes')
            ->when($request->filled('semester_id'), function ($query) use ($request) {
                $query->where('semester_id', $request->semester_id);
            })
            ->when($request->filled('q'), function ($query) use ($request) {
                // escape % dan _ supaya dicari sebagai teks biasa
                $kata = addcslashes($request->q, '%_\\');
                $query->where('name', 'LIKE', "%{$kata}%");
            })
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();

        return response()->json($courses);
    }

    // POST /api/study-list -> 201 + header Location
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->rules(), $this->messages());

        $course = Course::create($validated);

        return response()->json($course, 201)
            ->header('Location', route('study-list.show', $course));
    }

    // GET /api/study-list/{course}
    public function show(Course $course): JsonResponse
    {
        return response()->json($course);
    }

    // PUT/PATCH /api/study-list/{course} (boleh kirim sebagian field)
    public function update(Request $request, Course $course): JsonResponse
    {
        $validated = $request->validate($this->rules($course), $this->messages());

        $course->update($validated);

        return response()->json($course);
    }

    // DELETE /api/study-list/{course} -> 204 No Content
    public function destroy(Course $course): Response
    {
        $course->delete();

        return response()->noContent();
    }

    private function rules(?Course $course = null): array
    {
        // store: wajib semua. update: cukup field yang dikirim (sometimes)
        $wajib = $course ? 'sometimes' : 'required';

        return [
            'semester_id' => [$wajib, 'integer', 'exists:semesters,id'],
            'code' => [
                $wajib,
                'string',
                'max:30',
                Rule::unique('courses', 'code')->ignore($course?->id),
            ],
            'name' => [$wajib, 'string', 'max:120'],
            'lecturer_name' => ['nullable', 'string', 'max:120'],
            'sks' => [$wajib, 'integer', 'between:1,6'],
        ];
    }

    // Pesan validasi yang tampil di halaman Study List
    private function messages(): array
    {
        return [
            'code.unique' => 'Kode mata kuliah sudah dipakai.',
            'sks.between' => 'SKS harus antara 1 sampai 6.',
            'semester_id.exists' => 'Semester tidak ditemukan.',
        ];
    }
}
