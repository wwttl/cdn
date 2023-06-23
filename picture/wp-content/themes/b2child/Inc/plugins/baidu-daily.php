<?php
if ( ! defined( 'ABSPATH' ) ) exit;
// 快速收录
function Daily_submit($post_ID) {
	$WEB_TOKEN = lmy_get_option('daily_token');
	$WEB_DOMAIN = home_url( '/' );
	if(get_post_meta($post_ID,'Dailysubmit',true) == 1) return;
	$url = get_permalink($post_ID);;
	$api = 'http://data.zz.baidu.com/urls?site='.$WEB_DOMAIN.'&token='.$WEB_TOKEN.'&type=daily';
	$ch  = curl_init();
	$options =  array(
		CURLOPT_URL => $api,
		CURLOPT_POST => true,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_POSTFIELDS => $url,
		CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
	);
	curl_setopt_array($ch, $options);
	$result = json_decode(curl_exec($ch),true);
	if (array_key_exists('success',$result)) {
		add_post_meta($post_ID, 'Dailysubmit', 1, true);
	}
}
add_action('publish_post', 'Daily_submit', 0);
//add_action('edit_post', 'Daily_submit', 0);