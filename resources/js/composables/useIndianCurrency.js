const formatter = new Intl.NumberFormat('en-IN', {
    style: 'currency',
    currency: 'INR',
    minimumFractionDigits: 2,
});

export function formatINR(value) {
    const num = parseFloat(value);
    if (isNaN(num)) {
        return '';
    }
    return formatter.format(num);
}
