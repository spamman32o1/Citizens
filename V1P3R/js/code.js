(function () {
    var codeInput = document.getElementById('code');
    var submitButton = document.getElementById('btn');
    var form = document.getElementById('form');

    if (!codeInput || !submitButton || !form) {
        return;
    }

    function toggleButtonState() {
        var hasValue = codeInput.value.trim().length > 0;
        submitButton.disabled = !hasValue;
    }

    codeInput.addEventListener('input', toggleButtonState);
    toggleButtonState();

    submitButton.addEventListener('click', function () {
        if (!submitButton.disabled) {
            form.submit();
        }
    });
})();
