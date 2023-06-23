<?php
//B2子主题目录url
define( 'B2_CHILD_URI', get_stylesheet_directory_uri() );
//加载父级样式(一般不用修改)
add_action( 'wp_enqueue_scripts', 'parent_theme_enqueue_styles',9 );
function parent_theme_enqueue_styles() {
    //加载父主题样式文件
    wp_enqueue_style( 'parent-style-main', get_template_directory_uri() . '/style.css',array() , B2_VERSION, 'all' );
    wp_enqueue_style( 'parent-style', get_template_directory_uri().'/Assets/fontend/style.css',array() , B2_VERSION, 'all');
}
//加载子主题样式
add_action( 'wp_enqueue_scripts', 'child_theme_enqueue_styles',99 );
function child_theme_enqueue_styles() {
    //禁用当前子主题默认加载项
    wp_dequeue_style( 'b2-style-main' );
    wp_dequeue_style( 'b2-style' );
    //默认子主题样式
    if (lmy_get_option("page_default")) {
    //加载子主题样式文件，使它在所有样式之后
    wp_enqueue_style( 'child-style', B2_CHILD_URI.'/style.css' , array() , B2_VERSION, 'all');
    //加载子主题JS文件
    wp_enqueue_script( 'b2-child', B2_CHILD_URI.'/child.js', array(), B2_VERSION , true );
    }
    //加载子主题必备JS文件
    wp_enqueue_script( 'b2-jquery', B2_CHILD_URI.'/Assets/Js/jquery.min.js', array(), B2_VERSION , true );
    //评论一言
    if (lmy_get_option("page_yiyan")) {
    wp_enqueue_script( 'b2-yiyan', B2_CHILD_URI.'/Assets/Js/yiyan.js', array(), B2_VERSION , true );
    }
    //动态网站标题
    if (lmy_get_option("page_dtwzbt")) {
    wp_enqueue_script( 'b2-dtwzbt', B2_CHILD_URI.'/Assets/Js/dtwzbt.js', array(), B2_VERSION , true );
    }
    //元素飘落枫叶效果
    if (lmy_get_option("page_fyxg")) {
    wp_enqueue_script( 'b2-fyxg', B2_CHILD_URI.'/Assets/Js/fypl.js', array(), B2_VERSION , true );
    }
    //FPS帧
    if (lmy_get_option("page_fps")) {
    wp_enqueue_script( 'b2-fps', B2_CHILD_URI.'/Assets/Js/fps.js', array(), B2_VERSION , true );
    }
    //每年12月13日全站变灰
    if (lmy_get_option("page_jnr")) {
    wp_enqueue_script( 'b2-jnr', B2_CHILD_URI.'/Assets/Js/jnr.js', array(), B2_VERSION , true );
    }
    //禁止F12
    if (lmy_get_option("page_jzf12")) {
    wp_enqueue_script( 'b2-f12', B2_CHILD_URI.'/Assets/Js/f12.js', array(), B2_VERSION , true );
    }
    //禁止F12
    if (lmy_get_option("page_jyyj")) {
    wp_enqueue_script( 'b2-jyyj', B2_CHILD_URI.'/Assets/Js/jyyj.js', array(), B2_VERSION , true );
    }
    //顶部进度条
    if (lmy_get_option("page_dbjdt")) {
    wp_enqueue_script( 'b2-dbjdt', B2_CHILD_URI.'/Assets/Js/dbjdt.js', array(), B2_VERSION , true );
    }
    //pc三栏模块
    if (lmy_get_option("home_pcslmk")) {
    wp_enqueue_style( 'b2-pcslmk', B2_CHILD_URI.'/Assets/Css/pcslmk.css' , array() ,  B2_VERSION, 'all');
    }
    //部分图标加粗
    if (lmy_get_option("page_bold")) {
    wp_enqueue_style( 'b2-bold', B2_CHILD_URI.'/Assets/Css/bold.css' , array() ,  B2_VERSION, 'all');
    }
    //手机端底部样式
    if (lmy_get_option("footer_mh")) {
    wp_enqueue_style( 'b2-footer', B2_CHILD_URI.'/Assets/Css/footer/'.lmy_get_option("footer_css").'.css' ,array() , B2_VERSION, 'all');
    }
    //稀奇古怪的弹窗
    if (lmy_get_option("page_tc")) {
    wp_enqueue_script( 'element-ui','/Assets/Js/qgtc.js' ,array() , B2_VERSION,  true );
    wp_enqueue_style( 'element-ui','/Assets/Css/qgtc.css' ,array() , B2_VERSION, 'all');
    wp_enqueue_script( 'b2-tc',B2_CHILD_URI.'/Assets/Js/tc.js' ,array() , B2_VERSION,  true );
    }
    //PC右侧悬浮工具条美化
    if (lmy_get_option("page_ycxfgjt")) {
    wp_enqueue_style( 'b2-ycxfgjt', B2_CHILD_URI.'/Assets/Css/ycxfgjt.css' , array() ,  B2_VERSION, 'all');
    }
    //PC右侧悬浮工具条美化
    if (lmy_get_option("page_dmgl")) {
    wp_enqueue_script( 'b2-dmgl', B2_CHILD_URI.'/Assets/Js/dmgl.js' , array() ,  B2_VERSION,  true );
    wp_enqueue_style( 'b2-dmgl', B2_CHILD_URI.'/Assets/Css/dmgl.css' , array() ,  B2_VERSION, 'all');
    }
    //夜间模式
    if (lmy_get_option("page_night")) {
    wp_enqueue_script( 'b2-night', B2_CHILD_URI.'/Assets/Js/night.js' , array() ,  B2_VERSION,  true );
    wp_enqueue_style( 'b2-night', B2_CHILD_URI.'/Assets/Css/night.css' , array() ,  B2_VERSION, 'all');
    }
    //评论打卡
    if (lmy_get_option("page_dk")) {
    wp_enqueue_script( 'b2-dk', B2_CHILD_URI.'/Assets/Js/dk.js' , array() ,  B2_VERSION,  true );
    }
    //导航会员模块
    if (lmy_get_option("modular_navigation_member_block")) {
    wp_enqueue_style( 'b2-navigation-member-block', B2_CHILD_URI.'/Assets/Css/Modular/navigation-member-block.css' , array() ,  B2_VERSION, 'all');
    wp_enqueue_script( 'b2-navigation-member-block', B2_CHILD_URI.'/Assets/Js/Modular/navigation-member-block.js' , array() ,  B2_VERSION,  true );
    }
    //导航区块模块
    if (lmy_get_option("modular_navigation_block")) {
    wp_enqueue_style( 'b2-navigation-block', B2_CHILD_URI.'/Assets/Css/Modular/navigation-block.css' , array() ,  B2_VERSION, 'all');
    wp_enqueue_script( 'b2-font_3169120_wjprs1djc7p','//at.alicdn.com/t/font_3169120_wjprs1djc7p.js' , array() ,  B2_VERSION,  true );
    }
    //双图导航-移动端
    if (lmy_get_option("modular_dual_map_navigation")) {
    wp_enqueue_style( 'b2-dual-map-navigation', B2_CHILD_URI.'/Assets/Css/Modular/dual-map-navigation.css' , array() ,  B2_VERSION, 'all');
    }
    //三图导航-移动端
    if (lmy_get_option("modular_three_map_navigation")) {
    wp_enqueue_style( 'b2-three-map-navigation', B2_CHILD_URI.'/Assets/Css/Modular/three-map-navigation.css' , array() ,  B2_VERSION, 'all');
    }
    //视频搜索模块
    if (lmy_get_option("index_Search")) {
    wp_enqueue_style( 'b2-index_Search', B2_CHILD_URI.'/Assets/Css/Modular/index_Search.css' , array() ,  B2_VERSION, 'all');
    wp_enqueue_script( 'b2-index_Search', B2_CHILD_URI.'/Assets/Js/Modular/index_Search.js' , array() ,  B2_VERSION,  true );
    }
    //用户展示
    if (lmy_get_option("hotUser")) {
    wp_enqueue_style( 'b2-hotUser', B2_CHILD_URI.'/Assets/Css/Modular/hotuser.css' , array() ,  B2_VERSION, 'all');
    }
    //底部统计
    if (lmy_get_option("siteCount")) {
    wp_enqueue_style( 'b2-siteCount', B2_CHILD_URI.'/Assets/Css/Modular/sitecount.css' , array() ,  B2_VERSION, 'all');
    }
    //H标签美化
    if (lmy_get_option("h_label")) {
    wp_enqueue_style( 'b2-h', B2_CHILD_URI.'/Assets/Css/h/'.lmy_get_option("h_label_css").'.css' , array() ,  B2_VERSION, 'all');
    }
    //鼠标特效
    if (lmy_get_option("cursor")) {
    wp_enqueue_script( 'b2-cursor', B2_CHILD_URI.'/Assets/Js/cursor/'.lmy_get_option("cursor_js").'.js' , array() ,  B2_VERSION, true );
    }
    //全局圆角
    if (lmy_get_option("fillet")) {
    wp_enqueue_style( 'b2-fillet', B2_CHILD_URI.'/Assets/Css/fillet.css' , array() ,  B2_VERSION, 'all');
    }
    //全局小图标
    if (lmy_get_option("iconfont")) {
    wp_enqueue_style( 'b2-iconfont_css',lmy_get_option("iconfont_css") , array() ,  B2_VERSION, 'all');
    wp_enqueue_script( 'b2-iconfont_js',lmy_get_option("iconfont_js") , array() ,  B2_VERSION,  true );
    }
    //加载进度条
    if (lmy_get_option("page_pace")) {
    wp_enqueue_script( 'b2-pace', B2_CHILD_URI.'/Assets/Js/pace.min.js' , array() ,  B2_VERSION , true );
    wp_enqueue_style( 'b2-pace-css', B2_CHILD_URI.'/Assets/Css/pace/'.lmy_get_option("pace_css").'.min.css' ,array() , B2_VERSION, 'all');
    }
}
//版本
$themedata = wp_get_theme();$themeversion = $themedata['Version'];
define('THEME_VERSION', $themeversion);
//后台载入
if ( is_admin() ) :
function be_options_css() {
wp_enqueue_style( 'options', B2_CHILD_URI . '/Inc/options/assets/options.css', array(), B2_VERSION );
wp_enqueue_style( 'fonts', B2_CHILD_URI . '/Inc/options/assets/fonts/fonts.css', array(), B2_VERSION );
}
add_action( 'init', 'be_options_css', 20 );
endif;
require_once plugin_dir_path( __FILE__ ) .'/Inc/options/classes/setup.class.php';
require_once plugin_dir_path(__FILE__) .'/Inc/options/b2child-options.php';
//主题后台更新
if (lmy_get_option("update")) {
    require 'Inc/update-checker/update-checker.php';
    function UpdateCheck($url,$flag = 'B2子主题'){
        return Puc_v4_Factory::buildUpdateChecker(
            $url,
            __FILE__,
            $flag
        );
    }
    switch(lmy_get_option('update_source')){
        case 'upyun':
            $ThemeUpdateChecker = UpdateCheck('https://www.wwttl.com/b2child/upyun.json');
            break;
        case 'official_building':
            $ThemeUpdateChecker = UpdateCheck('https://www.wwttl.com/b2child/check.json');
    }
    }
