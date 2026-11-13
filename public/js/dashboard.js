(function () {
    const greeting = document.getElementById('greeting');
    const userName = window.TUNOTES && window.TUNOTES.user ? window.TUNOTES.user.name : '';

    if (greeting) {
        const hour = new Date().getHours();
        let timeGreeting = 'Selamat malam';

        if (hour < 11) {
            timeGreeting = 'Selamat pagi';
        } else if (hour < 15) {
            timeGreeting = 'Selamat siang';
        } else if (hour < 19) {
            timeGreeting = 'Selamat sore';
        }

        greeting.textContent = timeGreeting + ', ' + userName + ' !';
    }

    // Progress belajar: jumlah catatan yang tampil dari target 20 catatan.
    const count = (window.TUNOTES && window.TUNOTES.notesCount) || 0;
    const progressNumber = document.getElementById('progressNumber');
    const progressFill = document.getElementById('progressFill');
    const progressMessage = document.getElementById('progressMessage');
    const percentage = Math.min(100, Math.round((count / 20) * 100));

    if (progressNumber) {
        progressNumber.textContent = count + '/20';
    }

    if (progressFill) {
        progressFill.style.width = percentage + '%';
    }

    if (progressMessage) {
        progressMessage.textContent = count === 0
            ? 'Belum ada catatan yang dibaca. Mulai jelajahi catatan!'
            : 'Kamu sudah menyelesaikan ' + percentage + '% target belajar.';
    }

    // Tab sorting memakai query string yang diproses controller.
    document.querySelectorAll('.tab-btn[data-sort]').forEach(function (button) {
        button.addEventListener('click', function () {
            const url = new URL(window.location.href);
            url.searchParams.set('sort', button.dataset.sort);
            window.location.href = url.toString();
        });
    });

    // Like/favorit masih bersifat lokal pada UI.
    document.querySelectorAll('.like-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            button.classList.toggle('active');
        });
    });
})();
