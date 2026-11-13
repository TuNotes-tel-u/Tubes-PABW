<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/Logo-Tunotes.png') }}">
    <meta name="description" content="TuNotes — platform catatan kuliah cerdas untuk mahasiswa Indonesia.">
    <title>Detail My Notes - TuNotes</title>
    <link rel="stylesheet" href="{{ asset('css/DetailMyNotes.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://unpkg.com/akar-icons-fonts"></script>
</head>
<body>

    <header class="page-header">
        <div class="header-content">
            <a href="{{ route('notes.page') }}" class="back-button">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
            </a>
            <h3 class="page-title" id="courseTitle">{{ $category }}</h3>
        </div>
    </header>

    <main class="main-container">

        <div class="controls-wrapper">
            <div class="filters-left">
                <div class="dropdown">
                    <span>All Status</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </div>

                <div class="dropdown">
                    <span>All Dates</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </div>

                <div class="search-box">
                    <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" id="searchInput" placeholder="Search documents...">
                </div>
            </div>
            <div class="create">
               <a href="{{ route('notes.upload', ['category' => $category]) }}" class="btn btn-create"><i class="ai-plus"></i>Create Notes</a>
            </div>
        </div>

        <div class="table-card">
            <table class="notes-table">
                <thead>
                    <tr>
                        <th class="th-doc" style="width: 35%;">Document Title</th>
                        <th class="th-lecturer" style="width: 20%;">Lecturer</th>
                        <th class="th-date" style="width: 15%;">Upload Date</th>
                        <th class="th-actions" style="width: 30%;">Actions</th>
                    </tr>
                </thead>
                <tbody id="notesTableBody">
                    @forelse ($notes as $note)
                        @php
                            $iconClass = match ($note->file_type) {
                                'pdf' => 'icon-pdf',
                                'doc', 'docx' => 'icon-word',
                                'ppt', 'pptx' => 'icon-ppt',
                                default => 'icon-excel',
                            };
                        @endphp
                        <tr data-note-id="{{ $note->id }}"
                            data-search="{{ strtolower($note->display_title . ' ' . $note->file_name . ' ' . $note->course_name . ' ' . $note->lecturer_name) }}">
                            <td>
                                <div class="doc-info">
                                    <div class="file-icon {{ $iconClass }}">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                            <polyline points="14 2 14 8 20 8"></polyline>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="doc-name">{{ $note->display_title }}</div>
                                        <div class="doc-file">{{ $note->file_name }} • {{ number_format($note->file_size / 1024, 0) }} KB</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="lecturer-info">
                                    <img src="{{ asset('images/user1.png') }}" alt="" class="avatar">
                                    <span class="lecturer-name">{{ $note->lecturer_name ?: '-' }}</span>
                                </div>
                            </td>
                            <td><span class="text-date">{{ $note->created_at?->format('d M Y') }}</span></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('notes.edit', $note) }}" class="btn-action btn-update">Update</a>
                                    @if ($note->file_path)
                                        <a href="{{ asset(ltrim($note->file_path, '/')) }}" target="_blank" class="btn-action btn-download">View</a>
                                    @endif
                                    <button type="button" class="btn-action btn-delete" data-delete-note="{{ $note->id }}">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align:center; color:#6b7280; padding:32px;">
                                Belum ada catatan pada kategori {{ $category }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            <span class="showing-text" id="showingText">Menampilkan {{ $notes->count() }} catatan</span>
            <div class="pagination-controls">
                <button class="page-btn prev">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </button>
                <button class="page-btn active">1</button>
                <button class="page-btn next">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/api.js') }}"></script>
    <script src="{{ asset('js/detail-my-notes.js') }}"></script>
</body>
</html>
