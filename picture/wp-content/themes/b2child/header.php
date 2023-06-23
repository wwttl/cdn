<!doctype html>
<html <?php language_attributes(); ?> class="avgrund-ready b2dark">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover" />
<meta http-equiv="Cache-Control" content="no-transform" />
<meta http-equiv="Cache-Control" content="no-siteapp" />
<meta name="renderer" content="webkit"/>
<meta name="force-rendering" content="webkit"/>
<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1"/>
<link rel="profile" href="http://gmpg.org/xfn/11">
<meta name="theme-color" content="<?php echo b2_get_option('template_top','gg_bg_color'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo esc_url(home_url()) ?>/wp-content/themes/b2child/Assets/Css/Modular/bwveur0fle.css" />
<script src="<?php echo esc_url(home_url()) ?>/wp-content/themes/b2child/Assets/Js/Modular/bwveur0fle.js"></script>
<script src="<?php echo esc_url(home_url()) ?>/wp-content/themes/b2child/Assets/Js/jquery.min.js"></script>
<script src="/wp-content/themes/b2child/Assets/Js/instantpage.js" type="module" integrity="sha384-xxHashSHA3+base64"></script>
<script>
function b2loadScript(url, id,callback){
var script = document.createElement ("script")
script.type = "text/javascript";
script.id = id;
if (script.readyState){ //IE
script.onreadystatechange = function(){
if (script.readyState == "loaded" || script.readyState == "complete"){
script.onreadystatechange = null;
callback();
}
};
} else { //Others
script.onload = function(){
callback();
};
}
script.src = url;
document.getElementsByTagName("head")[0].appendChild(script);
}
function b2getCookie(name){
var nameEQ = name + "=";
var ca = document.cookie.split(';');
for(var i=0;i < ca.length;i++) {
var c = ca[i];
while (c.charAt(0)==' ') c = c.substring(1,c.length);
if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
}
return null;
}

function b2setCookie(name,value){

let days = b2_global.login_keep_days
var expires = "";
if (days) {
var date = new Date();
date.setTime(date.getTime() + (days*24*60*60*1000));
expires = "; expires=" + date.toUTCString();
}
document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}

function b2delCookie(name){
document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}

// b2loadScript('<?php echo B2_THEME_URI;?>/Assets/fontend/library/darkreader.js?ver=<?php echo B2_VERSION; ?>','b2-dark',()=>{
// DarkReader.enable({
// brightness: 100,
// contrast: 110,
// sepia: 30
// });
// })
</script>
<?php wp_head();?>
<?php do_action('toutiao_push'); ?>
<?php if (lmy_get_option("seo_ttsssjyz")) {if ( is_single() || is_page() ) { ?>
<!--头条搜索时间因子-->
        <meta property="bytedance:published_time" content="<?php echo get_the_time('Y-m-d\TH:i:s+08:00'); ?>" />
        <meta property="bytedance:updated_time" content="<?php echo get_the_modified_time('Y-m-d\TH:i:s+08:00'); ?>" />
    <?php } } ?>
    <?php if (lmy_get_option("page_dbjdt")) { ?>
    <!--顶部加载进度条-->
    <div id="percentageCounter"></div>
    <!--顶部加载进度条-->
    <?php } ?>
</head>
<body <?php body_class(b2_get_option('template_top','top_type')); ?>>
<?php 
$bg = b2_get_option('template_main','bg_image'); 
$bg_repeat = b2_get_option('template_main','bg_image_repeat');
if($bg && $bg_repeat == 2){
echo '<div class="b2-page-bg">
<img src="'.b2_get_thumb(array('thumb'=>$bg,'width'=>80,'height'=>'100%')).'" />
</div>';
}
?>

<div id="page" class="site">

<?php do_action('b2_header'); ?>
<!-- <div class="topshory-box">
<div class="wrapper">
<img class="topshory-bunner" src="http://192.168.1.5:2256/wp-content/uploads/2022/06/v2-a4ff18cc184e45b953e949ffff1f3f8c.jpg" />
</div>
</div> -->
<div id="content" class="site-content">

<?php do_action('b2_content_before'); ?>