<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reading;
use Illuminate\Http\Request;

class ReadingController extends Controller
{
    // Menampilkan semua data reading
    public function index()
    {
        $readings = Reading::latest()->paginate(10);

        return response()->json($readings, 200);
    }

    // Menampilkan satu data reading
    public function show($id)
    {
        $reading = Reading::find($id);

        if (!$reading) {
            return response()->json([
                'message' => 'Reading tidak ditemukan'
            ], 404);
        }

        return response()->json($reading, 200);
    }

    // Menambahkan data reading
    public function store(Request $request)
    {
        $validated = $request->validate([
            'note_title' => 'required|string|max:255',
            'status' => 'required|string',
            'progress' => 'required|integer|min:0|max:100',
            'target_date' => 'required|date',
        ]);

        $reading = Reading::create($validated);

        return response()->json([
            'message' => 'Reading berhasil ditambahkan',
            'data' => $reading
        ], 201);
    }

    // Mengubah data reading
    public function update(Request $request, $id)
    {
        $reading = Reading::find($id);

        if (!$reading) {
            return response()->json([
                'message' => 'Reading tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'note_title' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|string',
            'progress' => 'sometimes|required|integer|min:0|max:100',
            'target_date' => 'sometimes|required|date',
        ]);

        $reading->update($validated);

        return response()->json([
            'message' => 'Reading berhasil diperbarui',
            'data' => $reading
        ], 200);
    }

    // Menghapus data reading
    public function destroy($id)
    {
        $reading = Reading::find($id);

        if (!$reading) {
            return response()->json([
                'message' => 'Reading tidak ditemukan'
            ], 404);
        }

        $reading->delete();

        return response()->json([
            'message' => 'Reading berhasil dihapus'
        ], 200);
    }
}
