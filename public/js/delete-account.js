(function () {
    const popup = document.getElementById("deleteAccountPopup");
    const confirmStep = document.getElementById("deleteAccountConfirm");
    const passwordStep = document.getElementById("deleteAccountPassword");
    const openBtn = document.getElementById("deleteAccountOpen");
    const backdrop = document.getElementById("deleteAccountBackdrop");
    const noBtn = document.getElementById("deleteAccountNo");
    const yesBtn = document.getElementById("deleteAccountYes");
    const passwordCancel = document.getElementById("deleteAccountPasswordCancel");

    if (!popup) {
        return;
    }

    function showConfirm() {
        confirmStep.classList.remove("hidden");
        passwordStep.classList.add("hidden");
        popup.dataset.step = "confirm";
    }

    function showPassword() {
        confirmStep.classList.add("hidden");
        passwordStep.classList.remove("hidden");
        popup.dataset.step = "password";
        popup.querySelector('input[name="password"]')?.focus();
    }

    function openPopup() {
        showConfirm();
        popup.classList.remove("hidden");
    }

    function closePopup() {
        popup.classList.add("hidden");
        showConfirm();
        const input = popup.querySelector('input[name="password"]');
        if (input) {
            input.value = "";
        }
    }

    openBtn?.addEventListener("click", openPopup);
    noBtn?.addEventListener("click", closePopup);
    yesBtn?.addEventListener("click", showPassword);
    passwordCancel?.addEventListener("click", closePopup);
    backdrop?.addEventListener("click", closePopup);

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape" && !popup.classList.contains("hidden")) {
            closePopup();
        }
    });
})();
