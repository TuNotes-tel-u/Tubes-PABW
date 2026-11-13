(function () {
    const form = document.getElementById('uploadForm');

    if (!form) {
        return;
    }

    const fileInput = document.getElementById('fileInput');
    const dropzone = document.getElementById('dropzoneArea');
    const fileStatus = document.getElementById('fileStatus');
    const categorySelect = document.getElementById('category');
    const newCategoryInput = document.getElementById('newCategory');
    const submitButton = document.getElementById('submitButton');
    const errorBox = document.getElementById('formErrors');
    const maxSize = 10 * 1024 * 1024;

    function formatSize(bytes) {
        if (bytes >= 1024 * 1024) {
            return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
        }

        return Math.max(1, Math.round(bytes / 1024)) + ' KB';
    }

    function showFile(file) {
        if (!file) {
            fileStatus.textContent = 'Belum ada file dipilih.';
            fileStatus.classList.remove('file-selected');
            return;
        }

        if (file.size > maxSize) {
            fileStatus.textContent = 'File terlalu besar. Maksimal 10MB.';
            fileStatus.classList.remove('file-selected');
            fileInput.value = '';
            return;
        }

        fileStatus.textContent = file.name + ' (' + formatSize(file.size) + ')';
        fileStatus.classList.add('file-selected');
    }

    dropzone.addEventListener('click', function () {
        fileInput.click();
    });

    dropzone.addEventListener('dragover', function (event) {
        event.preventDefault();
        dropzone.classList.add('highlight');
    });

    dropzone.addEventListener('dragleave', function () {
        dropzone.classList.remove('highlight');
    });

    dropzone.addEventListener('drop', function (event) {
        event.preventDefault();
        dropzone.classList.remove('highlight');

        if (event.dataTransfer.files.length > 0) {
            fileInput.files = event.dataTransfer.files;
            showFile(fileInput.files[0]);
        }
    });

    fileInput.addEventListener('change', function () {
        showFile(fileInput.files[0]);
    });

    categorySelect.addEventListener('change', function () {
        const isNew = categorySelect.value === '__new__';
        newCategoryInput.style.display = isNew ? 'block' : 'none';

        if (isNew) {
            newCategoryInput.focus();
        }
    });

    form.addEventListener('submit', async function (event) {
        event.preventDefault();

        window.TuNotesErrors.reset(errorBox);

        const file = fileInput.files[0];
        const courseName = document.getElementById('courseName').value.trim();
        const category = categorySelect.value === '__new__'
            ? newCategoryInput.value.trim()
            : categorySelect.value;

        if (!courseName) {
            window.TuNotesErrors.show(errorBox, { message: 'Course Name wajib diisi.' });
            return;
        }

        if (!category) {
            window.TuNotesErrors.show(errorBox, { message: 'Category wajib dipilih atau diisi.' });
            return;
        }

        if (!file) {
            window.TuNotesErrors.show(errorBox, { message: 'Pilih file catatan terlebih dahulu.' });
            return;
        }

        const data = new FormData();
        data.append('course_name', courseName);
        data.append('lecturer_name', document.getElementById('lecturerName').value.trim());
        data.append('category', category);
        data.append('tags', document.getElementById('tags').value);
        data.append('description', document.getElementById('description').value);
        data.append('file', file);

        submitButton.disabled = true;
        submitButton.querySelector('span').textContent = 'Mengunggah...';

        try {
            await window.TuNotesAPI.store(data);
            window.location.href = window.TUNOTES.notesUrl;
        } catch (error) {
            window.TuNotesErrors.show(errorBox, error);
            submitButton.disabled = false;
            submitButton.querySelector('span').textContent = 'Upload Notes';
        }
    });
})();
