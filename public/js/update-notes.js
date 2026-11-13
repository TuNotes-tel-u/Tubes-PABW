(function () {
    const form = document.getElementById('updateForm');

    if (!form) {
        return;
    }

    const fileInput = document.getElementById('fileInputUpdate');
    const chooseButton = document.getElementById('chooseNewFile');
    const currentFileName = document.getElementById('currentFileName');
    const currentFileSize = document.getElementById('currentFileSize');
    const cancelButton = document.getElementById('cancelButton');
    const errorBox = document.getElementById('formErrors');
    const maxSize = 10 * 1024 * 1024;

    function formatSize(bytes) {
        if (bytes >= 1024 * 1024) {
            return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
        }

        return Math.max(1, Math.round(bytes / 1024)) + ' KB';
    }

    chooseButton.addEventListener('click', function () {
        fileInput.click();
    });

    fileInput.addEventListener('change', function () {
        const file = fileInput.files[0];

        if (!file) {
            return;
        }

        if (file.size > maxSize) {
            window.TuNotesErrors.show(errorBox, { message: 'File terlalu besar. Maksimal 10MB.' });
            fileInput.value = '';
            return;
        }

        currentFileName.textContent = file.name;
        currentFileSize.textContent = formatSize(file.size);
    });

    cancelButton.addEventListener('click', function () {
        window.location.href = form.dataset.cancelUrl;
    });

    form.addEventListener('submit', async function (event) {
        event.preventDefault();

        window.TuNotesErrors.reset(errorBox);

        const courseName = document.getElementById('courseName').value.trim();

        if (!courseName) {
            window.TuNotesErrors.show(errorBox, { message: 'Course Name wajib diisi.' });
            return;
        }

        const data = new FormData();
        data.append('course_name', courseName);
        data.append('lecturer_name', document.getElementById('lecturerName').value.trim());
        data.append('tags', document.getElementById('tagsInput').value);
        data.append('category', document.getElementById('category').value);
        data.append('description', document.getElementById('description').value);

        if (fileInput.files[0]) {
            data.append('file', fileInput.files[0]);
        }

        const submitButton = form.querySelector('.btn-save');
        submitButton.disabled = true;

        try {
            await window.TuNotesAPI.update(form.dataset.noteId, data);
            window.location.href = form.dataset.cancelUrl;
        } catch (error) {
            window.TuNotesErrors.show(errorBox, error);
            submitButton.disabled = false;
        }
    });
})();
