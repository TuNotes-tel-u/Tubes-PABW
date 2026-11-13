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
    // GET /api/study-list?semester_id=1&q=web
    public function index(Request $request): JsonResponse
    {
        $courses = Course::query()
            ->when($request->filled('semester_id'), function ($query) use ($request) {
                $query->where('semester_id', $request->semester_id);
            })
            ->when($request->filled('q'), function ($query) use ($request) {
                // escape % dan _ supaya dicari sebagai teks biasa
                $kata = addcslashes($request->q, '%_\\');
                $query->where('nama_mk', 'LIKE', "%{$kata}%");
            })
            ->orderBy('nama_mk')
            ->paginate(10)
            ->withQueryString();

        return response()->json($courses);
    }

    // POST /api/study-list  -> 201 + header Location
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->rules($request->input('semester_id')));

        $course = Course::create($validated);

        return response()->json($course, 201)
            ->header('Location', route('study-list.show', $course));
    }

    // GET /api/study-list/{course}
    public function show(Course $course): JsonResponse
    {
        return response()->json($course);
    }

    // PUT/PATCH /api/study-list/{course}  (boleh kirim sebagian field)
    public function update(Request $request, Course $course): JsonResponse
    {
        $semesterId = $request->input('semester_id', $course->semester_id);
        $validated = $request->validate($this->rules($semesterId, $course));

        $course->update($validated);

        return response()->json($course);
    }

    // DELETE /api/study-list/{course}  -> 204 No Content
    public function destroy(Course $course): Response
    {
        $course->delete();

        return response()->noContent();
    }

    // Aturan validasi dipakai bersama oleh store dan update
    private function rules($semesterId, ?Course $course = null): array
    {
        // store: wajib semua. update: cukup field yang dikirim (sometimes)
        $wajib = $course ? 'sometimes' : 'required';

        return [
            'semester_id' => [$wajib, 'integer', 'exists:semesters,id'],
            'kode_mk' => [
                $wajib,
                'string',
                'max:10',
                // kode mata kuliah harus unik di dalam satu semester
                Rule::unique('courses', 'kode_mk')
                    ->where(fn ($query) => $query->where('semester_id', $semesterId))
                    ->ignore($course?->id),
            ],
            'nama_mk' => [$wajib, 'string', 'max:100'],
            'sks' => [$wajib, 'integer', 'between:1,6'],
            'dosen' => ['nullable', 'string', 'max:100'],
        ];
    }
}
