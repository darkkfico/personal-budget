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
    const colors = ["#004d40", "#00796b", "#ff6f00", "#2e7d32", "#558b2f", "#00695c", "#ef6c00", "#33691e"];

    function formatAmount(value, currency) {
        return `${value} ${currency}`;
    }

    function conicGradient(values) {
        const total = values.reduce((sum, value) => sum + Number(value), 0);

        if (total <= 0) {
            return "#b2dfdb";
        }

        let start = 0;

        return values
            .map((value, index) => {
                const slice = (Number(value) / total) * 360;
                const end = start + slice;
                const stop = `${colors[index % colors.length]} ${start}deg ${end}deg`;
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

        const values = month.sections.map((section) => Number(section.spent));

        pieEl.style.background = `conic-gradient(${conicGradient(values)})`;

        summaryEl.innerHTML = `
            <span>Budget: ${formatAmount(month.budget, month.currency)}</span>
            <span>Total spent: ${formatAmount(month.spent, month.currency)}</span>
            <span>Left at month end: ${formatAmount(month.left, month.currency)}</span>
        `;

        legendEl.innerHTML = month.sections
            .map((section, index) => {
                const leftover = Number(section.allocated) - Number(section.spent);
                return `
                    <li class="flex items-start gap-3 bg-lightbutter/60 rounded-xl px-4 py-3">
                        <span class="mt-1 size-3 rounded-full shrink-0" style="background:${colors[index % colors.length]}"></span>
                        <div class="min-w-0 text-sm text-secondary">
                            <p class="font-bold">${section.name}</p>
                            <p>Allocated: ${formatAmount(section.allocated, month.currency)}</p>
                            <p>Spent: ${formatAmount(section.spent, month.currency)}</p>
                            <p>Left: ${formatAmount(leftover, month.currency)}</p>
                        </div>
                    </li>
                `;
            })
            .join("");
    }

    monthSelect.addEventListener("change", () => render(monthSelect.value));
    render(monthSelect.value);
})();
