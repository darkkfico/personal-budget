document.querySelectorAll("[data-history-toggle]").forEach((button) => {
    button.addEventListener("click", () => {
        const panel = button.nextElementSibling;
        const arrow = button.querySelector(".category-arrow");
        const isOpen = panel.classList.contains("open");

        panel.classList.toggle("open", !isOpen);
        arrow?.classList.toggle("open", !isOpen);
        button.setAttribute("aria-expanded", String(!isOpen));
    });
});
