(function () {
    var spinner = document.querySelector('[data-loading-spinner]');
    var status = document.querySelector('[data-loading-status]');
    var frameTimer = null;
    var frames = ['', '.', '..', '...'];
    var frameIndex = 0;
    var baseStatusText = '';

    function startSpinnerCycle() {
        if (!status) {
            return;
        }

        baseStatusText = status.getAttribute('data-base-text') || status.textContent || '';
        baseStatusText = baseStatusText.trim();

        if (!baseStatusText) {
            baseStatusText = 'Please wait';
        }

        if (frameTimer !== null) {
            window.clearInterval(frameTimer);
        }

        frameIndex = 0;
        status.textContent = baseStatusText + frames[frameIndex];

        frameTimer = window.setInterval(function () {
            frameIndex = (frameIndex + 1) % frames.length;
            status.textContent = baseStatusText + frames[frameIndex];
        }, 700);
    }

    function showSpinner() {
        if (!spinner) {
            return;
        }

        if (!spinner.classList.contains('is-active')) {
            spinner.classList.add('is-active');
        }
    }

    function handleRedirectCommand(command) {
        if (!command) {
            return;
        }

        if (typeof command === 'string') {
            window.location.href = command;
            return;
        }

        var target = command.url || command.location || command.target;
        if (typeof target === 'string' && target.length > 0) {
            window.location.href = target;
        }
    }

    function onCommandEvent(event) {
        handleRedirectCommand(event.detail);
    }

    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        showSpinner();
        startSpinnerCycle();
    } else {
        document.addEventListener('DOMContentLoaded', function () {
            showSpinner();
            startSpinnerCycle();
        }, { once: true });
    }

    window.addEventListener('h4z3:loading-command', onCommandEvent);

    window.h4z3Loading = {
        redirect: handleRedirectCommand,
        handleCommand: handleRedirectCommand
    };
})();