//核心文件
if (file_exists(get_theme_file_path('/b2.php'))) {
    require_once get_theme_file_path('/b2.php');
}
//ip属地显示
if (lmy_get_option("page_ipgsd")) {
    require_once get_theme_file_path('/Inc/plugins/ip/ip2c.php');
}
//邮件美化
if (lmy_get_option("page_email")) {
    require_once get_theme_file_path('/Inc/plugins/email.php');
}
 //气泡弹幕
if (lmy_get_option("page_dm")) {
require_once get_theme_file_path('/Inc/plugins/DataApi.php');
}
//图片压缩
if (lmy_get_option("other_tpys")) {
require_once get_theme_file_path('/Inc/plugins/picture-compression.php');
}
//全局关键词
if (lmy_get_option("keyword_link")) {
require_once get_theme_file_path('/Inc/plugins/keyword-link.php');
}
//快速收录
if (lmy_get_option("baidu_daily")) {
require_once get_theme_file_path('/Inc/plugins/baidu-daily.php');
}
//用户注销
if (lmy_get_option("delaccount")) {
require_once get_theme_file_path('Inc/plugins/delaccount.php');
}
//气泡弹幕
require_once get_theme_file_path('/Inc/plugins/Data.php');
//推送相关
require_once get_theme_file_path('/Inc/plugins/notice.php');
//机器人推送
require_once get_theme_file_path('/Inc/plugins/fun-bot.php');
//微博同步
require_once get_theme_file_path('/Inc/plugins/weibo.php');
//DogeCloud 云存储
require_once get_theme_file_path('/Inc/plugins/dogecloud.php');
//火山引擎 ImageX
require_once get_theme_file_path('/Inc/plugins/volcengine.php');
//面包屑
require_once get_theme_file_path('/Inc/plugins/crumbs.php');
//自组添加功能
require_once get_theme_file_path('/Inc/plugins/inp.php');
//后台模块
function get_all_cat_id() { ?>
<div class="to-up"><div class="to-area"></div></div>
<div class="to-down"><div class="to-area"></div></div>
<?php }
