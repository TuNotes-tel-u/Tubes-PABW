<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Note;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotePageController extends Controller
{
    /**
     * Halaman Dashboard: katalog catatan publik + pencarian & sorting.
     */
    public function dashboard(Request $request): View
    {
        $notes = Note::query()
            ->active()
            ->where('is_public', true)
            ->when($request->filled('query'), function ($query) use ($request) {
                $search = '%' . $request->query('query') . '%';

                $query->where(function ($q) use ($search) {
                    $q->where('title', 'LIKE', $search)
                        ->orWhere('file_name', 'LIKE', $search)
                        ->orWhere('course_name', 'LIKE', $search)
                        ->orWhere('lecturer_name', 'LIKE', $search);
                });
            })
            ->when($request->filled('category'), fn ($query) => $query->where('category', $request->query('category')));

        match ($request->query('sort', 'newest')) {
            'popular' => $notes->orderByDesc('downloads_count'),
            'rating' => $notes->orderByDesc('avg_rating'),
            default => $notes->orderByDesc('created_at'),
        };

        return view('dashboard', [
            'notes' => $notes->orderByDesc('id')->get(),
        ]);
    }

    /**
     * Halaman My Notes: catatan milik user yang sedang login.
     */
    public function index(Request $request): View
    {
        $userId = Auth::id();

        $notes = Note::query()
            ->active()
            ->ownedBy($userId)
            ->when($request->filled('category'), fn ($query) => $query->where('category', $request->query('category')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%' . $request->query('search') . '%';

                $query->where(function ($q) use ($search) {
                    $q->where('title', 'LIKE', $search)
                        ->orWhere('file_name', 'LIKE', $search)
                        ->orWhere('course_name', 'LIKE', $search);
                });
            })
            ->orderByDesc('created_at')
            ->get();

        return view('notes.index', [
            'notes' => $notes,
            'categories' => $this->categoriesFor($userId),
        ]);
    }

    /**
     * Halaman Detail My Notes: daftar catatan dalam satu kategori.
     */
    public function category(Request $request, string $category): View
    {
        $userId = Auth::id();

        $notes = Note::query()
            ->active()
            ->ownedBy($userId)
            ->where('category', $category)
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%' . $request->query('search') . '%';

                $query->where(function ($q) use ($search) {
                    $q->where('title', 'LIKE', $search)
                        ->orWhere('file_name', 'LIKE', $search)
                        ->orWhere('course_name', 'LIKE', $search);
                });
            })
            ->orderByDesc('created_at')
            ->get();

        return view('notes.category', [
            'notes' => $notes,
            'category' => $category,
            'categories' => $this->categoriesFor($userId),
        ]);
    }

    /**
     * Halaman Upload Notes.
     */
    public function create(): View
    {
        return view('notes.upload', [
            'categories' => $this->categoriesFor(Auth::id()),
        ]);
    }

    /**
     * Halaman Update Notes.
     */
    public function edit(Note $note): View
    {
        abort_unless($note->user_id === Auth::id(), 403);

        return view('notes.edit', [
            'note' => $note,
            'categories' => $this->categoriesFor(Auth::id()),
        ]);
    }

    /**
     * Form sederhana "tambah category" pada halaman My Notes.
     */
    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.max' => 'Nama kategori maksimal 50 karakter.',
        ]);

        Category::firstOrCreate([
            'user_id' => Auth::id(),
            'name' => trim($validated['name']),
        ]);

        return back()->with('status', 'Kategori "' . trim($validated['name']) . '" berhasil ditambahkan.');
    }

    /**
     * Gabungan kategori default + kategori buatan user + kategori pada notes.
     *
     * @return array<int, string>
     */
    private function categoriesFor(int $userId): array
    {
        return collect(Note::DEFAULT_CATEGORIES)
            ->merge(Category::where('user_id', $userId)->pluck('name'))
            ->merge(
                Note::where('user_id', $userId)
                    ->whereNotNull('category')
                    ->distinct()
                    ->pluck('category')
            )
            ->unique()
            ->values()
            ->all();
    }
}
