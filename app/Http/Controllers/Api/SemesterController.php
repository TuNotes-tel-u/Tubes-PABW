<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// Semester untuk halaman Study List (daftar + tambah)
class SemesterController extends Controller
{
    // GET /api/semesters
    public function index(): JsonResponse
    {
        $semesters = Semester::withCount('courses')
            ->orderByDesc('id')
            ->get();

        return response()->json($semesters);
    }

    // POST /api/semesters -> 201
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:50', 'unique:semesters,nama'],
        ], [
            'nama.unique' => 'Nama semester sudah ada.',
        ]);

        $semester = Semester::create($validated);

        return response()->json($semester, 201);
    }
}
