document.querySelectorAll("[data-history-toggle]").forEach((button) => {
    button.addEventListener("click", async () => {
        const panel = button.nextElementSibling;
        const arrow = button.querySelector(".category-arrow");
        const isOpen = panel.classList.contains("open");

        if (!isOpen && panel.dataset.loaded !== "1") {
            panel.innerHTML = '<p class="px-4 py-2.5 text-sm text-secondary/70">Loading...</p>';

            const url = new URL(panel.dataset.itemsUrl, window.location.origin);
            url.searchParams.set("month", panel.dataset.month);
            url.searchParams.set("field", panel.dataset.field);

            try {
                const response = await fetch(url, {
                    headers: { Accept: "application/json" },
                });
                const items = await response.json();
                const currency = panel.dataset.currency;

                if (!items.length) {
                    panel.textContent = "";
                    const empty = document.createElement("p");
                    empty.className = "px-4 py-2.5 text-sm text-secondary/70";
                    empty.textContent = "No items";
                    panel.appendChild(empty);
                } else {
                    panel.replaceChildren(
                        ...items.map((item) => {
                            const row = document.createElement("div");
                            row.className = "flex justify-between items-center px-4 py-2.5 bg-lightbutter/60 border-t border-butter/30";
                            const name = document.createElement("span");
                            name.className = "text-sm text-secondary/80 font-medium";
                            name.textContent = item.name;
                            const amount = document.createElement("span");
                            amount.className = "text-sm font-semibold text-secondary";
                            amount.textContent = `${item.amount} ${currency}`;
                            row.append(name, amount);
                            return row;
                        })
                    );
                }
            } catch (error) {
                panel.innerHTML = '<p class="px-4 py-2.5 text-sm text-red-600">Could not load items</p>';
            }

            panel.dataset.loaded = "1";
        }

        panel.classList.toggle("open", !isOpen);
        arrow?.classList.toggle("open", !isOpen);
        button.setAttribute("aria-expanded", String(!isOpen));
    });
});
