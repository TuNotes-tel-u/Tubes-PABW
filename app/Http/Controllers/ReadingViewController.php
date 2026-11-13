<?php

namespace App\Http\Controllers;

use App\Models\Reading;
use Illuminate\Http\Request;

class ReadingViewController extends Controller
{
    // Menampilkan semua data reading
    public function index()
    {
        $readings = Reading::latest()->get();

        return view('readings.index', compact('readings'));
    }

    // Menampilkan form tambah reading
    public function create()
    {
        return view('readings.create');
    }

    // Menyimpan reading baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'note_title' => 'required|string|max:255',
            'status' => 'required|string',
            'progress' => 'required|integer|min:0|max:100',
            'target_date' => 'required|date',
        ]);

        Reading::create($validated);

        return redirect()->route('readings.index')
            ->with('success', 'Reading berhasil ditambahkan');
    }

    // Menampilkan form edit
    public function edit($id)
    {
        $reading = Reading::findOrFail($id);

        return view('readings.edit', compact('reading'));
    }

    // Memperbarui reading
    public function update(Request $request, $id)
    {
        $reading = Reading::findOrFail($id);

        $validated = $request->validate([
            'note_title' => 'required|string|max:255',
            'status' => 'required|string',
            'progress' => 'required|integer|min:0|max:100',
            'target_date' => 'required|date',
        ]);

        $reading->update($validated);

        return redirect()->route('readings.index')
            ->with('success', 'Reading berhasil diperbarui');
    }

    // Menghapus reading
    public function destroy($id)
    {
        $reading = Reading::findOrFail($id);

        $reading->delete();

        return redirect()->route('readings.index')
            ->with('success', 'Reading berhasil dihapus');
    }
}