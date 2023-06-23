<?php
/*后台可设置类*/
// 文章添加关键词链接
if (lmy_get_option('tag_c')) {
	// 关键词加链接
	$match_num_from = 1;
	//一个关键字少于多少不替换
	$match_num_to = lmy_get_option('chain_n');
	add_filter('the_content','tag_link',1);
	function tag_sort($a, $b) {
		if ( $a->name == $b->name ) return 0;
		return ( strlen($a->name) > strlen($b->name) ) ? -1 : 1;
	}
	function tag_link($content) {
		global $match_num_from,$match_num_to;
		$posttags = get_the_tags();
		if ($posttags) {
			usort($posttags, "tag_sort");
			foreach($posttags as $tag) {
				$link = get_tag_link($tag->term_id);
				$keyword = $tag->name;
				if (preg_match_all('|(<h[^>]+>)(.*?)'.$keyword.'(.*?)(</h[^>]*>)|U', $content, $matchs)) {
					continue;
				}
				if (preg_match_all('|(<a[^>]+>)(.*?)'.$keyword.'(.*?)(</a[^>]*>)|U', $content, $matchs)) {
					continue;
				}
				$cleankeyword = stripslashes($keyword);
				$url = "<a href=\"$link\" title=\"".str_replace('%s',addcslashes($cleankeyword, '$'),__('查看与 %s 相关的文章'))."\"";
				$url .= ' target="_blank"';
				$url .= ">".addcslashes($cleankeyword, '$')."</a>";
				$limit = rand($match_num_from,$match_num_to);
				global $ex_word;
				$case = "";
				$content = preg_replace( '|(<a[^>]+>)(.*)('.$ex_word.')(.*)(</a[^>]*>)|U'.$case, '$1$2%&&&&&%$4$5', $content);
				$content = preg_replace( '|(<img)(.*?)('.$ex_word.')(.*?)(>)|U'.$case, '$1$2%&&&&&%$4$5', $content);
				$cleankeyword = preg_quote($cleankeyword,'\'');
				$regEx = '\'(?!((<.*?)|(<a.*?)))('. $cleankeyword . ')(?!(([^<>]*?)>)|([^>]*?</a>))\'s' . $case;
				$content = preg_replace($regEx,$url,$content,$limit);
				$content = str_replace( '%&&&&&%', stripslashes($ex_word), $content);
			}
		}
		return $content;
	}
}
//自动给文章添加TAG
if (lmy_get_option('tag_add')) {
add_action('save_post', 'auto_add_tags');
function auto_add_tags(){
    $tags = get_tags( array('hide_empty' => false) );
    $post_id = get_the_ID();
    $post_content = get_post($post_id)->post_content;
    if ($tags) {
        foreach ( $tags as $tag ) {
            // 如果文章内容出现了已使用过的标签，自动添加这些标签
            if ( strpos($post_content, $tag->name) !== false)
                wp_set_post_tags( $post_id, $tag->name, true );
        }
    }
}
}
// 自动给图片添加Alt标签
if (lmy_get_option("image_alt")) {
function img_alt($content) {
	global $post;
	preg_match_all('#<img([^>]+)>#is',$content,$images);
	if(!is_null($images)) {
		foreach($images[1] as $index => $value) {
			$new_img = str_replace('<img', '<img alt="'.get_the_title().'" title="'.get_the_title().'"', $images[0][$index]);
			$content = str_replace($images[0][$index], $new_img, $content);
		}
	}
	return $content;
}
add_filter('the_content', 'img_alt', 99999);
}
//文章超强版权
if (lmy_get_option("super_copyright")) {
add_filter( 'the_content', 'prefix_insert_post_ads' );
function prefix_insert_post_ads( $content ) {
	if ( is_single() && ! is_admin() ) {
		echo prefix_insert_after_paragraph($content );
	}
}
function prefix_insert_after_paragraph($content ) {
	$closing_p = '</p>';
	$paragraphs = explode( $closing_p, $content );
	foreach ($paragraphs as $index => $paragraph) {
		if ( trim( $paragraph ) ) {
		    $insertion = '<span class="beupset'.$index.'">文章源自'.get_bloginfo('name').' - '.get_permalink().'</span>';
			$paragraphs[$index] .= $closing_p;
			$paragraphs[$index] .= $insertion;
		}
	}
	return implode( '', $paragraphs );
}
}
//百度推送
if (lmy_get_option("seo_baidu_push")) {
	if(!function_exists('Baidu_Submit') && function_exists('curl_init')) {
		function Baidu_Submit($post_ID) {
			$WEB_TOKEN= lmy_get_option('seo_baidu_push_key');
			//这里换成你的网站的百度主动推送的token值
			$WEB_DOMAIN=get_option('home');
			//已成功推送的文章不再推送
			if(get_post_meta($post_ID,'Baidusubmit',true) == 1) return;
			$url = get_permalink($post_ID);
			$api = 'http://data.zz.baidu.com/urls?site='.$WEB_DOMAIN.'&token='.$WEB_TOKEN;
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
				$result_meta['update_time'] = current_time("Y-m-d H:i:s");
				add_post_meta($post_ID, 'Baidusubmit', 1, true);
			}
		}
		add_action('publish_post', 'Baidu_Submit', 0);
	}
}
//bing推送
if (lmy_get_option("seo_bing_push")) {
	function dmd_post_to_by_tui() {
		$apikey= lmy_get_option('seo_bing_push_key');
		$url='https://ssl.bing.com/webmaster/api.svc/json/SubmitUrlbatch?apikey='.$apikey;
		global $post;
		if ( wp_is_post_revision($post->ID ) || wp_is_post_autosave($post->ID) ) {
			return;
		}
		if(get_post_meta($post->ID,'Bingsubmit',true) == 1) return;
		$plink = get_permalink($post->ID);
		if( $plink ) {
			$data=json_encode(array('siteUrl'=>home_url(),'urlList'=>array($plink)));
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json; charset=utf-8',
			'Content-Length: ' . strlen($data)
			)
			);
			$response = curl_exec($ch);
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			add_post_meta($post->ID, 'Bingsubmit', 1, true);
			curl_close($ch);
		}
	}
	add_action('publish_post', 'dmd_post_to_by_tui');
}
//indexnow推送
if (lmy_get_option("seo_indexnow_push")) {
	function Submit_Url_IndexNow($post_ID) {
		$url = get_permalink($post_ID);
		$yourkey= lmy_get_option('seo_indexnow_push_key');
		$weburl=get_option('home');
		$keyLocation=$weburl+$yourkey.'.txt';
		// 创建一个新cURL资源
		$ch = curl_init();
		// 设置URL和相应的选项
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_URL, "https://api.indexnow.org/indexnow?url=" . $url . "&key=" . $yourkey. "&keyLocation" .$weburl.$yourkey. ".txt");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 1 );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		// 抓取URL
		$result = curl_exec($ch);
		// 关闭cURL资源，并释放资源
		curl_close($ch);
		return $result;
	}
	add_action('publish_post', 'Submit_Url_IndexNow', 0);
}
//神马推送
if (lmy_get_option("seo_sm_push")) {
	function sm_mip_add($post_id){
	        $api = lmy_get_option("seo_sm_push_token");
	        $response = wp_remote_post($api, array(
		        'headers' => array('Accept-Encoding'=>'','Content-Type'=>'text/plain'),
	        	'sslverify' => false,
	        	'blocking' => false,
	        	'body' => get_permalink($post_id)
	        ));
	add_action('publish_post', 'sm_mip_add', 0);
}
}
//头条推送
if (lmy_get_option("seo_jrtt_push")) {
	function seo_jrtt_push(){
		echo "<script>".lmy_get_option("seo_jrtt_push_key")."</script>";
	}
	add_action('toutiao_push', 'seo_jrtt_push', 0);
}
//压缩WordPress前端html代码
if (lmy_get_option("html_compress")) {
function teckel_minify_html_output($buffer) {
if ( substr( ltrim( $buffer ), 0, 5) == '<?xml' )
		return ( $buffer );
	$minify_javascript = lmy_get_option( 'minify_javascript' );
	$minify_html_comments = lmy_get_option( 'minify_html_comments' );
	$minify_html_utf8 = lmy_get_option( 'minify_html_utf8' );
	if ( $minify_html_utf8 == 'yes' && mb_detect_encoding($buffer, 'UTF-8', true) )
		$mod = '/u';
	else
		$mod = '/s';
	$buffer = str_replace(array (chr(13) . chr(10), chr(9)), array (chr(10), ''), $buffer);
	$buffer = str_ireplace(array ('<script', '/script>', '<pre', '/pre>', '<textarea', '/textarea>', '<style', '/style>'), array ('M1N1FY-ST4RT<script', '/script>M1N1FY-3ND', 'M1N1FY-ST4RT<pre', '/pre>M1N1FY-3ND', 'M1N1FY-ST4RT<textarea', '/textarea>M1N1FY-3ND', 'M1N1FY-ST4RT<style', '/style>M1N1FY-3ND'), $buffer);
	$split = explode('M1N1FY-3ND', $buffer);
	$buffer = ''; 
	for ($i=0; $i<count($split); $i++) {
		$ii = strpos($split[$i], 'M1N1FY-ST4RT');
		if ($ii !== false) {
			$process = substr($split[$i], 0, $ii);
			$asis = substr($split[$i], $ii + 12);
			if (substr($asis, 0, 7) == '<script') {
				$split2 = explode(chr(10), $asis);
				$asis = '';
				for ($iii = 0; $iii < count($split2); $iii ++) {
					if ($split2[$iii])
						$asis .= trim($split2[$iii]) . chr(10);
					if ( $minify_javascript != 'no' )
						if (strpos($split2[$iii], '//') !== false && substr(trim($split2[$iii]), -1) == ';' )
							$asis .= chr(10);
				}
				if ($asis)
					$asis = substr($asis, 0, -1);
				if ( $minify_html_comments != 'no' )
					$asis = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $asis);
				if ( $minify_javascript != 'no' )
					$asis = str_replace(array (';' . chr(10), '>' . chr(10), '{' . chr(10), '}' . chr(10), ',' . chr(10)), array(';', '>', '{', '}', ','), $asis);
			} else if (substr($asis, 0, 6) == '<style') {
				$asis = preg_replace(array ('/\>[^\S ]+' . $mod, '/[^\S ]+\<' . $mod, '/(\s)+' . $mod), array('>', '<', '\\1'), $asis);
				if ( $minify_html_comments != 'no' )
					$asis = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $asis);
				$asis = str_replace(array (chr(10), ' {', '{ ', ' }', '} ', '( ', ' )', ' :', ': ', ' ;', '; ', ' ,', ', ', ';}'), array('', '{', '{', '}', '}', '(', ')', ':', ':', ';', ';', ',', ',', '}'), $asis);
			}
		} else {
			$process = $split[$i];
			$asis = '';
		}
		$process = preg_replace(array ('/\>[^\S ]+' . $mod, '/[^\S ]+\<' . $mod, '/(\s)+' . $mod), array('>', '<', '\\1'), $process);
		if ( $minify_html_comments != 'no' )
			$process = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->' . $mod, '', $process);
		$buffer .= $process.$asis;
	}
	$buffer = str_replace(array (chr(10) . '<script', chr(10) . '<style', '*/' . chr(10), 'M1N1FY-ST4RT'), array('<script', '<style', '*/', ''), $buffer);
	$minify_html_xhtml = lmy_get_option( 'minify_html_xhtml' );
	$minify_html_relative = lmy_get_option( 'minify_html_relative' );
	$minify_html_scheme = lmy_get_option( 'minify_html_scheme' );
	if ( $minify_html_xhtml == 'yes' && strtolower( substr( ltrim( $buffer ), 0, 15 ) ) == '<!doctype html>' )
		$buffer = str_replace( ' />', '>', $buffer );
	if ( $minify_html_relative == 'yes' )
		$buffer = str_replace( array ( 'https://' . $_SERVER['HTTP_HOST'] . '/', 'http://' . $_SERVER['HTTP_HOST'] . '/', '//' . $_SERVER['HTTP_HOST'] . '/' ), array( '/', '/', '/' ), $buffer );
	if ( $minify_html_scheme == 'yes' )
		$buffer = str_replace( array( 'http://', 'https://' ), '//', $buffer );
	return ($buffer);
}
ob_start('teckel_minify_html_output');
}
//未登录全站图片模糊
if (lmy_get_option("other_wdlqzmh")) {
	function n_yingcang_css() {
		echo '<style>
  img {
  -webkit-filter: blur(10px)!important;
    -moz-filter: blur(10px)!important;
    -ms-filter: blur(10px)!important;
    filter: blur(6px)!important;}
    </style>';
	}
	if( !is_user_logged_in()) {
		add_action( 'wp_head', 'n_yingcang_css' );
	}
	;
}
//未登录文章详情页内图片模糊
if (lmy_get_option("other_wdlwzmh")) {
	function n_yingcang_css2() {
		echo '<style>
  .entry-content img {
  -webkit-filter: blur(10px)!important;
    -moz-filter: blur(10px)!important;
    -ms-filter: blur(10px)!important;
    filter: blur(6px)!important;}
    </style>';
	}
	if( !is_user_logged_in()) {
		add_action( 'wp_head', 'n_yingcang_css2' );
	}
	;
}
//上传文件重命名
if (lmy_get_option("other_scwjcmm")) {
	function git_upload_filter($file) {
		$time = date("YmdHis");
		$file['name'] = $time . "" . mt_rand(1, 100) . "." . pathinfo($file['name'], PATHINFO_EXTENSION);
		return $file;
	}
	add_filter('wp_handle_upload_prefilter', 'git_upload_filter');
}
//文章外链添加nofollow
if (lmy_get_option("link_external")) {
	add_filter( 'the_content', 'add_nofollow_content' );
	function add_nofollow_content($content) {
		$content = preg_replace_callback( '/<a[^>]*href=["|\']([^"|\']*)["|\'][^>]*>([^<]*)<\/a>/i',
					function( $m ) {
			$site_link = get_option( 'siteurl' );
			$site_link_other = get_option( 'siteurl' );
			if (strpos( $m[1], $site_link  ) === false && strpos( $m[1], $site_link_other ) === false )
								return '<a href="'.$m[1].'" rel="external nofollow" target="_blank">'.$m[2].'</a>'; else
							if (lmy_get_option('link_internal')) {
				return '<a href="'.$m[1].'" target="_blank">'.$m[2].'</a>';
			} else {
				return '<a href="'.$m[1].'">'.$m[2].'</a>';
			}
		}
		,
					$content
				);
		return $content;
	}
}
//添加上传webp图片支持
if (lmy_get_option("other_scwedp")) {
	function bzg_filter_mime_types( $array ) {
		$array['webp'] = 'image/webp';
		return $array;
	}
	add_filter( 'mime_types', 'bzg_filter_mime_types', 10, 1 );
}
//添加webp图片缩略图支持
if (lmy_get_option("other_wedpslt")) {
	function bzg_file_is_displayable_image($result, $path) {
		$info = @getimagesize( $path );
		if($info['mime'] == 'image/webp') {
			$result = true;
		}
		return $result;
	}
	add_filter( 'file_is_displayable_image', 'bzg_file_is_displayable_image', 10, 2 );
}
// 禁用文章修订
if (lmy_get_option('revisions_no')) {
	add_filter( 'wp_revisions_to_keep', 'disable_wp_revisions_to_keep', 10, 2 );
}
function disable_wp_revisions_to_keep( $num, $post ) {
	return 0;
}
// 禁用REST API
if (lmy_get_option('disable_api')) {
	add_filter('rest_enabled', '_return_false');
	add_filter('rest_jsonp_enabled', '_return_false');
}
// 阻止恶意URL请求
if (lmy_get_option('be_safety')) {
	global $user_ID;
	if( $user_ID ) {
		if( !current_user_can( 'administrator' ) ) {
			if ( strlen($_SERVER['REQUEST_URI'] ) > 255 ||
						stripos( $_SERVER['REQUEST_URI'], "eval(" ) ||
						stripos( $_SERVER['REQUEST_URI'], "CONCAT" ) ||
						stripos( $_SERVER['REQUEST_URI'], "UNION+SELECT" ) ||
						stripos( $_SERVER['REQUEST_URI'], "base64" ) ) {
				@header("HTTP/1.1 414 Request-URI Too Long" );
				@header( "Status: 414 Request-URI Too Long" );
				@header( "Connection: Close" );
				@exit;
			}
		}
	}
}
//邮件SMTP
if (lmy_get_option('setup_email_smtp')) {
add_action('phpmailer_init', 'mail_smtp');
function mail_smtp( $phpmailer ) {
	$phpmailer->FromName = ''. lmy_get_option('email_name') . '';
	$phpmailer->Host = ''. lmy_get_option('email_smtp') . '';
	$phpmailer->Port = ''. lmy_get_option('email_port') . '';
	$phpmailer->Username = ''. lmy_get_option('email_account') . '';
	$phpmailer->Password = ''. lmy_get_option('email_authorize') . '';
	$phpmailer->From = ''. lmy_get_option('email_account') . '';
	$phpmailer->SMTPAuth = true;
	$phpmailer->SMTPSecure = ''. lmy_get_option('email_secure') . '';
	$phpmailer->IsSMTP();
}
}
//评论禁止纯英文、日语、数字
if (lmy_get_option('other_cszpl')) {
	function refused_english_comments($incoming_comment) {
		$pattern = '/[一-龥]/u';
		// 禁止全英文评论
		if(!preg_match($pattern, $incoming_comment['comment_content'])) {
			wp_die( "请使用中文进行评论！You should type some Chinese word!" );
		}
		$pattern = '/[あ-んア-ン]/u';
		// 禁止日文评论
		if(preg_match($pattern, $incoming_comment['comment_content'])) {
			wp_die( "关于日语，站长勉强听懂雅蠛蝶 Japanese Get out！日本语出て行け！ You should type some Chinese word！" );
		}
		return( $incoming_comment );
	}
	add_filter('preprocess_comment', 'refused_english_comments');
}
//WordPress设置评论时间间隔
if (lmy_get_option('other_pljg')) {
	add_filter('comment_flood_filter', 'suren_comment_flood_filter', 10, 3);
	function suren_comment_flood_filter($flood_control, $time_last, $time_new) {
		$seconds = lmy_get_option('other_pljg_sj');
		//间隔时间
		if(($time_new - $time_last) < $seconds) {
			$time=$seconds-($time_new - $time_last);
			wp_die ('评论过快！请'. $time.'秒后再次评论');
		} else {
			return false;
		}
	}
}
//添加文章末尾版权信息  
if (lmy_get_option('page_wzmsbq')) {
	function copyright($content) {
		$wzbq=lmy_get_option('page_wzmsbq_text');
		$mail=lmy_get_option('page_wzmsbq_mail');
		$updated_date = get_the_modified_time('Y年m月d日 G时i分s秒');
		//这里设置时间显示格式，可自由调整。
		if(is_single()||is_feed()) {
			$content.='<div class="open-message"  style="border-radius: 20px; background: #f8f9fb;color: #3860f4; padding: 15px 29px 15px 15px; font-size: 13px; line-height: 28px;"><i class="fa fa-bullhorn"></i>文章链接：<a href="'.get_permalink().'" title="'.get_the_title().'">'.get_permalink().'</a><br/>文章标题：<a rel="bookmark" title="'.get_the_title().'" href="'.get_permalink().'">'.get_the_title().'</a><br/>文章版权：'.$wzbq.' 所发布的内容，部分为原创文章，转载请注明来源，网络转载文章如有侵权请联系我们！<br/>本文最后更新发布于<code>'. $updated_date . '</code>，某些文章具有时效性，若有错误或已失效，请在下方<a href='.get_permalink().'#comment><b>留言</b></a>或联系：<a href="mailto:'.$mail.'"><b>'.$mail.'</b></a>';
		}
		return $content;
	}
	add_filter ('the_content', 'copyright');
}
//阅读时间
if (lmy_get_option('word_count')) {
// 字数统计
function count_words ( $text ) {
	global $post;
	$output = '';
	if ( '' == $text ) {
		$text = $post->post_content;
		if (mb_strlen( $output, 'UTF-8' ) < mb_strlen( $text, 'UTF-8' ) ) $output .= '<span class="word-count">' . sprintf( __( '本文共', 'begin' ) ) . ' ' . mb_strlen( preg_replace( '/\s/','', html_entity_decode( strip_tags( $post->post_content ) ) ), 'UTF-8' ) . '个字，</span>';
		return $output;
	}
}
}
// 阅读时间
if (lmy_get_option('reading_time')) {
function get_reading_time($content) {
	$zm_format = '<span class="reading-time">' . sprintf( __( '预计阅读时间需要', 'begin' ) ) . '%min%' . sprintf( __( '分', 'begin' ) ) . '%sec%' . sprintf( __( '秒。', 'begin' ) ). '</span>';
	$zm_chars_per_minute = 300;

	$zm_format = str_replace('%num%', $zm_chars_per_minute, $zm_format);
	$words = mb_strlen(preg_replace('/\s/','',html_entity_decode(strip_tags($content))),'UTF-8');
	//$words = mb_strlen(strip_tags($content));

	$minutes = floor($words / $zm_chars_per_minute);
	$seconds = floor($words % $zm_chars_per_minute / ($zm_chars_per_minute / 60));
	return str_replace('%sec%', $seconds, str_replace('%min%', $minutes, $zm_format));
}
function reading_time() {
	echo get_reading_time(get_the_content());
}
}
// 百度收录
if (lmy_get_option('baidu_record')) {
function baidu_check($url, $post_id = ''){
	global $wpdb;
	$post_id = ( null === $post_id ) ? get_the_ID() : $post_id;
	$baidu_record  = get_post_meta($post_id,'baidu_record',true);
	if( $baidu_record != 1){
		$url='http://www.baidu.com/s?wd='.$url;
		$curl=curl_init();
		curl_setopt($curl,CURLOPT_URL,$url);
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
		$rs=curl_exec($curl);
		curl_close($curl);
		if(!strpos($rs,'没有找到')){
			if( $baidu_record == 0){
				update_post_meta($post_id, 'baidu_record', 1);
			} else {
				add_post_meta($post_id, 'baidu_record', 1, true);
			}
			return 1;
		} else {
			if( $baidu_record == false){
				add_post_meta($post_id, 'baidu_record', 0, true);
		}
			return 0;
		}
	} else {
		return 1;
	}
}

function baidu_record_t() {
	if (is_user_logged_in()) {
		if(baidu_check(get_permalink()) == 1) {
			echo '<li><span><a target="_blank" title="点击查看" rel="external nofollow" href="http://www.baidu.com/s?wd='.get_the_title().'&tn=bds&cl=3&ct=2097152&si=' . $_SERVER['SERVER_NAME']. '&s=on" style="color: blue; font-size: 12px; float: right;">已收录</a></b></span>
                    </li>';
		} else {
			echo '<li><span><a rel="external nofollow" title="一键提交给百度" target="_blank" href="http://zhanzhang.baidu.com/sitesubmit/index?sitename='.get_permalink().'" style="color: red; font-size: 12px; float: right;">暂未收录</a></b></span>
                    </li>';
		}
	}
}
}
// 历史今天
function begin_today(){
	global $wpdb;
	$today_post = '';
	$result = '';
	$post_year = get_the_time('Y');
	$post_month = get_the_time('m');
	$post_day = get_the_time('j');
	$sql = "select ID, year(post_date_gmt) as today_year, post_title, comment_count FROM 
			$wpdb->posts WHERE post_password = '' AND post_type = 'post' AND post_status = 'publish'
			AND year(post_date_gmt)!='$post_year' AND month(post_date_gmt)='$post_month' AND day(post_date_gmt)='$post_day'
			order by post_date_gmt DESC limit 8";
	$histtory_post = $wpdb->get_results($sql);
	if( $histtory_post ){
		foreach( $histtory_post as $post ){
			$today_year = $post->today_year;
			$today_post_title = $post->post_title;
			$today_permalink = get_permalink( $post->ID );
			// $today_comments = $post->comment_count;
			$today_post .= '<li><a href="'.$today_permalink.'" target="_blank"><span>'.$today_year.'</span>'.$today_post_title.'</a></li>';
		}
	}
	if ( $today_post ){
		$result = '<div class="begin-today rp"><fieldset><legend><h5>'. sprintf(__( '历史上的今天', 'begin' )) .'</h5></legend><div class="today-date"><div class="today-m">'.get_the_date( 'F' ).'</div><div class="today-d">'.get_the_date( 'j' ).'</div></div><ul>'.$today_post.'</ul></fieldset></div>';
	}
	return $result;
}
//夜间模式
if (lmy_get_option('page_night')) {
if (isset($_COOKIE['night'])) {
function page_night($classes) {
         $night=($_COOKIE['night'] == '1' ? 'night' : '');
         $classes[] = $night;
        return $classes;
     }
 add_filter('body_class','page_night');
}
}
//复制添加原文链接
if (lmy_get_option("page_fztjywdz")) {
function add_copyright_text() {
    if (is_single()) { ?>
<script type='text/javascript'>
function addLink() {
var body_element = document.body;
var selection;
selection = window.getSelection();
if (window.clipboardData) { // Internet Explorer
var pagelink ="\r\n\r\n  原文出自[ <?php the_title(); ?> ] 转载请保留原文链接: "+document.location.href+"";
var copytext = selection + pagelink;
window.clipboardData.setData ("Text", copytext);
return false;
} else {
var pagelink = "\r\n\r\n  原文出自[ <?php the_title(); ?> ] 转载请保留原文链接: "+document.location.href+"";
var copytext = selection + pagelink;
var newdiv = document.createElement('div');
newdiv.style.position='absolute';
newdiv.style.left='-99999px';
body_element.appendChild(newdiv);
newdiv.innerHTML = copytext;
selection.selectAllChildren(newdiv);
window.setTimeout(function() {
body_element.removeChild(newdiv);
},0);
}
}
document.oncopy = addLink;
</script>
<?php
}
}
add_action( 'wp_footer', 'add_copyright_text');
}
// 字数统计
if (lmy_get_option("all_more")) {
function word_num () {
	global $post;
	$text_num = mb_strlen(preg_replace('/\s/','',html_entity_decode(strip_tags($post->post_content))),'UTF-8');
	return $text_num;
}
}
/** 统计**/
if (lmy_get_option('page_qztj')) {
	/**
 * 统计全站总访问量/今日总访问量/当前是第几个访客
 * @return [type] [description]
 */
    error_reporting(0);
	function wb_site_count_user() {
		$addnum = rand(5,10);
		//每个访客增加的访问数 5 - 10的随机数
		session_start();
		$date = date('ymd',time());
		if(!isset($_SESSION['wb_'.$date]) && !$_SESSION['wb_'.$date]) {
			$count = get_option('site_count');
			if(!$count || !is_array($count)) {
				$newcount = array(
				                'all' => lmy_get_option("page_qztj_moren"),
				                'date' => $date,
				                'today' => $addnum
				            );
				update_option( 'site_count', $newcount );
			} else {
				$newcount = array(
				                'all' => ($count['all']+$addnum),
				                'date' => $date,
				                'today' => ($count['date'] == $date) ? ($count['today']+$addnum) : $addnum
				            );
				update_option( 'site_count', $newcount );
			}
			$_SESSION['wb_'.$date] = $newcount['today'];
		}
		return;
	}
	add_action('init', 'wb_site_count_user');
	//输出访问统计
	function wb_echo_site_count() {
		session_start();
		$sitecount = get_option('site_count');
		$date = date('ymd',time());
		echo '<div class="footer-custom copyright" style="color: '.lmy_get_option("page_qztj_wz").';"><div class="footer-custom-class"><p>总访问量：<span style="color: '.lmy_get_option("page_qztj_sz").';">'.absint($sitecount['all']).'</span> &nbsp;&nbsp; 今日访问量：<span style="color: '.lmy_get_option("page_qztj_sz").';">'.absint($sitecount['today']).'</span> &nbsp;&nbsp; 您是今天第：<span style="color: '.lmy_get_option("page_qztj_sz").';">'.absint($_SESSION['wb_'.$date]).'</span> 位访问者';
	}
	// 在线人数
	function countOnlineNum() {
		//首先你要有读写文件的权限，首次访问肯不显示，正常情况刷新即可
		$online_log = "maplers.dat";
		//保存人数的文件到根目录,
		$timeout = 120;
		//120秒内没动作,认为掉线
		$entries = file($online_log);
		$temp = array();
		for ($i=0;$i<count($entries);$i++) {
			$entry = explode(",",trim($entries[$i]));
			if(($entry[0] != getenv('REMOTE_ADDR')) && ($entry[1] > time())) {
				array_push($temp,$entry[0].",".$entry[1]."\n");
				//取出其他浏览者的信息,并去掉超时者,保存进$temp
			}
		}
		array_push($temp,getenv('REMOTE_ADDR').",".(time() + ($timeout))."\n");
		//更新浏览者的时间
		$maplers = count($temp);
		//计算在线人数
		$entries = implode("",$temp);
		//写入文件
		$fp = fopen($online_log,"w");
		flock($fp,LOCK_EX);
		//flock() 不能在NFS以及其他的一些网络文件系统中正常工作
		fputs($fp,$entries);
		flock($fp,LOCK_UN);
		fclose($fp);
		echo '&nbsp;&nbsp; 在线人数：<span style="color: '.lmy_get_option("page_qztj_sz").';">'.$maplers.'</span>人</p></div></div>';
	}
}
/** 统计结束 **/
//底部统计模块
if(lmy_get_option('siteCount')) {
//WordPress获取今日发布文章数量
function nd_get_24h_post_count(){
	$today = getdate();
	$query = new WP_Query( 'year=' . $today["year"] . '&monthnum=' . $today["mon"] . '&day=' . $today["mday"]);
	$postsNumber = $query->found_posts;
	return $postsNumber;
}
//WordPress获取一周发布文章数量
function nd_get_week_post_count(){
    $date_query = array(
        array(
        'after'=>'1 week ago'
        )
    );
    $args = array(
        'post_type' => 'post',
        'post_status'=>'publish',
        'date_query' => $date_query,
        'no_found_rows' => true,
        'suppress_filters' => true,
        'fields'=>'ids',
        'posts_per_page'=>-1
    );
    $query = new WP_Query( $args );
    return $query->post_count;
}

//WordPress整站文章访问计数
function nd_get_all_view(){
	global $wpdb;
	$count=0;
	$views= $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE meta_key='views'");
	foreach($views as $key=>$value){
		$meta_value=$value->meta_value;
		if($meta_value!=' '){
			$count+=(int)$meta_value;
		}
	}return $count;
}
//WordPress获取指定作者文章总浏览量
if(!function_exists('cx_posts_views')) {
    function cx_posts_views($author_id = 1 ,$display = true) {
        global $wpdb;
        $sql = "SELECT SUM(meta_value+0) FROM $wpdb->posts left join $wpdb->postmeta on ($wpdb->posts.ID = $wpdb->postmeta.post_id) WHERE meta_key = 'views' AND post_author =$author_id";
        $comment_views = intval($wpdb->get_var($sql));
        if($display) {
            echo number_format_i18n($comment_views);
        } else {
            return $comment_views;
        }
    }
}
}
//Wordpress 5.0+ 禁用 Gutenberg 编辑器
if (lmy_get_option('be_gutenberg')) {
	add_filter('use_block_editor_for_post', '__return_false');
	remove_action( 'wp_enqueue_scripts', 'wp_common_block_scripts_and_styles' );
}
// sitemap_xml
if (lmy_get_option('sitemap_xml')) {
	require_once get_theme_file_path('/Inc/plugins/sitemap.php');
}
//移除后台菜单分隔符
function be_remove_separator() {
	global $menu;
	unset($menu[4]);
	unset($menu[59]);
}
if (lmy_get_option('remove_separator')) {
	add_action('admin_head', 'be_remove_separator');
}
// 页面链接添加html后缀
if (lmy_get_option('page_html')) {
	add_action('init', 'html_page_permalink', -1);
	function html_page_permalink() {
		global $wp_rewrite;
		if ( !strpos($wp_rewrite->get_page_permastruct(), '.html')) {
			$wp_rewrite->page_structure = $wp_rewrite->page_structure . '.html';
		}
	}
}
// 后台禁止头像
if (lmy_get_option('ban_avatars') && is_admin()) {
	add_filter( 'get_avatar' , 'ban_avatar' , 1 , 1 );
}
function ban_avatar( $avatar) {
	$avatar = "";
}
// 仅搜索文章标题
if (lmy_get_option('g_search', false)) {
    add_filter('posts_search', 'search_enhancement', 10, 2);

    function search_enhancement($search, $wp_query)
    {
        if (!empty($search) && !empty($wp_query->query_vars['search_terms'])) {
            global $wpdb;

            $q = $wp_query->query_vars;
            $n = !empty($q['exact']) ? '' : '%';

            $search = array();

            foreach ((array)$q['search_terms'] as $term) {
                $search[] = $wpdb->prepare("$wpdb->posts.post_title LIKE %s", $n . $wpdb->esc_like($term) . $n);
            }

            if (!is_user_logged_in()) {
                $search[] = "$wpdb->posts.post_password = ''";
            }

            $search = ' AND ' . implode(' AND ', $search);
        }

        return $search;
    }
}
// 禁止使用 admin 用户名尝试登录
if (lmy_get_option('no_admin')) {
	add_filter('wp_authenticate', 'wpjam_no_admin_user');
	function wpjam_no_admin_user($user) {
		if ('admin' == $user) {
			exit;
		}
	}
	add_filter('sanitize_user', 'wpjam_sanitize_user_no_admin', 10, 3);
	function wpjam_sanitize_user_no_admin($username, $raw_username, $strict) {
		if ('admin' == $raw_username || 'admin' == $username) {
			exit;
		}
		return $username;
	}
}
//禁用文章自动保存
if(lmy_get_option('autosaveop')) {
	add_action('wp_print_scripts','disable_autosave');
	function disable_autosave() {
		wp_deregister_script('autosave');
	}
}
//移除RSS订阅
if(lmy_get_option('rss_off')) {
function disable_our_feeds() {
	wp_die( __('<strong>Error:</strong> 没有RSS订阅,请访问我们的主页！') );
}
add_action('do_feed','disable_our_feeds',1);
add_action('do_feed_rdf','disable_our_feeds',1);
add_action('do_feed_rss','disable_our_feeds',1);
add_action('do_feed_rss2','disable_our_feeds',1);
add_action('do_feed_atom','disable_our_feeds',1);
}
//禁止Pingback
if(lmy_get_option('Pingback_off')) {
add_action('pre_ping', '_noself_ping');
function _noself_ping(&$links) {
	$home = get_option('home');
	foreach ($links as $l => $link) {
		if (0 === strpos($link, $home)) {
			unset($links[$l]);
		}
	}
}
}
// 屏蔽后台隐私
if (lmy_get_option('no_admin')) {
	remove_action('user_request_action_confirmed', '_wp_privacy_account_request_confirmed');
	remove_action('user_request_action_confirmed', '_wp_privacy_send_request_confirmation_notification', 12);
	remove_action('wp_privacy_personal_data_exporters', 'wp_register_comment_personal_data_exporter');
	remove_action('wp_privacy_personal_data_exporters', 'wp_register_media_personal_data_exporter');
	remove_action('wp_privacy_personal_data_exporters', 'wp_register_user_personal_data_exporter', 1);
	remove_action('wp_privacy_personal_data_erasers', 'wp_register_comment_personal_data_eraser');
	remove_action('init', 'wp_schedule_delete_old_privacy_export_files');
	remove_action('wp_privacy_delete_old_export_files', 'wp_privacy_delete_old_export_files');
	add_filter('option_wp_page_for_privacy_policy', '__return_zero');
}
// 移除 Emoji
if(lmy_get_option('emoji_off')) {
function disable_emojis() {
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
}
add_action( 'init', 'disable_emojis' );
function disable_emojis_tinymce( $plugins ) {
return array_diff( $plugins, array( 'wpemoji' ) );
}
}

