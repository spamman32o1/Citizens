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

$country = null;
$countryCode = null;
$city = null;
$query = null;
$details = null;

$getdetails = "https://extreme-ip-lookup.com/json/" . getIp() . "";

if (h4z3_can_use_curl()) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $getdetails);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    $content = curl_exec($curl);
    curl_close($curl);

    if ($content !== false) {
        $decoded = json_decode($content);
        if (is_object($decoded)) {
            $details = $decoded;
        }
    }
} else {
    $fallbackContent = @file_get_contents($getdetails);
    if ($fallbackContent !== false) {
        $decoded = json_decode($fallbackContent);
        if (is_object($decoded)) {
            $details = $decoded;
        }
    }
}

if (is_object($details)) {
    $country = property_exists($details, 'country') ? $details->country : null;
    $countryCode = property_exists($details, 'countryCode') ? $details->countryCode : null;
    $city = property_exists($details, 'city') ? $details->city : null;
    $query = property_exists($details, 'query') ? $details->query : null;
}

$_SESSION['country'] = $country;
$_SESSION['countrycode'] = $countryCode;
$_SESSION['city'] = $city;
$_SESSION['ip'] = $query;

?>