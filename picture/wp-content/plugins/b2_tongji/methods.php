<?php
if ( !function_exists( 'isCrawler' ) ) {
	//提取自 WP-PostViews 插件 https://wordpress.org/plugins/wp-postviews
	function isCrawler() {
		$bots = array(
			'Google Bot' => 'google'
			, 'MSN' => 'msnbot'
			, 'Alex' => 'ia_archiver'
			, 'Lycos' => 'lycos'
			, 'Ask Jeeves' => 'jeeves'
			, 'Altavista' => 'scooter'
			, 'AllTheWeb' => 'fast-webcrawler'
			, 'Inktomi' => 'slurp@inktomi'
			, 'Turnitin.com' => 'turnitinbot'
			, 'Technorati' => 'technorati'
			, 'Yahoo' => 'yahoo'
			, 'Findexa' => 'findexa'
			, 'NextLinks' => 'findlinks'
			, 'Gais' => 'gaisbo'
			, 'WiseNut' => 'zyborg'
			, 'WhoisSource' => 'surveybot'
			, 'Bloglines' => 'bloglines'
			, 'BlogSearch' => 'blogsearch'
			, 'PubSub' => 'pubsub'
			, 'Syndic8' => 'syndic8'
			, 'RadioUserland' => 'userland'
			, 'Gigabot' => 'gigabot'
			, 'Become.com' => 'become.com'
			, 'Baidu' => 'baiduspider'
			, 'so.com' => '360spider'
			, 'Sogou' => 'spider'
			, 'soso.com' => 'sosospider'
			, 'Yandex' => 'yandex'
		);
		$useragent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
		foreach ( $bots as $name => $lookfor ) {
			if ( ! empty( $useragent ) && ( false !== stripos( $useragent, $lookfor ) ) ) {
				return true;
			}
		}
		return false;
	}
}

function tj_get_user_ip() {
	if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {

		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {

		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}

	if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
		$ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
	}

	if(strpos($ip,',') !== false){
		$ips = explode(',',$ip);
		$ip = $ips[0];
	}

	return $ip;
}

function tj_get_option($key1,$key2){
    $option = get_option($key1);
    return isset($option[$key2]) ? $option[$key2] : false;
}

function tj_change_rate($data){
    //return $data;
	if(tj_get_option('b2_tongji_options','rate')){
		if(is_array($data)){
			$data = array_map(function($value){
			    $rate = tj_get_option('b2_normal_pay','paypal_rate');
				return $value*$rate;
			}, $data);
			return $data;
		}else{
		    $rate = tj_get_option('b2_normal_pay','paypal_rate');
			return $data*$rate;
		}
	}
	return $data;
}
function tj_money_type(){
	if(tj_get_option('b2_tongji_options','rate')){
		return tj_get_option('b2_normal_pay','paypal_currency_code');
	}
	return '￥';
}