(function () {
    const dataEl = document.getElementById("historyPieData");
    const pieEl = document.getElementById("historyPieChart");
    const monthSelect = document.getElementById("historyPieMonth");
    const summaryEl = document.getElementById("historyPieSummary");
    const legendEl = document.getElementById("historyPieLegend");

    if (!dataEl || !pieEl || !monthSelect) {
        return;
    }

    const months = JSON.parse(dataEl.textContent);
    const spentColors = ["#004d40", "#00796b", "#ff6f00", "#2e7d32", "#558b2f", "#00695c", "#ef6c00", "#33691e"];
    const leftoverColor = "#80cbc4";

    function formatAmount(value, currency) {
        return window.formatMoney(value, currency);
    }

    function conicGradient(slices) {
        const total = slices.reduce((sum, slice) => sum + Number(slice.value), 0);

        if (total <= 0) {
            return leftoverColor;
        }

        let start = 0;

        return slices
            .map((slice) => {
                const span = (Number(slice.value) / total) * 360;
                const end = start + span;
                const stop = `${slice.color} ${start}deg ${end}deg`;
                start = end;
                return stop;
            })
            .join(", ");
    }

    function render(monthName) {
        const month = months[monthName];

        if (!month) {
            return;
        }

        const leftover = Math.max(0, Number(month.left));
        const slices = month.sections
            .map((section, index) => ({
                name: section.name,
                value: Number(section.spent),
                color: spentColors[index % spentColors.length],
            }))
            .filter((slice) => slice.value > 0);

        if (leftover > 0) {
            slices.push({
                name: "Not spent",
                value: leftover,
                color: leftoverColor,
            });
        }

        pieEl.style.background = `conic-gradient(${conicGradient(slices)})`;

        summaryEl.innerHTML = `
            <span>Budget: ${formatAmount(month.budget, month.currency)}</span>
            <span>Total spent: ${formatAmount(month.spent, month.currency)}</span>
            <span>Left at month end: ${formatAmount(month.left, month.currency)}</span>
        `;

        const fieldLegend = month.sections
            .map((section, index) => {
                const fieldLeft = Number(section.allocated) - Number(section.spent);
                return `
                    <li class="flex items-start gap-3 bg-lightbutter/60 rounded-xl px-4 py-3">
                        <span class="mt-1 size-3 rounded-full shrink-0" style="background:${spentColors[index % spentColors.length]}"></span>
                        <div class="min-w-0 text-sm text-secondary">
                            <p class="font-bold">${section.name}</p>
                            <p>Allocated: ${formatAmount(section.allocated, month.currency)}</p>
                            <p>Spent: ${formatAmount(section.spent, month.currency)}</p>
                            <p>Left: ${formatAmount(fieldLeft, month.currency)}</p>
                        </div>
                    </li>
                `;
            })
            .join("");

        const leftoverLegend = leftover > 0
            ? `
                <li class="flex items-start gap-3 bg-lightbutter/60 rounded-xl px-4 py-3">
                    <span class="mt-1 size-3 rounded-full shrink-0" style="background:${leftoverColor}"></span>
                    <div class="min-w-0 text-sm text-secondary">
                        <p class="font-bold">Not spent</p>
                        <p>Left: ${formatAmount(leftover, month.currency)}</p>
                    </div>
                </li>
            `
            : "";

        legendEl.innerHTML = fieldLegend + leftoverLegend;
    }

    monthSelect.addEventListener("change", () => render(monthSelect.value));
    render(monthSelect.value);
})();
