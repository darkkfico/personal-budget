window.formatMoney = function (amount, currency) {
    const code = String(currency || "").toUpperCase();

    if (code === "EUR") {
        return "€" + amount;
    }

    if (code === "USD") {
        return "$" + amount;
    }

    return `${amount} ${currency || ""}`.trim();
};
