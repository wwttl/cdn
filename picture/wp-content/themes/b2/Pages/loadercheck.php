<?php

if(!current_user_can('administrator')) wp_die('您无权访问此页');
ini_set("display_errors", "On");
error_reporting(E_ALL);
restore_exception_handler();
restore_error_handler();
date_default_timezone_set('Asia/Shanghai');

// Set constants
define('WIZARD_VERSION', '2.0.2');
define('WIZARD_DEFAULT_LANG', 'zh-cn');
define('WIZARD_OPTIONAL_LANG', 'zh-cn,en');
define('WIZARD_NAME_ZH', 'Swoole Compiler Loader安装助手');
define('WIZARD_NAME_EN', 'Swoole Compiler Loader Wizard');
define('WIZARD_DEFAULT_RUN_MODE', 'web');
define('WIZARD_OPTIONAL_RUN_MODE', 'cli,web');
define('WIZARD_DEFAULT_OS', 'linux');
define('WIZARD_OPTIONAL_OS', 'linux,windows');
define('WIZARD_BASE_API', 'http://compiler.swoole.com');

// Language items
$languages['zh-cn'] = [
    'title' => 'Swoole Compiler Loader 安装助手',
];
$languages['en'] = [
    'title' => 'Swoole Compiler Loader Wizard',
];

// Set env variable for current environment
$env = [];
// Check os type
$env['os'] = [];
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $env['os']['name'] = "windows";
    $env['os']['raw_name'] = php_uname();
} else {
    $env['os']['name'] = "unix";
    $env['os']['raw_name'] = php_uname();
}
// Check php
$env['php'] = [];
$env['php']['version'] = phpversion();
// Check run mode
$sapi_type = php_sapi_name();
if ( "cli" == $sapi_type) {
    $env['php']['run_mode'] = "cli";
} else {
    $env['php']['run_mode'] = "web";
}
// Check php bit
if (PHP_INT_SIZE == 4) {
    $env['php']['bit'] = 32;
} else {
    $env['php']['bit'] = 64;
}
$env['php']['sapi'] = $sapi_type;
$env['php']['ini_loaded_file'] = php_ini_loaded_file();
$env['php']['ini_scanned_files'] = php_ini_scanned_files();
$env['php']['loaded_extensions'] = get_loaded_extensions();
$env['php']['incompatible_extensions'] = ['xdebug', 'ionCube', 'zend_loader'];
$env['php']['loaded_incompatible_extensions'] = [];
$env['php']['extension_dir'] = ini_get('extension_dir');
// Check incompatible extensions
if (is_array($env['php']['loaded_extensions'])) {
    foreach($env['php']['loaded_extensions'] as $loaded_extension) {
        foreach($env['php']['incompatible_extensions'] as $incompatible_extension) {
            if (strpos(strtolower($loaded_extension), strtolower($incompatible_extension)) !== false) {
                $env['php']['loaded_incompatible_extensions'][] = $loaded_extension;
            }
        }
    }
}
$env['php']['loaded_incompatible_extensions'] = array_unique($env['php']['loaded_incompatible_extensions']);
// Parse System Environment Info
$sysInfo = w_getSysInfo();
// Check php thread safety
$env['php']['raw_thread_safety'] = isset($sysInfo['thread_safety']) ? $sysInfo['thread_safety'] : false;
if (isset($sysInfo['thread_safety'])) {
    $env['php']['thread_safety'] = $sysInfo['thread_safety'] ? '线程安全' : '非线程安全';
} else {
    $env['php']['thread_safety'] = '未知';
}
// Check swoole loader installation
if (isset($sysInfo['swoole_loader']) and isset($sysInfo['swoole_loader_version'])) {
    $env['php']['swoole_loader']['status']  = $sysInfo['swoole_loader'] ? "<span style='color: #007bff;'>已安装</span>"
        : '未安装';
    if ($sysInfo['swoole_loader_version'] !== false) {
        $env['php']['swoole_loader']['version'] = "<span style='color: #007bff;'>". $sysInfo['swoole_loader_version'] ."</span>";
    } else {
        $env['php']['swoole_loader']['version'] = '未知';
    }
} else {
    $env['php']['swoole_loader']['status']  = '未安装';
    $env['php']['swoole_loader']['version'] =  '未知';
}
/**
 *  Web mode
 */
