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

typeAuto?.addEventListener("change", toggleBudgetTypeForms);
typeCustom?.addEventListener("change", toggleBudgetTypeForms);

const hasCustomErrors = document.querySelector("#customChangeForm .text-red-600");
const hasAutoErrors = document.querySelector("#autoChangeForm .text-red-600");

if (hasCustomErrors && typeCustom) {
    typeCustom.checked = true;
} else if (hasAutoErrors && typeAuto) {
    typeAuto.checked = true;
}

toggleBudgetTypeForms();
