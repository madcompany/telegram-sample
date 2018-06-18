<?php
if (!function_exists('public_path')) {
    /**
     * Return the path to public dir
     *
     * @param null $path
     *
     * @return string
     */
    function public_path($path = null)
    {
        return rtrim(app()->basePath('public/' . $path), '/');
    }
}


if (!function_exists('make_result')) {

    function make_result($status = true, $message = '정상적으로 처리되었습니다. ')
    {
        if($status === true){
            return [ 'data' => [ 'result' => true,'message' => $message] ];
        }else{
            return [ 'errors' => [ 'message' => $message ]];
        }
    }
}

if (!function_exists('uuid')) {
    function uuid()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}

if (!function_exists('pr')) {
    function pr($data, $continue = true)
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";

        if(!$continue) exit;
    }
}

if (!function_exists('now')) {
    function now()
    {
        return date('Y-m-d');
    }
}


if (!function_exists('get_hashtag')) {
    /**
     * 해쉬태그 추출
     * @param $string
     * @return mixed
     */
    function get_hashtag($string)
    {
        $matches = array();
        preg_match_all('/#\S*\w/i', $string, $matches);
        return $matches[0];
    }
}

/**
 * Get hearder Authorization
 * */
function getAuthorizationHeader(){
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    }
    else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
        $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
        //print_r($requestHeaders);
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    return $headers;
}
/**
 * get access token from header
 * */
function getBearerToken() {
    $headers = getAuthorizationHeader();
    // HEADER: Get the access token from the header
    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
    }
    return null;
}

/**
 * 로그 추적용 ID
 */
function getTraceId()
{
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

        // 32 bits for "time_low"
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),

        // 16 bits for "time_mid"
        mt_rand(0, 0xffff),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand(0, 0x0fff) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand(0, 0x3fff) | 0x8000,

        // 48 bits for "node"
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

function get_http_response_code($theURL) {
    $headers = get_headers($theURL);
    return substr($headers[0], 9, 3);
}

/**
 * CURL
 * @param $sUrl
 * @param array $aPostField
 * @return mixed
 */
function curlPost($sUrl, $aPostField=array())
{
    $oCurl = curl_init();
    curl_setopt($oCurl, CURLOPT_URL, trim($sUrl));
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($oCurl, CURLOPT_POST, true);
    curl_setopt($oCurl, CURLOPT_POSTFIELDS, $aPostField);
    $sResponse = curl_exec($oCurl);

    $aCurlInfo['info'] = (count($aPostField) > 0 ? array_merge(array('postfield'=>$aPostField), curl_getinfo($oCurl)) : curl_getinfo($oCurl));
    $aCurlInfo['error_no'] = curl_errno($oCurl);
    $aCurlInfo['error_msg'] = curl_error($oCurl);

    curl_close($oCurl);

    if(!empty($sResponse)){
        return $sResponse;
    }
}

function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}