if ('web' == $env['php']['run_mode']) {
    $language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 4);
    if (preg_match("/zh-c/i", $language)) {
        $env['lang'] = "zh-cn";
        $wizard_lang = $env['lang'];
    } else {
        $env['lang'] = "en";
        $wizard_lang = $env['lang'];
    }
    $html = '';
    // Header
    $html_header = '<!doctype html>
	<html lang="en">
	  <head>
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<!-- Bootstrap CSS -->
		<link href="https://lib.baomitu.com/twitter-bootstrap/4.1.0/css/bootstrap.min.css" rel="stylesheet">
		<title>%s</title>
		<style>
			.list_info {display: inline-block; width: 12rem;}
			.bold_text {font-weight: bold;}
			.code {color:#007bff;font-size: medium;}
		</style>
	  </head>
	  <body class="bg-light"> 
	  ';
    $html_header = sprintf($html_header, $languages[$wizard_lang]['title']);
    $html_body = '<div class="container">';
    $html_body_nav = '<div class="py-5 text-center"  style="padding-bottom: 1rem!important;">';
    $html_body_nav .= '<h2>环境检测</h2>';
    $html_body_nav .= '<p class="lead"> Version:2.0.4 Date:2020-12-31</p>';
    $html_body_nav .=  '</div><hr>';

    // Environment information
    $html_body_environment = '
	<div class="col-12"  style="padding-top: 1rem!important;">
		<h5 class="text-center">检查当前环境</h5>
		<ul class="list-unstyled text-small">';
    $html_body_environment .= '<li><span class="list_info">操作系统 : </span>' . $env['os']['raw_name'] . '</li>';
    $html_body_environment .= '<li><span class="list_info">PHP版本 : </span>' . $env['php']['version'] . '</li>';
    $html_body_environment .= '<li><span class="list_info">PHP运行环境 : </span>' . $env['php']['sapi'] . '</li>';
    $html_body_environment .= '<li><span class="list_info">PHP配置文件 : </span>'  . $env['php']['ini_loaded_file'] . '</li>';
    $html_body_environment .= '<li><span class="list_info">PHP是否线程安全 : </span>' . $env['php']['thread_safety'] . '</li>';
    $html_body_environment .= '<li><span class="list_info">是否安装了扩展 : </span>' . $env['php']['swoole_loader']['status'] . '</li>';
    if (isset($sysInfo['swoole_loader']) and $sysInfo['swoole_loader']) {
        $html_body_environment .= '<li><span class="list_info">扩展版本 : </span>' . $env['php']['swoole_loader']['version'] . '</li>';
    }
    if ($env['php']['bit'] == 32) {
        $html_body_environment .= '<li><span style="color:red">温馨提示：当前环境使用的PHP为 ' . $env['php']['bit'] . ' 位的PHP，Compiler 目前不支持 Debug 版本或 32 位的PHP，可在 phpinfo() 中查看对应位数，如果误报请忽略此提示</span></li>';
    }
    $html_body_environment .= '	</ul></div>';

    // Error infomation
    $html_error = "";

    if(extension_loaded('swoole_loader')){
        $ext = new \ReflectionExtension('swoole_loader');

        if($ext->getVersion() !== '2.2.9'){
            $html_error = '<hr>
                <div class="col-12"  style="padding-top: 1rem!important;">
                <h5 class="text-center" style="color:red">错误信息</h5>
                <p class="text-center" style="color:red">当前主题需要新版的扩展，请升级您的扩展</p>
            </div>';
        }    
    }
  
    if (!empty($env['php']['loaded_incompatible_extensions'])) {
        $html_error = '<hr>
		<div class="col-12"  style="padding-top: 1rem!important;">
		<h5 class="text-center" style="color:red">错误信息</h5>
		<p class="text-center" style="color:red">%s</p>
    </div>
		';
        $err_msg = "当前PHP包含与B2扩展不兼容的扩展" . implode(',', $env['php']['loaded_incompatible_extensions']) . "，请移除不兼容的扩展。";
        $html_error = sprintf($html_error, $err_msg);
    }
    

    // Body footer
    $html_body_footer = '<footer class="my-5 pt-5 text-muted text-center text-small">

  </footer>';
    $html_body .= $html_body_nav . '<div class="row">' . $html_body_environment . $html_error . '</div>' . $html_body_footer;
    $html_body .= '</div>';
    // Footer
    $html_footer = '
		<script src="https://lib.baomitu.com/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://lib.baomitu.com/axios/0.18.0/axios.min.js"></script>
		<script src="https://lib.baomitu.com/twitter-bootstrap/4.1.0/js/bootstrap.min.js"></script>
		</body>
	</html>';
    // Make full html
    $html = $html_header .  $html_body . $html_footer;
    // Output html content
    //ob_start();
    echo $html;
    //ob_end_clean();
    //die();
}

