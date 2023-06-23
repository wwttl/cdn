<?php
/*
Plugin Name: B2_统计
Plugin URI: #
Description: B2统计插件，对网站各种信息进行统计记录，方便站长对进行优化。
Version: 4.5
Author: #
Author URI: https://gitee.com/jiangye123/b2-plugins
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'B2_TJ_VERSION', '4.5' );
define( 'B2_TJ_DIR', plugin_dir_path( __FILE__ ) );
if (!defined('B2_TJ_URL'))     define('B2_TJ_URL',plugin_dir_url(__FILE__));


//date_default_timezone_set( 'Asia/Shanghai' );
//ini_set('date.timezone','Asia/Shanghai');    



//设置按钮
add_filter('plugin_action_links_'.plugin_basename(__FILE__), function($links){
    $links[] = '<a href="'.get_admin_url(null, 'admin.php?page=b2_tz_main_control') . '">' . __('统计页面','tj') . '</a>';
    return $links;
});
/*
//增加插件信息
add_filter('plugin_row_meta',function($links, $file) {
    if ($file == plugin_basename(__FILE__)) {
    	$links[] = '<span>升级插件后需要重新启动一下</span>';
    }
    return $links;
}, 10, 2);
*/
/*
//插件报错输出
add_action('activated_plugin',function($plugin_name){
    $out = '===================BEGIN===================='.PHP_EOL;
    $out .= 'Time   : '.date("Y/m/d H:i:s").PHP_EOL;
    $out .= 'Plugin :'.$plugin_name.PHP_EOL;
    $out .= 'Error: '.PHP_EOL;
    $out .= ob_get_contents();
    $out .= '=====================END===================='.PHP_EOL;
    file_put_contents(ABSPATH. 'plugin_activation.log', $out, FILE_APPEND);
});
*/
/////////////////////////增加错误提示
add_action( 'admin_notices', function(){
    if( get_transient( 'b2_tongji_notice' ) ){
        echo '<div class="notice notice-warning is-dismissible"><h1>B2 统计 启动提醒</h1>
            <p>时区设置有误，已自动修正</p>
        </div>';
        delete_transient( 'b2_tongji_notice' );
   }
} );

register_activation_hook(__FILE__,function(){
    if(wp_timezone_string() !== "Asia/Shanghai"){/////////修正时区
        update_option('gmt_offset',(float)8);
        update_option('timezone_string',"Asia/Shanghai");
        set_transient( 'b2_tongji_notice', true, 5 );
    }
	global $wpdb;
	//下载统计表
	$table_name = $wpdb->prefix.'TZ_download';
	if($wpdb->get_var("show tables like '$table_name'") != $table_name){
		$sql = " CREATE TABLE `$table_name` (
			`TZ_id` bigint(20) NOT NULL AUTO_INCREMENT,
			PRIMARY KEY(TZ_id),
			`TZ_date` timestamp not null default current_timestamp,
			`TZ_user` int,
			`TZ_post` int,
			`TZ_index` longtext,
			`TZ_post_i` int,
			`TZ_post_i_file_name` longtext,
			`TZ_post_i_file_url` longtext,
			`TZ_post_i_file_tq` longtext,
			`TZ_post_i_file_jy` longtext,
			`ip` longtext
		) CHARSET=utf8;";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
	$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$table_name' AND column_name = 'ip'"  );
	if(empty($row)){
		$wpdb->query("DELETE FROM $table_name" );
		$wpdb->query("ALTER TABLE $table_name ADD ip varchar(45) not null");
	}
	$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$table_name' AND column_name = 'TZ_index'"  );
	if(empty($row)){
	    $wpdb->query("DELETE FROM $table_name" );
		$wpdb->query("ALTER TABLE $table_name ADD TZ_index varchar(45) not null");
	}
	//搜索统计表
	$table_name = $wpdb->prefix.'Tj_Search';
	if($wpdb->get_var("show tables like '$table_name'") != $table_name){
		$sql = " CREATE TABLE `$table_name` (
			`id` bigint(20) NOT NULL AUTO_INCREMENT,
			PRIMARY KEY(id),
			`date` timestamp not null default current_timestamp,
			`user` int,
			`type` longtext,
			`word` longtext,
			`ip` longtext
		) CHARSET=utf8;";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
	//用户异常表
	$table_name = $wpdb->prefix.'Tj_User_Error';
	if($wpdb->get_var("show tables like '$table_name'") != $table_name){
		$sql = " CREATE TABLE `$table_name` (
			`id` bigint(20) NOT NULL AUTO_INCREMENT,
			PRIMARY KEY(id),
			`date` timestamp not null default current_timestamp,
			`type` longtext,
			`des` longtext,
			`user` longtext,
			`ip` longtext,
			`data` longtext
		) CHARSET=utf8;";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
	$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$table_name' AND column_name = 'user'"  );
	if(empty($row)){
	    $wpdb->query("DELETE FROM $table_name" );
		$wpdb->query("ALTER TABLE $table_name ADD user varchar(45) not null");
	}
});


