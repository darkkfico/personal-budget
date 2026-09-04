(function () {
    const popup = document.getElementById("leftoverAllocationPopup");
    const confirmBtn = document.getElementById("leftoverAllocationConfirm");
    const backBtn = document.getElementById("leftoverAllocationBack");
    const backdrop = document.getElementById("leftoverAllocationBackdrop");
    const select = document.getElementById("leftoverSectionSelect");

    if (!popup) {
        return;
    }

    const typeCustom = document.getElementById("typeCustom");
    if (typeCustom) {
        typeCustom.checked = true;
        typeCustom.dispatchEvent(new Event("change"));
    }

    function hidePopup() {
        popup.classList.add("hidden");
    }

    backBtn?.addEventListener("click", hidePopup);
    backdrop?.addEventListener("click", hidePopup);

    confirmBtn?.addEventListener("click", () => {
        if (!select?.value) {
            select?.reportValidity();
            return;
        }

        const form = document.querySelector("[data-custom-budget-form]");

        if (!form) {
            return;
        }

        let hidden = form.querySelector('[name="leftover_section"]');

        if (!hidden) {
            hidden = document.createElement("input");
            hidden.type = "hidden";
            hidden.name = "leftover_section";
            form.appendChild(hidden);
        }

        hidden.value = select.value;
        form.requestSubmit();
    });
})();
