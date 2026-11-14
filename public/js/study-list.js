// study-list.js — halaman Study List (modul Naufal).
// Data dari API Laravel: /api/semesters dan /api/study-list.
// Semua input lewat study-modal.js (showFormModal, showConfirmModal, showAlertModal).
(function () {
  'use strict';

  const container = document.getElementById('semestersContainer');
  const filter = document.getElementById('semesterFilter');
  const csrf = document.querySelector('meta[name="csrf-token"]');
  const notesUrl = (window.STUDY_LIST && window.STUDY_LIST.notesUrl) || '#';

  let semesters = [];
  let courses = [];

  // -- Pemanggil API: selalu JSON, error dilempar dengan pesan dari server --
  async function request(path, method, body) {
    const headers = { Accept: 'application/json' };
    if (body) headers['Content-Type'] = 'application/json';
    if (csrf) headers['X-CSRF-TOKEN'] = csrf.content;

    const response = await fetch('/api' + path, {
      method: method || 'GET',
      headers: headers,
      credentials: 'same-origin',
      body: body ? JSON.stringify(body) : undefined,
    });

    if (response.status === 204) return null;

    let data = null;
    try {
      data = await response.json();
    } catch (error) {
      data = null;
    }

    if (!response.ok) {
      const err = new Error(errorMessage(data));
      err.status = response.status;
      throw err;
    }
    return data;
  }

  // Gabungkan pesan validasi Laravel (422) jadi satu teks
  function errorMessage(data) {
    if (data && data.errors) {
      return Object.keys(data.errors)
        .map(function (field) { return data.errors[field].join(' '); })
        .join(' ');
    }
    return (data && data.message) || 'Terjadi kesalahan pada server.';
  }

  // Ambil semua halaman /api/study-list (API-nya berpaginasi)
  async function fetchAllCourses() {
    let page = 1;
    let all = [];
    while (true) {
      const result = await request('/study-list?per_page=100&page=' + page);
      all = all.concat(result.data || []);
      if (!result.last_page || page >= result.last_page) break;
      page += 1;
    }
    return all;
  }

  async function load() {
    container.innerHTML = '<p class="study-message">Memuat…</p>';
    try {
      const results = await Promise.all([request('/semesters'), fetchAllCourses()]);
      semesters = results[0] || [];
      courses = results[1];
    } catch (error) {
      container.innerHTML = '<p class="study-message study-message--error">Gagal memuat data: '
        + escapeHtml(error.message) + '</p>';
      return;
    }
    renderFilter();
    render();
  }

  function renderFilter() {
    const selected = filter.value;
    filter.innerHTML = '<option value="">Semua Semester</option>'
      + semesters.map(function (s) {
        return '<option value="' + s.id + '">' + escapeHtml(s.nama) + '</option>';
      }).join('');
    if (semesters.some(function (s) { return String(s.id) === selected; })) {
      filter.value = selected;
    }
  }

  function render() {
    const totalNotes = courses.reduce(function (sum, c) { return sum + (c.notes_count || 0); }, 0);
    setText('statTotalCourses', courses.length);
    setText('statTotalNotes', totalNotes);
    setText('statActiveSemesters', semesters.length);

    if (!semesters.length) {
      container.innerHTML = '<p class="study-message">Belum ada semester. Klik '
        + '<strong>Tambah Semester Baru</strong> di atas untuk mulai.</p>';
      return;
    }

    const shown = semesters.filter(function (s) {
      return !filter.value || String(s.id) === filter.value;
    });

    let html = shown.map(function (s) {
      const list = courses.filter(function (c) { return c.semester_id === s.id; });
      return renderSection(s.nama, list, s.id);
    }).join('');

    // Mata kuliah lama yang belum punya semester (dibuat dari modul notes)
    const orphan = courses.filter(function (c) { return c.semester_id === null; });
    if (!filter.value && orphan.length) {
      html += renderSection('Tanpa Semester', orphan, null);
    }

    container.innerHTML = html;
  }

  function renderSection(title, list, semesterId) {
    const addCard = semesterId !== null
      ? '<div class="course-card course-card--add" data-action="add-course" data-semester-id="' + semesterId + '">'
        + '<div class="course-add-content">'
        + '<div class="add-icon"><i class="fa-solid fa-plus"></i></div>'
        + '<h3 class="course-title course-title--muted">Tambah Mata Kuliah</h3>'
        + '<p class="course-notes-count course-notes-count--muted">Buat mata kuliah baru</p>'
        + '</div></div>'
      : '';

    return '<section class="semester-section">'
      + '<div class="semester-header">'
      + '<h2>' + escapeHtml(title) + '</h2>'
      + '<span class="semester-count">' + list.length + ' mata kuliah</span>'
      + '</div>'
      + '<div class="course-grid">' + list.map(renderCard).join('') + addCard + '</div>'
      + '</section>';
  }

  function renderCard(c) {
    const colors = ['blue', 'purple', 'green', 'orange', 'red-light', 'blue-dark'];
    const color = colors[c.id % colors.length];
    const meta = [c.code, c.sks ? c.sks + ' SKS' : null, c.lecturer_name]
      .filter(Boolean)
      .map(escapeHtml)
      .join(' • ');

    return '<div class="course-card" data-course-id="' + c.id + '">'
      + '<div class="course-actions">'
      + '<button type="button" data-action="edit-course" title="Edit mata kuliah" aria-label="Edit mata kuliah"><i class="fa-solid fa-pen"></i></button>'
      + '<button type="button" data-action="delete-course" title="Hapus mata kuliah" aria-label="Hapus mata kuliah"><i class="fa-solid fa-trash"></i></button>'
      + '</div>'
      + '<div class="course-icon course-icon--' + color + '"><i class="fa-solid fa-book"></i></div>'
      + '<h3 class="course-title">' + escapeHtml(c.name) + '</h3>'
      + '<p class="course-notes-count">' + (c.notes_count || 0) + ' catatan tersedia</p>'
      + (meta ? '<p class="course-meta">' + meta + '</p>' : '')
      + '<a href="' + escapeHtml(notesUrl) + '" class="btn btn--outline">Lihat Catatan</a>'
      + '</div>';
  }

  function courseFields(course) {
    const c = course || {};
    const fields = [
      { name: 'name', label: 'Nama Mata Kuliah', type: 'text', required: true,
        value: c.name || '', placeholder: 'contoh: Pengembangan Aplikasi Web' },
      { name: 'code', label: 'Kode Mata Kuliah', type: 'text', required: true,
        value: c.code || '', placeholder: 'contoh: IF101' },
      { name: 'sks', label: 'SKS (1 sampai 6)', type: 'number', required: true,
        value: c.sks || 3 },
      { name: 'lecturer_name', label: 'Dosen Pengajar (opsional)', type: 'text',
        value: c.lecturer_name || '' },
    ];
    if (course) {
      fields.unshift({
        name: 'semester_id', label: 'Semester', type: 'select', required: true,
        value: c.semester_id === null ? '' : String(c.semester_id),
        options: semesters.map(function (s) { return { value: String(s.id), label: s.nama }; }),
      });
    }
    return fields;
  }

  function payload(data, semesterId) {
    return {
      semester_id: parseInt(semesterId, 10),
      name: String(data.name || '').trim(),
      code: String(data.code || '').trim(),
      sks: parseInt(data.sks, 10),
      lecturer_name: String(data.lecturer_name || '').trim() || null,
    };
  }

  async function run(action) {
    try {
      await action();
      await load();
    } catch (error) {
      await showAlertModal({ type: 'error', title: 'Gagal', message: error.message });
    }
  }

  // -- Tambah Semester --
  document.getElementById('btnAddSemester').addEventListener('click', async function () {
    const data = await showFormModal({
      title: 'Tambah Semester Baru',
      fields: [
        { name: 'nama', label: 'Nama Semester', type: 'text', required: true,
          placeholder: 'contoh: 2026 Ganjil' },
      ],
      submitLabel: 'Tambah Semester',
    });
    if (!data) return;
    await run(function () {
      return request('/semesters', 'POST', { nama: String(data.nama || '').trim() });
    });
  });

  filter.addEventListener('change', render);

  // -- Aksi di kartu (event delegation) --
  container.addEventListener('click', async function (e) {
    const addCard = e.target.closest('[data-action="add-course"]');
    if (addCard) {
      const data = await showFormModal({
        title: 'Tambah Mata Kuliah',
        fields: courseFields(null),
        submitLabel: 'Tambah',
      });
      if (!data) return;
      await run(function () {
        return request('/study-list', 'POST', payload(data, addCard.dataset.semesterId));
      });
      return;
    }

    const button = e.target.closest('[data-action="edit-course"], [data-action="delete-course"]');
    if (!button) return;

    const id = parseInt(button.closest('.course-card').dataset.courseId, 10);
    const course = courses.find(function (c) { return c.id === id; });
    if (!course) return;

    if (button.dataset.action === 'edit-course') {
      const data = await showFormModal({
        title: 'Edit Mata Kuliah',
        fields: courseFields(course),
        submitLabel: 'Simpan',
      });
      if (!data) return;
      await run(function () {
        return request('/study-list/' + id, 'PATCH', payload(data, data.semester_id));
      });
      return;
    }

    const ok = await showConfirmModal({
      title: 'Hapus Mata Kuliah?',
      message: '"' + course.name + '" akan dihapus. Catatan yang terhubung tetap ada.',
      confirmLabel: 'Hapus',
      danger: true,
    });
    if (!ok) return;
    await run(function () { return request('/study-list/' + id, 'DELETE'); });
  });

  function setText(id, value) {
    const el = document.getElementById(id);
    if (el) el.textContent = String(value);
  }

  function escapeHtml(value) {
    return String(value == null ? '' : value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  load();
})();
