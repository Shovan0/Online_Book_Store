document.addEventListener('DOMContentLoaded', function () {
    const toasts = document.querySelectorAll('.toast');

    if (toasts.length > 0) {
        toasts.forEach(function (toast) {
            // Keep flash messages visible until page navigation or manual close.
        });
    }
});
