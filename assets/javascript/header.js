// Detect the current page to make the button on nav bar active

document.addEventListener("DOMContentLoaded", () => {
    const currentPath = window.location.pathname.split("/").pop();  

    document.querySelectorAll(".nav-menu a").forEach(link => {
        const linkPath = link.getAttribute("href");

        if (linkPath === currentPath) {
            link.classList.add("active");
        } else {
            link.classList.remove("active");
        }
    });
});