register_deactivation_hook(__FILE__, function(){
	if(tj_get_option('b2_tongji_options','delete')){
    	global $wpdb;
    	$table_name = $wpdb->prefix.'TZ_download';
    	$wpdb->query("DROP TABLE IF EXISTS $table_name" );
    	$table_name = $wpdb->prefix.'Tj_Search';
    	$wpdb->query("DROP TABLE IF EXISTS $table_name" );
    	$table_name = $wpdb->prefix.'Tj_User_Error';
    	$wpdb->query("DROP TABLE IF EXISTS $table_name" );
    	delete_option( 'tj_user_login_num' );
	}
});

//////////////////////////////自动删除老旧数据s
add_action( 'wp', 'tz_daily_schedule');
function tz_daily_schedule() {
    if (!wp_next_scheduled( 'tz_daily_schedule_event' )) {
        wp_schedule_event(time(), 'daily', 'tz_daily_schedule_event');
    }
}
add_action('tz_daily_schedule_event','tz_daily_schedule_event');
function tz_daily_schedule_event(){
	global $wpdb;
	$mtime= date("Y-m-d H:i:s", strtotime("-8 day"));
	$wpdb->query( "DELETE FROM " . $wpdb->prefix . "TZ_download WHERE TZ_date < '$mtime'" );
	$wpdb->query( "DELETE FROM " . $wpdb->prefix . "Tj_Search WHERE date < '$mtime'" );
	$wpdb->query( "DELETE FROM " . $wpdb->prefix . "Tj_User_Error WHERE date < '$mtime'" );
}
//////////////////////////////自动删除老旧数据e


///////////////////////////////////////调整后台菜单顺序
function tj_custom_menu_order($menu) {
	if (!$menu) return true;
	array_unshift($menu,'index.php','b2_tz_main_control','b2_main_options');
	$menu = array_unique($menu);
	return $menu;
}
add_filter('custom_menu_order', 'tj_custom_menu_order');
add_filter('menu_order', 'tj_custom_menu_order');
///////////////////////////////////////////

require_once B2_TJ_DIR.'methods.php';
require_once B2_TJ_DIR.'tz.php';
require_once B2_TJ_DIR.'setting.php';  

require_once B2_TJ_DIR.'b2_dashboard/dashboard.php';
require_once B2_TJ_DIR.'b2_main_control/b2_main_control.php';
//require_once B2_TJ_DIR.'b2_main_control/b2_main_control_1.php';
require_once B2_TJ_DIR.'b2_main_control/b2_main_control_2.php';
require_once B2_TJ_DIR.'b2_main_control/b2_main_control_4.php';
require_once B2_TJ_DIR.'b2_main_control/b2_main_control_3.php';
require_once B2_TJ_DIR.'b2_main_control/b2_main_control_5.php';
require_once B2_TJ_DIR.'b2_main_control/b2_main_control_6.php';
require_once B2_TJ_DIR.'b2_main_control/b2_main_control_7.php';

require_once B2_TJ_DIR.'b2_directmessage_control/b2_directmessage_control.php';
require_once B2_TJ_DIR.'b2_user_control/b2_user_control.php';
require_once B2_TJ_DIR.'b2_comments/b2_comments.php';
require_once B2_TJ_DIR.'b2_download_control/b2_download_control.php';
if(tj_get_option('b2_tongji_options','search')){
	require_once B2_TJ_DIR.'b2_search_control/b2_search_control.php';//搜索
}
if(tj_get_option('b2_tongji_options','qiandao')){
	require_once B2_TJ_DIR.'b2_user_dailysign_control/b2_user_dailysign_control.php';
}
//require_once B2_TJ_DIR.'b2_refresh/b2_refresh.php';
////////api
add_action( 'rest_api_init', function () {
    register_rest_route('tj/v1', '/money/', array(
        'methods' => 'POST',
        'callback' => function($request){
            if (!isset($_SESSION)) {
                session_start();
            }
            $sec = isset($request['sec']) ? sanitize_text_field($request['sec']) : '';
            $check_time = isset($_SESSION['sign_ckeck_code_time']) ? $_SESSION['sign_ckeck_code_time'] : 0;
            if(tj_get_option('b2_tongji_options','apisec')==$sec && time()-$check_time>5){
                $TZ_control = new TZ_control();
                $jinrishouru = $TZ_control->tz_get_today_money();
                return array(
                    'money' => $jinrishouru[0]
                );
            }
            $_SESSION['sign_ckeck_code_time'] = time();
        },
        'permission_callback' => '__return_true'
    ));
});