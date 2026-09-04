(function () {
    const popup = document.getElementById("itemAdjustPopup");
    const form = document.getElementById("itemAdjustForm");
    const title = document.getElementById("itemAdjustTitle");
    const hint = document.getElementById("itemAdjustHint");
    const label = document.getElementById("itemAdjustLabel");
    const amountInput = document.getElementById("itemAdjustAmount");
    const adjustValue = document.getElementById("itemAdjustValue");
    const submitBtn = document.getElementById("itemAdjustSubmit");
    const cancelBtn = document.getElementById("itemAdjustCancel");
    const backdrop = document.getElementById("itemAdjustBackdrop");

    if (!popup || !form) {
        return;
    }

    let direction = "add";

    function closePopup() {
        popup.classList.add("hidden");
        amountInput.value = "";
        adjustValue.value = "";
    }

    function openPopup(button) {
        direction = button.dataset.direction;
        const itemName = button.dataset.itemName;
        const current = button.dataset.current;
        const currency = button.dataset.currency || "";
        const currentText = window.formatMoney(current, currency);

        form.action = button.dataset.action;

        if (direction === "add") {
            title.textContent = `Adding to ${itemName}`;
            hint.textContent = `You are adding to the current amount (${currentText}).`;
            label.textContent = "Amount to add";
            submitBtn.textContent = "Add";
        } else {
            title.textContent = `Subtracting from ${itemName}`;
            hint.textContent = `You are subtracting from the current amount (${currentText}).`;
            label.textContent = "Amount to subtract";
            submitBtn.textContent = "Subtract";
        }

        popup.classList.remove("hidden");
        amountInput.focus();
    }

    document.querySelectorAll("[data-adjust-open]").forEach((button) => {
        button.addEventListener("click", () => openPopup(button));
    });

    cancelBtn?.addEventListener("click", closePopup);
    backdrop?.addEventListener("click", closePopup);

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape" && !popup.classList.contains("hidden")) {
            closePopup();
        }
    });

    form.addEventListener("submit", (event) => {
        const amount = Number(amountInput.value);

        if (!Number.isFinite(amount) || amount <= 0) {
            event.preventDefault();
            amountInput.focus();
            return;
        }

        adjustValue.value = direction === "add" ? String(amount) : String(-amount);
    });
})();
