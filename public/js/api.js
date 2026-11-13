(function () {
    const csrf = document.querySelector('meta[name="csrf-token"]');

    async function request(path, options) {
        const opts = options || {};
        const isForm = opts.body instanceof FormData;

        const headers = Object.assign(
            { Accept: 'application/json' },
            isForm ? {} : { 'Content-Type': 'application/json' },
            opts.headers || {}
        );

        if (csrf) {
            headers['X-CSRF-TOKEN'] = csrf.content;
        }

        const response = await fetch('/api' + path, Object.assign(
            { credentials: 'same-origin' },
            opts,
            { headers: headers }
        ));

        let data = null;

        if (response.status !== 204) {
            const text = await response.text();

            try {
                data = text ? JSON.parse(text) : null;
            } catch (error) {
                data = { message: text };
            }
        }

        if (!response.ok) {
            const err = new Error((data && data.message) || 'Terjadi kesalahan pada server.');
            err.status = response.status;
            err.data = data;
            throw err;
        }

        return data;
    }

    function resetFormErrors(container) {
        if (!container) {
            return;
        }

        container.innerHTML = '';
        container.style.display = 'none';
    }

    function showFormErrors(container, error) {
        if (!container) {
            alert(error.message);
            return;
        }

        const messages = [];

        if (error.data && error.data.errors) {
            Object.keys(error.data.errors).forEach(function (field) {
                messages.push(error.data.errors[field].join(' '));
            });
        } else {
            messages.push(error.message);
        }

        container.innerHTML = messages.map(function (message) {
            return '<div>' + message + '</div>';
        }).join('');
        container.style.display = 'block';
    }

    window.TuNotesAPI = {
        get: function (path) {
            return request(path, { method: 'GET' });
        },

        store: function (formData) {
            return request('/notes', { method: 'POST', body: formData });
        },

        update: function (id, formData) {
            formData.append('_method', 'PATCH');

            return request('/notes/' + id, { method: 'POST', body: formData });
        },

        destroy: function (id) {
            return request('/notes/' + id, { method: 'DELETE' });
        },
    };

    window.TuNotesErrors = {
        reset: resetFormErrors,
        show: showFormErrors,
    };
})();
