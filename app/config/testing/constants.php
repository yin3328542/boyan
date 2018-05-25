<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

/*
|--------------------------------------------------------------------------
| REST_FULL应用证书
|--------------------------------------------------------------------------
*/
$base_domain = getdomain($_SERVER['HTTP_HOST']);
define('REST_SERVER', 'http://pc.'. $base_domain .'/api/');
define('APP_KEY', 'Pcu78fJ7rhs52Ki8');
define('APP_SECRET', 'Pc9diejudh7362109kdius76hrG5e7DU');

define('APPTYPE_ADMIN', 0);
define('APPTYPE_XCX', 1);
define('APPTYPE_PC', 7); //PC

define('PATH_WWW_ROOT','http://www.zuanla.com.cn');
define('PATH_ASSESTS','/assets');
define('PREFIX_CACHE_KEY','KR_PC_CACHE_');
define('IMG_SERVER_FOLDER', 'data');

$base_domain = getdomain($_SERVER['HTTP_HOST']);
define('BASE_DOMAIN', $base_domain);
define('IMAGE_SERVER', 'http://www.zuanla.com.cn');

function getdomain($url) {
    $host = strtolower ( $url );
    if (strpos ( $host, '/' ) !== false) {
        $parse = @parse_url ( $host );
        $host = $parse ['host'];
    }
    $topleveldomaindb = array ('com', 'edu', 'gov', 'int', 'mil', 'net', 'org', 'biz', 'info', 'pro', 'name', 'museum', 'coop', 'aero', 'xxx', 'idv', 'mobi', 'cc', 'me' );
    $str = '';
    foreach ( $topleveldomaindb as $v ) {
        $str .= ($str ? '|' : '') . $v;
    }

    $matchstr = "[^\.]+\.(?:(" . $str . ")|\w{2}|((" . $str . ")\.\w{2}))$";
    if (preg_match ( "/" . $matchstr . "/ies", $host, $matchs )) {
        $domain = $matchs ['0'];
    } else {
        $domain = $host;
    }
    return $domain;
}
