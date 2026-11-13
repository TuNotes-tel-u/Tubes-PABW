(function () {
    const searchInput = document.getElementById('searchInput');
    const cards = Array.prototype.slice.call(document.querySelectorAll('#notesGrid .note-card'));

    if (!searchInput) {
        return;
    }

    searchInput.addEventListener('input', function () {
        const keyword = searchInput.value.toLowerCase().trim();

        cards.forEach(function (card) {
            const haystack = (card.dataset.search || card.textContent).toLowerCase();
            card.style.display = haystack.includes(keyword) ? '' : 'none';
        });
    });
})();
