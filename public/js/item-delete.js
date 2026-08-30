(function () {
    const popup = document.getElementById("itemDeletePopup");
    const form = document.getElementById("itemDeleteForm");
    const nameEl = document.getElementById("itemDeleteName");
    const permanentInput = document.getElementById("itemDeletePermanent");
    const keepHistoryBtn = document.getElementById("itemDeleteKeepHistory");
    const permanentBtn = document.getElementById("itemDeletePermanentBtn");
    const cancelBtn = document.getElementById("itemDeleteCancel");
    const backdrop = document.getElementById("itemDeleteBackdrop");

    if (!popup || !form) {
        return;
    }

    function closePopup() {
        popup.classList.add("hidden");
        form.action = "";
        permanentInput.value = "0";
    }

    function openPopup(button) {
        form.action = button.dataset.deleteAction;
        nameEl.textContent = `You are deleting “${button.dataset.itemName}”.`;
        popup.classList.remove("hidden");
    }

    document.querySelectorAll("[data-delete-open]").forEach((button) => {
        button.addEventListener("click", () => openPopup(button));
    });

    keepHistoryBtn?.addEventListener("click", () => {
        permanentInput.value = "0";
    });

    permanentBtn?.addEventListener("click", () => {
        permanentInput.value = "1";
    });

    cancelBtn?.addEventListener("click", closePopup);
    backdrop?.addEventListener("click", closePopup);

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape" && !popup.classList.contains("hidden")) {
            closePopup();
        }
    });
})();
