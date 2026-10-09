document.addEventListener('click', function (event) {
    const printButton = event.target.closest(
        '[data-action="print-receipt"]'
    );

    if (!printButton) {
        return;
    }

    window.print();
});