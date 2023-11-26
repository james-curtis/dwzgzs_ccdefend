<?php
/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2018/7/20
 * Time: 20:32
 */
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

function import_func($name) {
    return require_once DISCUZ_ROOT.'source/plugin/dwzgzs_ccdefend/'.$name.'.php';
}

/*function getip() {
    if ($_SERVER['HTTP_X_FORWARDED_FOR'])
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif ($_SERVER['HTTP_CLIENT_IP']) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif ($_SERVER['REMOTE_ADDR']) {
        $ip = $_SERVER['REMOTE_ADDR'];
    } else {
        $ip = 'Unknown';
    }
    return $ip;
}*/

function getua() {
    return $_SERVER['HTTP_USER_AGENT'];
}

function gettime() {
    return $_SERVER['REQUEST_TIME'];
}

function httpStatus($num){//网页返回码
    static $http = array (
        100 => "HTTP/1.1 100 Continue",
        101 => "HTTP/1.1 101 Switching Protocols",
        200 => "HTTP/1.1 200 OK",
        201 => "HTTP/1.1 201 Created",
        202 => "HTTP/1.1 202 Accepted",
        203 => "HTTP/1.1 203 Non-Authoritative Information",
        204 => "HTTP/1.1 204 No Content",
        205 => "HTTP/1.1 205 Reset Content",
        206 => "HTTP/1.1 206 Partial Content",
        300 => "HTTP/1.1 300 Multiple Choices",
        301 => "HTTP/1.1 301 Moved Permanently",
        302 => "HTTP/1.1 302 Found",
        303 => "HTTP/1.1 303 See Other",
        304 => "HTTP/1.1 304 Not Modified",
        305 => "HTTP/1.1 305 Use Proxy",
        307 => "HTTP/1.1 307 Temporary Redirect",
        400 => "HTTP/1.1 400 Bad Request",
        401 => "HTTP/1.1 401 Unauthorized",
        402 => "HTTP/1.1 402 Payment Required",
        403 => "HTTP/1.1 403 Forbidden",
        404 => "HTTP/1.1 404 Not Found",
        405 => "HTTP/1.1 405 Method Not Allowed",
        406 => "HTTP/1.1 406 Not Acceptable",
        407 => "HTTP/1.1 407 Proxy Authentication Required",
        408 => "HTTP/1.1 408 Request Time-out",
        409 => "HTTP/1.1 409 Conflict",
        410 => "HTTP/1.1 410 Gone",
        411 => "HTTP/1.1 411 Length Required",
        412 => "HTTP/1.1 412 Precondition Failed",
        413 => "HTTP/1.1 413 Request Entity Too Large",
        414 => "HTTP/1.1 414 Request-URI Too Large",
        415 => "HTTP/1.1 415 Unsupported Media Type",
        416 => "HTTP/1.1 416 Requested range not satisfiable",
        417 => "HTTP/1.1 417 Expectation Failed",
        500 => "HTTP/1.1 500 Internal Server Error",
        501 => "HTTP/1.1 501 Not Implemented",
        502 => "HTTP/1.1 502 Bad Gateway",
        503 => "HTTP/1.1 503 Service Unavailable",
        504 => "HTTP/1.1 504 Gateway Time-out"
    );
    dheader($http[$num]);
    dheader("Content-Length: 0");  //告诉浏览器数据长度,浏览器接收到此长度数据后就不再接收数据
    dheader("Connection: Close");      //告诉浏览器关闭当前连接,即为短连接
    exit();
}

function getSetting() {
    global $_G;
    require_once libfile('function/cache');
    loadcache('plugin');
    $set = $_G['cache']['plugin']['dwzgzs_ccdefend'];
    $set['disip'] = explode("\n",$set['disip']);
    $set['allow_ip'] = explode("\n",$set['allow_ip']);
    $set['allow_uid'] = explode('|',$set['allow_uid']);
    $set['allow_forums'] = dunserialize($set['allow_forums']);
    $set['allow_group'] = dunserialize($set['allow_group']);
    $set['allow_url'] = explode("\n",$set['allow_url']);
    $set['list_ua'] = explode("\n",$set['list_ua']);
    $set['dis_time'] = dintval($set['dis_time']);
    $set['on_ua'] = dintval($set['on_ua']);
    return $set;
}

//获取用户IP地址
function getip($cdn)
{

    if(!empty($_SERVER["HTTP_CLIENT_IP"]))
    {
        $cip = $_SERVER["HTTP_CLIENT_IP"];
    }
    else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"]) && $cdn == true)
    {
        $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    }
    else if(!empty($_SERVER["REMOTE_ADDR"]) && $cdn == false)
    {
        $cip = $_SERVER["REMOTE_ADDR"];
    }
    else
    {
        $cip = '';
    }
    preg_match("/[\d\.]{7,15}/", $cip, $cips);
    $cip = isset($cips[0]) ? $cips[0] : 'unknown';
    unset($cips);

    return $cip;
}


function is_spider($ua,$ip)
{
    require_once DISCUZ_ROOT.'/source/plugin/dwzgzs_ccdefend/config/spider_ua.php';
    require_once DISCUZ_ROOT.'/source/plugin/dwzgzs_ccdefend/config/spider_ip.php';
    $uas = get_spider_ua();
    $ips = get_spider_ip();
    foreach ($uas as $k => $v) {
        if ($k == 'safe360')
        {
            foreach ($v as $item) {
                if (stripos($ua,$item) !== false)
                {
                    foreach ($ips[$k] as $vv) {
                        if (preg_match($vv,$ip) != false)
                        {
                            return true;
                        }
                    }
                }
            }
        } else {
            if (stripos($ua,$v) !== false)
            {
                foreach ($ips[$k] as $vv) {
                    if (preg_match($vv,$ip) != false)
                    {
                        return true;
                    }
                }
            }
        }
    }

    return false;
}
