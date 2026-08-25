const sections = document.querySelector("#sections");
const addSectionBtn = document.getElementById("addSection");

function getSectionBlocks() {
    return sections ? [...sections.querySelectorAll("[data-section]")] : [];
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
        const amountInput = block.querySelector('[data-role="amount"]');
        const removeBtn = block.querySelector(".remove-section");

        if (nameInput) {
            nameInput.name = `custom-field${number}`;
            nameInput.placeholder = number === 1
                ? "Name of section 1, eg Groceries..."
                : `Name of section ${number}`;
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
        <div class="relative inline-block w-full">
            <input type="number" name="custom-field${number}-amount" data-role="amount" min="0" max="100"
                placeholder="Percentage of section ${number}"
                class="w-full bg-transparent border-b-2 border-b-secondary px-3 py-3 text-md text-secondary focus:scale-[102%] outline-none focus:outline-none focus:transtition focus:duration-500 placeholder:text-secondary/60 placeholder:font-semiboldpercentInput">
            <span class="pointer-events-none absolute right-10 top-1/2 -translate-y-1/2 text-secondary/60">%</span>
        </div>
        <button type="button" class="remove-section inline-flex items-center gap-2 rounded-xl border-2 border-accent bg-accent/15 px-4 py-2 text-base font-bold text-accent cursor-pointer hover:bg-accent hover:text-butter transition">
            <i class="fa-solid fa-trash-can text-sm pointer-events-none"></i>
            <span class="pointer-events-none">Remove section</span>
        </button>
    </div>`;
}

function removeSection(sectionBlock) {
    if (!sectionBlock || getSectionBlocks().length <= 1) {
        return;
    }

    sectionBlock.remove();
    renumberSections();
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
    removeSection(removeBtn.closest("[data-section]"));
});

renumberSections();