/**
 * Cli mode
 */
if ( "cli" == $env['php']['run_mode'] ) {

}

/**
 * Useful functions
 */
// Dump detail of variable
function w_dump($var) {
    if(is_object($var) and $var instanceof Closure) {
        $str    = 'function (';
        $r      = new ReflectionFunction($var);
        $params = array();
        foreach($r->getParameters() as $p) {
            $s = '';
            if($p->isArray()) {
                $s .= 'array ';
            } else if($p->getClass()) {
                $s .= $p->getClass()->name . ' ';
            }
            if($p->isPassedByReference()){
                $s .= '&';
            }
            $s .= '$' . $p->name;
            if($p->isOptional()) {
                $s .= ' = ' . var_export($p->getDefaultValue(), TRUE);
            }
            $params []= $s;
        }
        $str .= implode(', ', $params);
        $str .= '){' . PHP_EOL;
        $lines = file($r->getFileName());
        for($l = $r->getStartLine(); $l < $r->getEndLine(); $l++) {
            $str .= $lines[$l];
        }
        echo $str;
        return;
    } else if(is_array($var)) {
        echo "<xmp class='a-left'>";
        print_r($var);
        echo "</xmp>";
        return;
    } else {
        var_dump($var);
        return;
    }
}
// Parse verion of php
function w_parse_version($version) {
    $versionList = [];
    if (is_string($version)) {
        $rawVersionList = explode('.', $version);
        if (isset($rawVersionList[0])) {
            $versionList[] = $rawVersionList[0];
        }
        if (isset($rawVersionList[1])) {
            $versionList[] = $rawVersionList[1];
        }
    }
    return $versionList;
}
function w_getSysInfo() {
    global $env;
    $sysEnv = [];
    // Get content of phpinfo
    ob_start();
    phpinfo();
    $sysInfo = ob_get_contents();
    ob_end_clean();
    // Explode phpinfo content
    if ($env['php']['run_mode'] == 'cli') {
        $sysInfoList = explode('\n', $sysInfo);
    } else {
        $sysInfoList = explode('</tr>', $sysInfo);
    }
    foreach($sysInfoList as $sysInfoItem) {
        if (preg_match('/thread safety/i', $sysInfoItem)) {
            $sysEnv['thread_safety'] = (preg_match('/(enabled|yes)/i', $sysInfoItem) != 0);
        }
        if (preg_match('/swoole_loader support/i', $sysInfoItem)) {
            $sysEnv['swoole_loader'] = (preg_match('/(enabled|yes)/i', $sysInfoItem) != 0);
        }
        if (preg_match('/swoole_loader version/i', $sysInfoItem)) {
            preg_match('/\d+.\d+.\d+/s', $sysInfoItem, $match);
            $sysEnv['swoole_loader_version'] = isset($match[0]) ? $match[0] : false;
        }
    }
    //var_dump($sysEnv);die();
    return $sysEnv;
}