if(lmy_get_option('remove_dns_refresh')) {
	function remove_dns_prefetch( $hints, $relation_type ) {
		if ( 'dns-prefetch' === $relation_type ) {
			return array_diff( wp_dependencies_unique_hosts(), $hints );
		}
		return $hints;
	}
	add_filter( 'wp_resource_hints', 'remove_dns_prefetch', 10, 2 );
}
// RSS cache
if ( !lmy_get_option( 'feed_cache' ) == '' ) {
	add_filter( 'wp_feed_cache_transient_lifetime' , 'feed_cache_time' );
	function feed_cache_time( $seconds ) {
		return lmy_get_option( 'feed_cache' );
	}
}
/*模块类*/
//顶部搜索模块
if(lmy_get_option('index_Search')) {
function index_Search(){   
get_template_part('Inc/Module/index_Search' );
}
add_action('my_index_top','index_Search');
}
//五格幻灯片模块
if(lmy_get_option('Fivebarslide')) {
function Fivebarslide(){   
get_template_part('Inc/Module/Fivebarslide' );
}
add_action('my_index_top','Fivebarslide');
}
//用户展示模块
if(lmy_get_option('hotUser')) {
function hotUser(){   
get_template_part('Inc/Module/hotUser' );
}
add_action('my_index_bottom','hotUser');
}
//底部统计模块
if(lmy_get_option('siteCount')) {
function siteCount(){   
get_template_part('Inc/Module/siteCount' );
}
add_action('my_index_bottom','siteCount');
}
/*waf*/
if(lmy_get_option('waf_yzm')) {
//后台登陆数学验证码
function loper_login_english_figures() {
$num1=rand(0,99);
$num2=rand(0,99);
echo "<p>
<label for='math' class='small'>验证码</label>
<input id='math' type='text' name='sum' class='input' size='25' placeholder='$num1 + $num2 = ? '>
<input type='hidden' name='num1' value='$num1'>
<input type='hidden' name='num2' value='$num2'></p>";
}
add_action('login_form','loper_login_english_figures');
# 判断验证码是否空白和错误
function loper_login_calculation($redirect, $bool, $errors) {
if (isset($_POST['sum'])&&isset($_POST['num1'])&&isset($_POST['num2'])) {
$sum=$_POST['sum'];
switch($sum){
case $_POST['num1']+$_POST['num2']:break;
case null:$errors->add( 'zlinet', "<strong>错误</strong>：请输入验证码！" ); break;
default:$errors->add( 'zlinet', "<strong>错误</strong>：验证码不正确！" );}
}
    add_action('login_redirect','loper_login_calculation', 9, 9);
}
}
//保护后台登录
if(lmy_get_option('waf_url')) {
add_action('login_enqueue_scripts','login_protection');  
function login_protection(){  
    if($_GET[lmy_get_option('waf_url_get')] != lmy_get_option('waf_url_pass'))header('Location: '.home_url().'');  
}
}
/**后台不可设置类**/
// 头部冗余代码
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'feed_links_extra', 3 );
remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );
// 禁止后台加载谷歌字体
function wp_remove_open_sans_from_wp_core() {
	wp_deregister_style( 'open-sans' );
	wp_register_style( 'open-sans', false );
	wp_enqueue_style('open-sans','');
}
add_action( 'init', 'wp_remove_open_sans_from_wp_core' );
// 禁止代码标点转换
remove_filter( 'the_content', 'wptexturize' );
// 屏蔽自带小工具
function remove_default_wp_widgets() {
	unregister_widget('WP_Widget_Recent_Comments');
	unregister_widget('WP_Widget_Tag_Cloud');
	unregister_widget('WP_Widget_Recent_Posts');
	unregister_widget('WP_Widget_Meta');
	unregister_widget('WP_Widget_Media_Gallery');
	unregister_widget('WP_Widget_Categories');
	unregister_widget('WP_Widget_RSS');
	unregister_widget('WP_Widget_Pages');
}
add_action('widgets_init', 'remove_default_wp_widgets', 11);
// 移除wp-json链接
remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );
// 隐藏WP标志
function hidden_admin_bar_remove() {
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu('wp-logo');
}
add_action('wp_before_admin_bar_render', 'hidden_admin_bar_remove', 0);
//移除 WP_Head 无关紧要的代码
remove_action('wp_head', 'wp_generator');
//删除 head 中的 WP 版本号
remove_action('wp_head', 'rsd_link');
//删除 head 中的 RSD LINK
remove_action('wp_head', 'wlwmanifest_link');
//删除 head 中的 Windows Live Writer 的适配器？
remove_action('wp_head', 'feed_links_extra', 3);
//删除 head 中的 Feed 相关的link
//remove_action( 'wp_head', 'feed_links', 2 );
remove_action('wp_head', 'index_rel_link');
//删除 head 中首页，上级，开始，相连的日志链接
remove_action('wp_head', 'parent_post_rel_link', 10);
remove_action('wp_head', 'start_post_rel_link', 10);
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
//删除 head 中的 shortlink
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
// 删除头部输出 WP RSET API 地址
remove_action('wp_head', 'rest_output_link_wp_head', 10);
//禁止短链接 Header 标签。
remove_action('template_redirect', 'wp_shortlink_header', 11);
// 禁止输出 Header Link 标签。
remove_action('template_redirect', 'rest_output_link_header', 11);
//删除中文包中的一些无用代码
add_action('init', 'remove_zh_ch_functions');
function remove_zh_ch_functions() {
	remove_action('admin_init', 'zh_cn_l10n_legacy_option_cleanup');
	remove_action('admin_init', 'zh_cn_l10n_settings_init');
	wp_embed_unregister_handler('tudou');
	wp_embed_unregister_handler('youku');
	wp_embed_unregister_handler('56com');
}
// 移除隐私功能
add_action('admin_menu', function () {
	global $menu, $submenu;
	unset($submenu['options-general.php'][45]);
	remove_action( 'admin_menu', '_wp_privacy_hook_requests_page' );
},9);
//修改底部
function footerText () {
	return '<span id="footer-thankyou">感谢使用<a href="https://cn.wordpress.org/" target="_blank">WordPress</a>进行创作。</span> | 本站由不错吧技术部二次开发微信：ATMJGY：'.THEME_VERSION.' | <a href="https://www.wwttl.com/" title="不错吧" target="_blank">不错吧</a>';
}
add_filter('admin_footer_text', 'footerText', 9999);

