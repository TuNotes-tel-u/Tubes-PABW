(function () {
    const user = window.TUNOTES && window.TUNOTES.user;

    if (!user) {
        return;
    }

    document.querySelectorAll('[data-user-name]').forEach(function (element) {
        if (!element.textContent.trim()) {
            element.textContent = user.name;
        }
    });

    document.querySelectorAll('[data-user-avatar]').forEach(function (element) {
        if (user.avatar) {
            element.src = user.avatar;
        }
    });
})();
