<?php
//静态文件CDN加速
if ( !is_admin() ) {
add_action('wp_loaded','yuncai_ob_start');

function yuncai_ob_start() {
ob_start('yuncai_qiniu_cdn_replace');
}
function yuncai_qiniu_cdn_replace($html){
$local_host = 'https://www.wwttl.com'; //博客域名
$qiniu_host = 'https://cdn.wwttl.com'; //CDN域名
$cdn_exts = 'js|css|ttf|woff|tiff|bmp|pcx|tga|exif|fpx|psd|cdr|pcd|dxf|ufo|eps|ai|raw'; //扩展名（使用|分隔）
$cdn_dirs = 'wp-content|wp-includes|wp-admin'; //目录（使用|分隔）

$cdn_dirs = str_replace('-', '\-', $cdn_dirs);

if ($cdn_dirs) {
$regex = '/' . str_replace('/', '\/', $local_host) . '\/((' . $cdn_dirs . ')\/[^\s\?\\\'\"\;\>\<]{1,}.(' . $cdn_exts . '))([\"\\\'\s\?]{1})/';
$html = preg_replace($regex, $qiniu_host . '/$1$4', $html);
} else {
$regex = '/' . str_replace('/', '\/', $local_host) . '\/([^\s\?\\\'\"\;\>\<]{1,}.(' . $cdn_exts . '))([\"\\\'\s\?]{1})/';
$html = preg_replace($regex, $qiniu_host . '/$1$3', $html);
}
return $html;
}
}
//wordpress上传的图片保存到二级域名,找回上传设置
if(get_option('upload_path')=='wp-content/uploads' ||get_option('upload_path')==null) {
        update_option('upload_path',WP_CONTENT_DIR.'/uploads');
}
/*彻底关闭自动更新 关闭核心程序、主题、插件及翻译自动更新*/add_filter('automatic_updater_disabled', '__return_true');
 
/*关闭更新检查定时作业*/remove_action('init', 'wp_schedule_update_checks');
 
/*移除已有的版本检查定时作业*/wp_clear_scheduled_hook('wp_version_check');
/*移除已有的插件更新定时作业*/wp_clear_scheduled_hook('wp_update_plugins');
/*移除已有的主题更新定时作业*/wp_clear_scheduled_hook('wp_update_themes');
/*移除已有的自动更新定时作业*/wp_clear_scheduled_hook('wp_maybe_auto_update');
 
/*移除后台内核更新检查*/remove_action( 'admin_init', '_maybe_update_core' );
 
/*移除后台插件更新检查*/remove_action( 'load-plugins.php', 'wp_update_plugins' );
remove_action( 'load-update.php', 'wp_update_plugins' );
remove_action( 'load-update-core.php', 'wp_update_plugins' );
remove_action( 'admin_init', '_maybe_update_plugins' );
 
/*移除后台主题更新检查*/remove_action( 'load-themes.php', 'wp_update_themes' );
remove_action( 'load-update.php', 'wp_update_themes' );
remove_action( 'load-update-core.php', 'wp_update_themes' );
remove_action( 'admin_init', '_maybe_update_themes' ); 
/*关闭程序更新提示*/add_filter( 'pre_site_transient_update_core', function($a){ return null; });
/*关闭插件更新提示*/add_filter('pre_site_transient_update_plugins', function($a){return null;});
/*关闭主题更新提示*/add_filter('pre_site_transient_update_themes',  function($a){return null;});


/* WordPress注册邮箱白名单*/function is_valid_email_domain($login, $email, $errors ){
$valid_email_domains = array("gmail.com","qq.com","163.com");// 允许注册的邮箱信息
$valid = false;
foreach( $valid_email_domains as $d ){
$d_length = strlen( $d );
$current_email_domain = strtolower( substr( $email, -($d_length), $d_length));
if( $current_email_domain == strtolower($d) ){
$valid = true;
break;
}
}
// if invalid, return error message
if( $valid === false ){
$errors->add('domain_whitelist_error',__( '<strong>ERROR</strong>: 本站只支持gmail、QQ、网易邮箱注册。' ));
}
}
add_action('register_post', 'is_valid_email_domain',10,3 );
//禁用xmlrpc.php禁媒介攻击
add_filter('xmlrpc_enabled', '__return_false');
