(function () {
    const isMd = () => window.matchMedia("(min-width: 768px)").matches;
    const pairs = [
        ["autoInfoIcon", "autoInfo"],
        ["customInfoIcon", "customInfo"],
    ]
        .map(([iconId, panelId]) => ({
            icon: document.getElementById(iconId),
            panel: document.getElementById(panelId),
        }))
        .filter((pair) => pair.icon && pair.panel);

    function closeAll() {
        pairs.forEach(({ icon, panel }) => {
            panel.classList.add("hidden");
            panel.classList.remove("inline-block");
            icon.setAttribute("aria-expanded", "false");
        });
    }

    function toggle(target) {
        const isOpen = !target.panel.classList.contains("hidden");
        closeAll();

        if (!isOpen) {
            target.panel.classList.remove("hidden");
            target.panel.classList.add("inline-block");
            target.icon.setAttribute("aria-expanded", "true");
        }
    }

    pairs.forEach((pair) => {
        pair.icon.addEventListener("click", (event) => {
            if (!isMd()) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();
            toggle(pair);
        });

        pair.icon.addEventListener("keydown", (event) => {
            if (!isMd() || (event.key !== "Enter" && event.key !== " ")) {
                return;
            }

            event.preventDefault();
            toggle(pair);
        });
    });

    document.addEventListener("click", (event) => {
        if (!isMd()) {
            return;
        }

        const clickedIcon = pairs.some(({ icon }) => icon.contains(event.target));

        if (!clickedIcon) {
            closeAll();
        }
    });

    window.matchMedia("(min-width: 768px)").addEventListener("change", closeAll);
})();
