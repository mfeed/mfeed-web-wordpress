document.addEventListener("DOMContentLoaded", function () {
    const current = window.location.pathname.replace(/\/$/, "") + "/";
    document.querySelectorAll('.sidebar-cell').forEach(a => {
        if (a.getAttribute("href") === current) {
            a.classList.add("active");
        }
    });
});