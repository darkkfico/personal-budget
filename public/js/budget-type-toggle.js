const typeAuto = document.getElementById("typeAuto");
const typeCustom = document.getElementById("typeCustom");
const autoForm = document.getElementById("autoChangeForm");
const customForm = document.getElementById("customChangeForm");

function toggleBudgetTypeForms() {
    const isAuto = typeAuto?.checked;

    if (autoForm) {
        autoForm.classList.toggle("hidden", !isAuto);
    }

    if (customForm) {
        customForm.classList.toggle("hidden", isAuto);
    }
}

const sectionsPopup = document.getElementById("customSectionsPopup");
const sectionsPopupGotIt = document.getElementById("customSectionsGotIt");
const sectionsPopupBackdrop = document.getElementById("customSectionsBackdrop");

function openSectionsPopup() {
    sectionsPopup?.classList.remove("hidden");
}

function closeSectionsPopup() {
    sectionsPopup?.classList.add("hidden");
}

const autoConvertPopup = document.getElementById("autoConvertPopup");
const autoConvertGotIt = document.getElementById("autoConvertGotIt");
const autoConvertBackdrop = document.getElementById("autoConvertBackdrop");

function openAutoConvertPopup() {
    autoConvertPopup?.classList.remove("hidden");
}

function closeAutoConvertPopup() {
    autoConvertPopup?.classList.add("hidden");
}

typeAuto?.addEventListener("change", toggleBudgetTypeForms);
typeCustom?.addEventListener("change", toggleBudgetTypeForms);
typeCustom?.addEventListener("click", openSectionsPopup);
typeAuto?.addEventListener("click", openAutoConvertPopup);
sectionsPopupGotIt?.addEventListener("click", closeSectionsPopup);
sectionsPopupBackdrop?.addEventListener("click", closeSectionsPopup);
autoConvertGotIt?.addEventListener("click", closeAutoConvertPopup);
autoConvertBackdrop?.addEventListener("click", closeAutoConvertPopup);

const hasCustomErrors = document.querySelector("#customChangeForm .text-red-600");
const hasAutoErrors = document.querySelector("#autoChangeForm .text-red-600");

if (hasCustomErrors && typeCustom) {
    typeCustom.checked = true;
} else if (hasAutoErrors && typeAuto) {
    typeAuto.checked = true;
}

toggleBudgetTypeForms();

if (sectionsPopup?.dataset.showOnLoad === "1" || hasCustomErrors) {
    openSectionsPopup();
}

if (hasAutoErrors) {
    openAutoConvertPopup();
}
