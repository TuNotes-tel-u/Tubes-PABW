(function () {
    window.TuNotesToast = function (message) {
        const toast = document.createElement('div');
        toast.textContent = message;
        toast.style.cssText = 'position:fixed;right:24px;bottom:24px;background:#111827;color:#fff;' +
            'padding:12px 18px;border-radius:8px;font:14px Inter,sans-serif;z-index:9999;' +
            'box-shadow:0 8px 20px rgba(0,0,0,.18);';

        document.body.appendChild(toast);

        setTimeout(function () {
            toast.remove();
        }, 3000);
    };
})();
