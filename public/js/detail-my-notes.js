(function () {
    const searchInput = document.getElementById('searchInput');
    const rows = Array.prototype.slice.call(document.querySelectorAll('#notesTableBody tr[data-note-id]'));
    const showingText = document.getElementById('showingText');

    function updateCount() {
        const visible = rows.filter(function (row) {
            return row.style.display !== 'none';
        }).length;

        if (showingText) {
            showingText.textContent = 'Menampilkan ' + visible + ' catatan';
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const keyword = searchInput.value.toLowerCase().trim();

            rows.forEach(function (row) {
                const haystack = (row.dataset.search || row.textContent).toLowerCase();
                row.style.display = haystack.includes(keyword) ? '' : 'none';
            });

            updateCount();
        });
    }

    document.querySelectorAll('[data-delete-note]').forEach(function (button) {
        button.addEventListener('click', async function () {
            const id = button.dataset.deleteNote;

            if (!window.confirm('Hapus catatan ini? Tindakan ini tidak dapat dibatalkan.')) {
                return;
            }

            button.disabled = true;

            try {
                await window.TuNotesAPI.destroy(id);

                const row = button.closest('tr');

                if (row) {
                    const index = rows.indexOf(row);

                    if (index !== -1) {
                        rows.splice(index, 1);
                    }

                    row.remove();
                }

                updateCount();
            } catch (error) {
                button.disabled = false;
                alert(error.message);
            }
        });
    });
})();
