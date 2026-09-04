const sections = document.querySelector("#sections");
const addSectionBtn = document.getElementById("addSection");

function formRoot() {
    return sections?.closest("form") ?? null;
}

function getSectionBlocks() {
    return sections ? [...sections.querySelectorAll("[data-section]")] : [];
}

function getBudget() {
    const value = parseFloat(formRoot()?.querySelector('[name="budget"]')?.value);

    return Number.isFinite(value) && value > 0 ? value : 0;
}

function roundMoney(value) {
    const currency = formRoot()?.querySelector('[name="currency"]')?.value;

    if (currency === "MKD") {
        return Math.round(value);
    }

    return Math.round(value * 100) / 100;
}

function roundPercent(value) {
    return Math.round(value * 100) / 100;
}

function syncMoneyFromPercent(block) {
    const moneyInput = block.querySelector('[data-role="money"]');
    const percentInput = block.querySelector('[data-role="amount"]');
    const budget = getBudget();

    if (!moneyInput || !percentInput) {
        return;
    }

    if (percentInput.value === "") {
        moneyInput.value = "";
        return;
    }

    const percent = parseFloat(percentInput.value);

    if (!Number.isFinite(percent) || !budget) {
        return;
    }

    moneyInput.value = String(roundMoney((percent / 100) * budget));
}

function syncPercentFromMoney(block) {
    const moneyInput = block.querySelector('[data-role="money"]');
    const percentInput = block.querySelector('[data-role="amount"]');
    const budget = getBudget();

    if (!moneyInput || !percentInput) {
        return;
    }

    if (moneyInput.value === "") {
        percentInput.value = "";
        return;
    }

    const money = parseFloat(moneyInput.value);

    if (!Number.isFinite(money) || !budget) {
        return;
    }

    percentInput.value = String(roundPercent((money / budget) * 100));
}

function syncAllMoneyFromPercent() {
    getSectionBlocks().forEach(syncMoneyFromPercent);
}

function setRemoveButtonVisibility(removeBtn, visible) {
    if (!removeBtn) return;

    if (visible) {
        removeBtn.classList.remove("hidden");
        removeBtn.classList.add("inline-flex");
    } else {
        removeBtn.classList.add("hidden");
        removeBtn.classList.remove("inline-flex");
    }
}

function renumberSections() {
    const blocks = getSectionBlocks();

    blocks.forEach((block, index) => {
        const number = index + 1;
        const nameInput = block.querySelector('[data-role="name"]');
        const moneyInput = block.querySelector('[data-role="money"]');
        const amountInput = block.querySelector('[data-role="amount"]');
        const removeBtn = block.querySelector(".remove-section");

        if (nameInput) {
            nameInput.name = `custom-field${number}`;
            nameInput.placeholder = number === 1
                ? "Name of section 1, eg Groceries..."
                : `Name of section ${number}`;
        }

        if (moneyInput) {
            moneyInput.name = `custom-field${number}-money`;
            moneyInput.placeholder = `Amount of section ${number}`;
        }

        if (amountInput) {
            amountInput.name = `custom-field${number}-amount`;
            amountInput.placeholder = `Percentage of section ${number}`;
        }

        setRemoveButtonVisibility(removeBtn, blocks.length > 1);
    });
}

function sectionHtml(number) {
    return `<div class="budget-section w-full space-y-6 animate-myanimation" data-section>
        <div class="w-full relative">
            <span class="text-red-500 absolute top-0 left-0">*</span>
            <input type="text" name="custom-field${number}" data-role="name"
                placeholder="${number === 1 ? "Name of section 1, eg Groceries..." : `Name of section ${number}`}"
                class="w-full bg-transparent border-b-2 border-b-secondary px-3 py-3 text-md text-secondary font-semiboldfocus:scale-[102%] outline-none focus:outline-none focus:transtition focus:duration-500 placeholder:text-secondary/70 placeholder:font-semibold">
        </div>
        <div class="flex w-full flex-col gap-6 lg:flex-row lg:items-start lg:gap-8">
            <div class="relative w-full lg:flex-1">
                <input type="number" name="custom-field${number}-money" data-role="money" min="0" step="any"
                    placeholder="Amount of section ${number}"
                    class="w-full bg-transparent border-b-2 border-b-secondary px-3 py-3 text-md text-secondary focus:scale-[102%] outline-none focus:outline-none focus:transtition focus:duration-500 placeholder:text-secondary/60 placeholder:font-semibold">
            </div>
            <div class="relative w-full lg:flex-1">
                <input type="number" name="custom-field${number}-amount" data-role="amount" min="0" max="100" step="any"
                    placeholder="Percentage of section ${number}"
                    class="w-full bg-transparent border-b-2 border-b-secondary px-3 py-3 text-md text-secondary focus:scale-[102%] outline-none focus:outline-none focus:transtition focus:duration-500 placeholder:text-secondary/60 placeholder:font-semibold">
                <span class="pointer-events-none absolute right-10 top-1/2 -translate-y-1/2 text-secondary/60 lg:right-4">%</span>
            </div>
        </div>
        <button type="button" class="remove-section inline-flex items-center gap-2 rounded-xl border-2 border-accent bg-accent/15 px-4 py-2 text-base font-bold text-accent cursor-pointer hover:bg-accent hover:text-butter transition">
            <i class="fa-solid fa-trash-can text-sm pointer-events-none"></i>
            <span class="pointer-events-none">Remove section</span>
        </button>
    </div>`;
}

addSectionBtn?.addEventListener("click", (e) => {
    e.preventDefault();
    if (!sections) return;

    sections.insertAdjacentHTML("beforeend", sectionHtml(getSectionBlocks().length + 1));
    renumberSections();
});

document.addEventListener("click", (e) => {
    const removeBtn = e.target.closest(".remove-section");
    if (!removeBtn) return;

    e.preventDefault();
    if (getSectionBlocks().length <= 1) return;

    removeBtn.closest("[data-section]")?.remove();
    renumberSections();
});

sections?.addEventListener("input", (e) => {
    const block = e.target.closest("[data-section]");

    if (!block) {
        return;
    }

    if (e.target.matches('[data-role="money"]')) {
        syncPercentFromMoney(block);
    }

    if (e.target.matches('[data-role="amount"]')) {
        syncMoneyFromPercent(block);
    }
});

formRoot()?.querySelector('[name="budget"]')?.addEventListener("input", syncAllMoneyFromPercent);
formRoot()?.querySelector('[name="currency"]')?.addEventListener("change", syncAllMoneyFromPercent);

function fillMissingPairedValues() {
    getSectionBlocks().forEach((block) => {
        const moneyInput = block.querySelector('[data-role="money"]');
        const percentInput = block.querySelector('[data-role="amount"]');

        if (!moneyInput || !percentInput || !getBudget()) {
            return;
        }

        if (moneyInput.value === "" && percentInput.value !== "") {
            syncMoneyFromPercent(block);
        } else if (percentInput.value === "" && moneyInput.value !== "") {
            syncPercentFromMoney(block);
        }
    });
}

renumberSections();
fillMissingPairedValues();
