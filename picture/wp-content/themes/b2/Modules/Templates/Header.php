<?php
namespace B2\Modules\Templates;

class Header{

    public function init(){
        add_action('b2_header',array($this,'header_cache'),3);
        add_action( 'wp_update_nav_menu', array($this,'flash_cache'));
    }

    public function flash_cache(){
        wp_cache_delete('b2_header_html','b2_template');
    }

    // public static function gg_box(){
    //     return '
    //     <div id="gg-box" :class="[\'wrapper gg-box b2-radius\',{\'show-gg-box mg-b\':show}]" v-cloak>
    //         <div class="gg-title"><a href="'.b2_get_custom_page_url('announcements').'">'.__('公告','b2').'</a></div>
    //         <div class="gg-content"><span v-text="ggdata.date" class="gg-date"></span><a :href="ggdata.href" target="_blank" class="gg-link"><span v-text="ggdata.title"></span></a>'.b2_get_icon('b2-jiantou').'<span v-text="ggdata.desc" class="gg-desc"></span></div>
    //     </div>';
    // }

    public static function header_style(){
        

        $text_color = b2_get_option('template_top','gg_text_color');
        $rgb = \B2\Modules\Common\FileUpload::hex2rgb($text_color);
        $bg = b2_get_option('template_top','gg_bg_color');
        $bg2 = b2_get_option('template_top','gg_bg_color_2');
        $text_color_2 = b2_get_option('template_top','gg_text_color_2');
        return '
        <style>
                .header-banner{
                    background-color:'.$bg.'
                }
                .header-banner .ym-menu a,.header-banner,.social-top .site-title,.top-search-button button,.top-search input,.login-button .b2-account-circle-line
                {
                    color:'.$text_color.';
                    fill: '.$text_color.';
                }
                .social-top .login-button .b2-user{
                    color:'.$text_color.';
                    fill: '.$text_color.';
                }
                .top-search-select{
                    border-right-color:rgba('.$text_color.',.5);
                    
                }
                .top-search input::placeholder {
                    color: '.$text_color.';
                }
                .header{
                    background-color:'.$bg2.';
                    color:'.$text_color_2.'
                }
                .header .button,.header .login-button button{
                    border-color:'.$text_color_2.';
                    color:'.$text_color_2.';
                }
                .header .header-logo{
                    color:'.$text_color_2.'
                }
                @media screen and (max-width: 768px){
                    .logo-center .header-banner-left,.logo-left .header-banner-left,.menu-center .header-banner-left,.logo-top .header-banner-left{
                        background:none
                    }
                    .header-banner-left{
                        background:'.$bg2.';
                    }
                    .header .mobile-box{
                        color: initial;
                    }
                    .logo-center .login-button .b2-account-circle-line,
                    .logo-left .login-button .b2-account-circle-line,
                    .menu-center .login-button .b2-account-circle-line,
                    .logo-top .login-button .b2-account-circle-line{
                        color:'.$text_color_2.'
                    }
                    .logo-center .menu-icon .line-1,.logo-center .menu-icon .line-2,.logo-center .menu-icon .line-3,
                    .social-top .menu-icon .line-1,.social-top .menu-icon .line-2,.social-top .menu-icon .line-3,
                    .logo-left .menu-icon .line-1,.logo-left .menu-icon .line-2,.logo-left .menu-icon .line-3,
                    .menu-center .menu-icon .line-1,.menu-center .menu-icon .line-2,.menu-center .menu-icon .line-3,
                    .logo-top .menu-icon .line-1,.logo-top .menu-icon .line-2,.logo-top .menu-icon .line-3
                    {
                        background:'.$text_color_2.'
                    }
                    .social-top .header-banner .ym-menu a{
                        color:'.$text_color_2.'
                    }
                }
                
            </style>
        ';
    }

    /**
     * 页面顶部
     *
     * @return string 顶部的HTML代码
     * @author Li Ruchun <lemolee@163.com>
     * @version 1.0.0
     * @since 2018
     */
    public static function header(){

        $type = b2_get_option('template_top','top_type');

        $html = self::header_style();
        
        if($type === 'social-top'){

            $arg = array(
                'theme_location' => 'ym-menu',
                'container_id'=>'ym-menu',
                'container_class'=> 'ym-menu',
                'echo' => FALSE,
                // 'fallback_cb' => '__return_false',
                // 'walker' => new Menu()
            );
    
            $menu = wp_nav_menu($arg);

            $sub_menu = self::sub_menu();
            if (lmy_get_option("page_b2child_vip")) {
            $page_b2child_vip_text = lmy_get_option('page_b2child_vip_text');
            $page_b2child_vip_rmb = lmy_get_option('page_b2child_vip_rmb');
            $b2child_vip='<style>
.abcd_benefitTag_benefits-wrapper {position:relative;margin-right: 5px;display:flex;justify-content:center;height:53px;}.benefitTag__benefits-icon___3aeab {display:inline-block;width:32px;height:32px;}.benefitTag__benefits-text___5d19f {font-size:12px;color:#4d3626;font-weight:600;position:absolute;bottom:0;left:50%;-webkit-transform:translateX(-50%);transform:translateX(-50%);white-space:nowrap;}.benefitTag__popover___fbaea {min-width:0;max-width:200px;z-index:9;}.abcd_memberWrapper {position: relative;padding: 5px 20px 20px;cursor: pointer;display: flex;flex-direction: column;background-color: #ffffff87;backdrop-filter: blur(10px);align-items: center;border-radius: 0px 0px 10px 10px;box-shadow: 0px 5px 40px 0px rgba(17,58,93,.1);}.abcd_memberWrapper .abcd_memberCard_member-header {position:relative;padding-top:12px;font-weight:700;white-space:nowrap;text-align:center;margin-bottom:16px;width: 300px;box-sizing:border-box;}.abcd_memberWrapper .abcd_memberCard_member-header .abcd_memberCard_member-title {font-size:16px;line-height:22px;color:#663f32;letter-spacing:0;font-weight:600;margin-bottom:4px;}.abcd_memberWrapper .abcd_memberCard_member-header .abcd_memberCard_member-desc {font-size:12px;color:#663f32;letter-spacing:0;line-height:17px;font-weight:400;}.abcd_memberWrapper.abcd_memberCard_none .abcd_memberCard_member-header .abcd_memberCard_member-title {height:25px;width:180px;margin-left:auto;margin-right:auto;}.abcd_memberWrapper.memberCard__expired___9de29 .abcd_memberCard_member-header,.abcd_memberWrapper.memberCard__will-expired___3128d .abcd_memberCard_member-header {background:hsla(0,0%,100%,.8);padding:16px 0;border-radius:8px;}.abcd_memberWrapper.memberCard__expired___9de29 {background:#dadde0;}.abcd_memberWrapper.memberCard__expired___9de29 .abcd_memberCard_member-header .abcd_memberCard_member-desc {color:#ff2b00;}.abcd_memberWrapper .abcd_memberCard_member-benefits {position:relative;width: 100%;box-sizing:border-box;background:hsla(0,0%,100%,.8);border-radius:8px;margin-bottom:16px;white-space:nowrap;overflow:hidden;}.abcd_memberWrapper .abcd_memberCard_member-benefits li {float: left;width: 25%;margin: 10px 0;}.abcd_memberWrapper .abcd_memberCard_member-benefits .memberCard__leftBtn___ec917,.abcd_memberWrapper .abcd_memberCard_member-benefits .memberCard__rightBtn___0cc46 {position:absolute;top:50%;width:12px;height:28px;-webkit-transform:translateY(-50%);transform:translateY(-50%);background-color:rgba(230,163,115,.2);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .25s;}.abcd_memberWrapper .abcd_memberCard_member-benefits .memberCard__leftBtn___ec917 .memberCard__icon-font___9846a,.abcd_memberWrapper .abcd_memberCard_member-benefits .memberCard__rightBtn___0cc46 .memberCard__icon-font___9846a {color:#4d3626;font-size:12px;-webkit-transform:scale(.5);transform:scale(.5);}.abcd_memberWrapper .abcd_memberCard_member-benefits .memberCard__leftBtn___ec917:hover,.abcd_memberWrapper .abcd_memberCard_member-benefits .memberCard__rightBtn___0cc46:hover {background-color:rgba(230,163,115,.16);}.abcd_memberWrapper .abcd_memberCard_member-benefits .memberCard__leftBtn___ec917 {left:0;border-radius:0 4px 4px 0;}.abcd_memberWrapper .abcd_memberCard_member-benefits .memberCard__rightBtn___0cc46 {right:0;border-radius:4px 0 0 4px;}.abcd_memberWrapper .memberCard__joinBtn___a50c1 {position:relative;width:152px;height:32px;background-image:linear-gradient(134deg,#4d5580,#3d4466);font-size:14px;color:#fff3eb;font-weight:600;display:flex;align-items:center;justify-content:center;cursor:pointer;}.abcd_memberWrapper>button{border: 0!important;}.abcd_memberWrapper .memberCard__joinBtn___a50c1:hover {background-image:linear-gradient(134deg,rgba(77,85,128,.9),rgba(61,68,102,.9));}.abcd_memberWrapper .memberCard__joinBtn___a50c1:active {background-image:linear-gradient(134deg,rgba(77,85,128,.8),rgba(61,68,102,.8));}.abcd_memberWrapper .memberCard__joinBtn___a50c1 .memberCard__tips___3220a {top:-14px;right:-54px;position:absolute;background-image:linear-gradient(90deg,#ff9580,#f36);border-radius:6px;color:#fff;letter-spacing:0;text-align:center;font-weight:100;padding:2px 8px;font-size:20px;-webkit-transform:scale(.5);transform:scale(.5);}.abcd_memberCard_benefitsLiWrapper {position:relative;left:0;transition:left .3s linear;}.abcd_header_vip_topi {padding:2px 6px 0 6px;right: 0px;line-height:24px;color:#fff;text-align:center;position: absolute;top: 5px;box-sizing:border-box;border-radius:16px;font-size:19px;white-space:nowrap;-webkit-transform:scale(.5);}.abcd_header_vip {padding:0 24px;position:relative;width:20px;height: 70px;background:url(/wp-content/themes/b2child/Assets/img/vipiconhover.svg) no-repeat 50%/25px;}.abcd_header_vip>i {position:absolute;top: 5px;right:-6px;}.abcd_header_vip:hover .abcd_member_tip[data-status=true] {display:block;}.abcd_member_tip {position:absolute;top:70px;right:-14px;display:none;z-index:8000;}.abcd_header_vip i {font-style: normal;font-weight: 400;}.jiaobiao_color4{color:#fff;background-color:#ff2a8e;}@media (max-width: 780px) {.abcd_header_vip {display: none;}}
</style>
					<a href="/vips" class="abcd_header_vip nav-member-btn"
draggable="false" style="padding: 0px 18px; width: 0px;background:url(/wp-content/themes/b2child/Assets/img/vipiconhover.svg) no-repeat 10%/25px;">
    <div class="icon-btn">
    </div>
    <i class="abcd_header_vip_topi jiaobiao_color4" style="right: -15px;">开通</i>
    <div class="abcd_member_tip" data-status="true">
        <div data-position="" class="abcd_memberWrapper abcd_memberCard_none">
            <div class="abcd_memberCard_member-header">
                <div class="abcd_memberCard_member-title">'.$page_b2child_vip_text.'</div>
                <div class="abcd_memberCard_member-desc">一次购买，免费更新</div>
            </div>
            <ul class="abcd_memberCard_member-benefits">
                <div class="abcd_memberCard_benefitsLiWrapper" style="left: 0px;"> <li>
                                            <div class="abcd_benefitTag_benefits-wrapper">
                                                <i class="benefitTag__benefits-icon___3aeab" style="background: url(/wp-content/themes/b2child/Assets/img/20220707012917663.svg) center center / contain no-repeat;">
                                                </i>
                                                <span class="benefitTag__benefits-text___5d19f">
                                                  只购1次
                                                </span>
                                            </div>
                                        </li> <li>
                                            <div class="abcd_benefitTag_benefits-wrapper">
                                                <i class="benefitTag__benefits-icon___3aeab" style="background: url(/wp-content/themes/b2child/Assets/img/20220707012915212.svg) center center / contain no-repeat;">
                                                </i>
                                                <span class="benefitTag__benefits-text___5d19f">
                                                  免费指导
                                                </span>
                                            </div>
                                        </li> <li>
                                            <div class="abcd_benefitTag_benefits-wrapper">
                                                <i class="benefitTag__benefits-icon___3aeab" style="background: url(/wp-content/themes/b2child/Assets/img/20220707012914275.svg) center center / contain no-repeat;">
                                                </i>
                                                <span class="benefitTag__benefits-text___5d19f">
                                                  保持更新
                                                </span>
                                            </div>
                                        </li> <li>
                                            <div class="abcd_benefitTag_benefits-wrapper">
                                                <i class="benefitTag__benefits-icon___3aeab" style="background: url(/wp-content/themes/b2child/Assets/img/20220707012912468.svg) center center / contain no-repeat;">
                                                </i>
                                                <span class="benefitTag__benefits-text___5d19f">
                                                  免费更新
                                                </span>
                                            </div>
                                        </li></div>
            </ul>
            <button class="memberCard__joinBtn___a50c1">
                开通会员
                <div class="memberCard__tips___3220a">
                 现价'.$page_b2child_vip_rmb.'元
                </div>
            </button>
        </div>
    </div>
</a>';}else{
            $b2child_vip='';}

            $html .= '
            <div class="site-header mg-b social-top '.($sub_menu ? '' : 'social-no-sub').'"><div class="site-header-in">';
            $html .= '<div class="header-banner top-style">
                <div class="header-banner-content wrapper">
                    <div class="header-banner-left">
                        '.self::logo().'
                        '.$menu.'
                    </div>
                    <div class="header-banner-right">
                        '.$b2child_vip.'
                        '.self::search_form().'
                        '.self::user().'
                        <div class="mobile-show top-style-menu">
                            <div id="mobile-menu-button" class="menu-icon" onclick="mobileMenu.showAc()">
                                <div class="line-1"></div>
                                <div class="line-2"></div>
                                <div class="line-3"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
    
            $html .= $sub_menu;
    
            $html .= '</div></div>';
            
        }else{

            $html .= '<div class="site-header mg-b"><div class="site-header-in">';
            $html .= self::header_top();
    
            $html .= '<div class="header '.$type.'">
                <div class="top-style">
                    <div class="top-style-blur"></div>
                    <div class="wrapper">
                        '.self::menu_icon().'
                        '.self::logo().'
                        '.self::user().'
                        '.self::menu().'
                    </div>
                </div>
            </div>';
    
            $html .= '</div></div>';
        }

        return apply_filters('b2_top_html',$html, $type);
    }

    public static function search_form(){
        $post_type = b2_get_search_type();
        unset($post_type['cpay']);
        $opt_type = b2_get_option('template_top','search_menu');
        if(!$opt_type){
            $opt_type = $post_type;
        }

        $_post_type = [];

        foreach ($opt_type as $key) {
            if(isset($post_type[$key])){
                if(isset($post_type[$key])){
                    $_post_type[$key] = $post_type[$key];
                }
                
            }
            
        }

        $search = '<div class="header-search-select b2-radius" v-cloak v-show="show" data-search=\''.json_encode($_post_type).'\'>';
    
        foreach ($_post_type as $k => $v) {
            $search .= '<a href="javascript:void(0)" :class="type == \''.$k.'\' ? \'select b2-radius\' : \'b2-radius\'" @click="type = \''.$k.'\'">'.$v.'</a>';
        }

        $search .= '</div>';

        return '<div class="top-search mobile-hidden" ref="topsearch" data-search=\''.json_encode($_post_type,true).'\'>
            <form method="get" action="'.B2_HOME_URI.'" class="mobile-search-input b2-radius">
                <div class="top-search-button">
                    <a class="top-search-select" '.(count($opt_type) > 1 ? '@click.stop.prevent="show = !show"' : '').' href="javascript:void(0)"><span v-show="data != \'\'" v-text="data[type]">'.$_post_type[$opt_type[0]].'</span>'.b2_get_icon('b2-arrow-down-s-line').'</a>
                    '.$search.'
                </div>
                <input class="search-input b2-radius" type="text" name="s" autocomplete="off" placeholder="'.__('搜索','b2').'">
                <input type="hidden" name="type" :value="type">
                <button class="search-button-action">'.b2_get_icon('b2-search-line').'</button>
            </form>
        </div>';
    }

    public static function sub_menu($_arg = ''){

        $html = '';

        $audit_mode = is_audit_mode();

        if(b2_is_page_type('newsflashes')){
            $arg = array(
                'theme_location' => 'newsflashes',
                'container_id'=>'top-menu',
                'container_class'=> 'top-menu',
                'menu_id' => 'top-menu-ul',
                'menu_class'=>'top-menu-ul',
                'echo' => FALSE,
                'fallback_cb' => '__return_false' 
            );
            if(b2_get_option('template_top','close_theme_menu_custom')){
                $arg['walker'] = new Menu();
            }

            if($_arg){
                $arg = $_arg;
                $arg['theme_location'] = 'newsflashes';
            }

            $html = wp_nav_menu($arg);
            if($html && !$_arg && !$audit_mode){
                $newsflashes_name = b2_get_option('normal_custom','custom_newsflashes_name');
                $html .= '<div class="top-submit">
                    <button class="empty" onclick="postPoBox.go(\''.get_post_type_archive_link('newsflashes').'?action=showbox\',\'newsflashes\')">'.b2_get_icon('b2-flashlight-line').sprintf(__('发布%s','b2'),$newsflashes_name).'</button>
                </div>';
            }
        }elseif(b2_is_page_type('document')){
            $arg = array(
                'theme_location' => 'document',
                'container_id'=>'top-menu',
                'container_class'=> 'top-menu',
                'menu_id' => 'top-menu-ul',
                'menu_class'=>'top-menu-ul',
                'echo' => FALSE,
                'fallback_cb' => '__return_false' 
            );
            if(b2_get_option('template_top','close_theme_menu_custom')){
                $arg['walker'] = new Menu();
            }

            if($_arg){
                $arg = $_arg;
                $arg['theme_location'] = 'document';
            }

            $html = wp_nav_menu($arg);
            if($html && !$_arg && !$audit_mode){
                $html .= '<div class="top-submit">
                    <button class="empty button" onclick="topMenuLeft.go(\'requests\')">'.b2_get_icon('b2-clipboard-line').__('提交工单','b2').'</button>
                </div>';
            }
            
        }elseif(b2_is_page_type('shop')){
            $arg = array(
                'theme_location' => 'shop',
                'container_id'=>'top-menu',
                'container_class'=> 'top-menu',
                'menu_id' => 'top-menu-ul',
                'menu_class'=>'top-menu-ul',
                'echo' => FALSE,
                'fallback_cb' => '__return_false' 
            );
            if(b2_get_option('template_top','close_theme_menu_custom')){
                $arg['walker'] = new Menu();
            }

            if($_arg){
                $arg = $_arg;
                $arg['theme_location'] = 'shop';
            }

            $html = wp_nav_menu($arg);
            if($html && !$_arg && !$audit_mode){
                $html .= '<div class="top-submit">
                    <button class="empty button" onclick="topMenuLeft.go(\'orders\')">'.b2_get_icon('b2-file-list-2-line').__('我的订单','b2').'</button>
                </div>';
            }
            
        }elseif(b2_is_page_type('circle')){
            $arg = array(
                'theme_location' => 'circle',
                'container_id'=>'top-menu',
                'container_class'=> 'top-menu',
                'menu_id' => 'top-menu-ul',
                'menu_class'=>'top-menu-ul',
                'echo' => FALSE,
                'fallback_cb' => '__return_false' 
            );
            if(b2_get_option('template_top','close_theme_menu_custom')){
                $arg['walker'] = new Menu();
            }

            if($_arg){
                $arg = $_arg;
                $arg['theme_location'] = 'circle';
            }

            $html = wp_nav_menu($arg);
            if($html && !$_arg && !$audit_mode){
                $circle_name = b2_get_option('normal_custom','custom_circle_name');
                $html .= '<div class="top-submit">
                    <a class="empty button" href="'.b2_get_custom_page_url('all-circles').'" target="_blank">'.b2_get_icon('b2-donut-chart-fill').sprintf(__('所有%s','b2'),$circle_name).'</a>
                </div>';
            }
        }elseif(apply_filters('b2_is_page', 'links')){
            $arg = array(
                'theme_location' => 'links',
                'container_id'=>'top-menu',
                'container_class'=> 'top-menu',
                'menu_id' => 'top-menu-ul',
                'menu_class'=>'top-menu-ul',
                'echo' => FALSE,
                'fallback_cb' => '__return_false' 
            );
            if(b2_get_option('template_top','close_theme_menu_custom')){
                $arg['walker'] = new Menu();
            }

            if($_arg){
                $arg = $_arg;
                $arg['theme_location'] = 'links';
            }

            $html = wp_nav_menu($arg);
            if($html && !$_arg && !$audit_mode){
                $html .= '<div class="top-submit">
                    <a class="empty button" href="'.b2_get_custom_page_url('link-register').'" target="_blank">'.b2_get_icon('b2-user-location-line').__('申请入驻','b2').'</a>
                </div>';
            }
        }elseif(apply_filters('b2_is_page', 'infomation')){
            
            $arg = array(
                'theme_location' => 'infomation',
                'container_id'=>'top-menu',
                'container_class'=> 'top-menu',
                'menu_id' => 'top-menu-ul',
                'menu_class'=>'top-menu-ul',
                'echo' => FALSE,
                'fallback_cb' => '__return_false' 
            );
            if(b2_get_option('template_top','close_theme_menu_custom')){
                $arg['walker'] = new Menu();
            }

            if($_arg){
                $arg = $_arg;
                $arg['theme_location'] = 'infomation';
            }

            $html = wp_nav_menu($arg);
            if($html && !$_arg && !$audit_mode && is_singular('infomation')){
                $html .= '<div class="top-submit">
                    <a class="empty button" href="'.b2_get_custom_page_url('po-infomation').'" target="_blank">'.b2_get_icon('b2-user-location-line').__('发布','b2').'</a>
                </div>';
            }
        }elseif(apply_filters('b2_is_page', 'post')){
            $arg = array(
                'theme_location' => 'post',
                'container_id'=>'top-menu',
                'container_class'=> 'top-menu',
                'menu_id' => 'top-menu-ul',
                'menu_class'=>'top-menu-ul',
                'echo' => FALSE,
                'fallback_cb' => '__return_false' 
            );
            if(b2_get_option('template_top','close_theme_menu_custom')){
                $arg['walker'] = new Menu();
            }

            if($_arg){
                $arg = $_arg;
                $arg['theme_location'] = 'post';
            }

            $html = wp_nav_menu($arg);
            if($html && !$_arg && !$audit_mode){
                $html .= '<div class="top-submit">
                    <a class="empty button" href="javascript:void(0)" onclick="postPoBox.go(\''.b2_get_custom_page_url('write').'\',\'write\')">'.b2_get_icon('b2-quill-pen-line').__('写文章','b2').'</a>
                </div>';
            }
        }elseif(!b2_is_page_type('announcement')){
            $arg = array(
                'theme_location' => 'top',
                'container_id'=>'top-menu',
                'container_class'=> 'top-menu',
                'menu_id' => 'top-menu-ul',
                'menu_class'=>'top-menu-ul',
                'echo' => FALSE,
                'fallback_cb' => '__return_false' 
            );
            if(b2_get_option('template_top','close_theme_menu_custom')){
                $arg['walker'] = new Menu();
            }

            if($_arg){
                $arg = $_arg;
                $arg['theme_location'] = 'top';
            }

            $html = wp_nav_menu($arg);
            if($html && !$_arg && !$audit_mode){
                $html .= '<div class="top-submit">
                    <a class="empty button" href="javascript:void(0)" onclick="userTools.goUserPage()">'.b2_get_icon('b2-user-heart-line').__('个人中心','b2').'</a>
                </div>';
            }
        }

        if(!$html) return '';

        if($_arg) return $html;

        $type = b2_get_option('template_top','top_type');

        $html = '<div class="header '.$type.'">
            <div class="top-style-bottom">
                <div class="top-style-blur"></div>
                <div class="wrapper">
                <div id="mobile-menu" class="mobile-box" ref="MobileMenu">
                    <div class="mobile-show">'.self::logo().'</div>
                    '.$html.'
                </div>
                <div class="site-opt" onclick="mobileMenu.showAc()"></div>
                </div>
            </div>
        </div>';


        return $html;
    }

    // public static function mobile_search(){
    //     if(is_home()){
    //         return '
    //             <div class="b2-radius mobile-search mobile-show">
    //                 <form method="get" action="'.home_url().'" class="mobile-search-input">
    //                     <input class="search-input b2-radius" type="text" name="s" autocomplete="off" placeholder="搜索">
    //                     <input type="hidden" name="type" value="post">
    //                 </form>
    //             </div>
    //         ';
    //     }
    //     return '';
    // }

    /**
     * 如果主题使用了缓存，输出缓存的header html 否则直接返回执行结果
     *
     * @return string
     * @author Li Ruchun <lemolee@163.com>
     * @version 1.0.0
     * @since 2018
     */
    public function header_cache(){
      
        // if(B2_OPEN_CACHE){
        //     $html = wp_cache_get('b2_header_html','b2_template');
        //     if($html === false){
        //         $html = self::header();
        //         wp_cache_set('b2_header_html', $html, 'b2_template', WEEK_IN_SECONDS );
        //     }
        // }else{
        //     $html = self::header();
        // }

        // echo $html;
        // unset($html);

        echo self::header();
    }

    /**
     * 顶部菜单
     * self::cart()
     * @return string
     * @author Li Ruchun <lemolee@163.com>
     * @version 1.0.0
     * @since 2018
     */
    public static function header_top(){

        $arg = array(
            'theme_location' => 'ym-menu',
            'container_id'=>'ym-menu',
            'container_class'=> 'ym-menu',
            'echo' => FALSE,
            // 'fallback_cb' => '__return_false',
            // 'walker' => new Menu()
        );

        $menu = wp_nav_menu($arg);

        $html = '
        <div class="header-banner">
            <div class="header-banner-content wrapper">
                <div class="header-banner-left">
                    '.$menu.'
                </div>
                <div class="header-banner-right normal-banner-right">
                    <div class="top-search-icon mobile-show"><a href="javascript:void(0)" onclick="b2SearchBox.show = true">'.b2_get_icon('b2-search-line').'</a></div>
                    <div class="mobile-hidden">
                    '.self::search_form().'
                    </div>
                    '.(!is_audit_mode() ? '<div class="change-theme" v-if="b2token" v-cloak>
                    <div class="mobile-show" >
                        <button @click="b2SearchBox.show = true">'.b2_get_icon('b2-search-line').'</button>
                    </div>
                    <div class="mobile-hidden">
                        <button @click="showBox">'.b2_get_icon('b2-add-circle-line').'</button>
                    </div>
                    <div>
                    <a href="'.b2_get_custom_page_url('message').'" data-title="'.__('消息','b2').'" class="user-tips">'.b2_get_icon('b2-notification-3-line').'<b class="bar-mark" v-if="count > 0" v-cloak></b></a>
                    </div>
                </div>' : '').'
                </div>
            </div>
        </div>';

        return apply_filters('b2_top_header_top',$html, $menu);
    }

    /**
     * 页面顶部菜单
     *
     * @return string 顶部菜单html字符串
     * @author Li Ruchun <lemolee@163.com>
     * @version 1.0.0
     * @since 2018
     */
    public static function menu(){
        $arg = array(
            'theme_location' => 'top',
            'container_id'=>'top-menu',
            'container_class'=> 'top-menu',
            'menu_id' => 'top-menu-ul',
            'menu_class'=>'top-menu-ul',
            'echo' => FALSE,
            'fallback_cb' => '__return_false' 
        );
        if(b2_get_option('template_top','close_theme_menu_custom')){
            $arg['walker'] = new Menu();
        }
        
        $html = '<div id="mobile-menu" class="mobile-box" ref="MobileMenu">
            <div class="header-tools">
                <div class="mobile-show">
                    '.self::logo(false).'
                    <span class="close-button" @click="mobileMenu.showAc()">×</span>
                </div>
            </div>
            '.self::sub_menu($arg).'
        </div><div class="site-opt" onclick="mobileMenu.showAc()"></div>';

        return apply_filters('b2_header_menu',$html,$arg);
    }

    /**
     * 顶部LOGO的HTML代码
     *
     * @return string LOGO的HTML字符串
     * @author Li Ruchun <lemolee@163.com>
     * @version 1.0.0
     * @since 2018
     */
    public static function logo($h1 = true){
        
        if(is_home() || is_front_page()){
            if($h1){
                $logo = '<h1>'.self::logo_link().'</h1>';
            }else{
                $logo = self::logo_link();
            }
        }else{
            $logo = self::logo_link();
        }
        $html = '<div class="header-logo"><div class="logo">'.$logo.'</div></div>';

        return apply_filters('b2_header_logo',$html);
    }

    public static function menu_icon(){
        return '<div id="mobile-menu-button" :class="[\'menu-icon\',{\'active\':show}]" @click="showAc()">
            <div class="line-1"></div>
            <div class="line-2"></div>
            <div class="line-3"></div>
        </div>';
    }

    public static function logo_link(){

        $text_logo = b2_get_option('normal_main','text_logo');
        $img_logo = b2_get_option('normal_main','img_logo');
        $img_logo_white = b2_get_option('normal_main','img_logo_white');

        $html = '<a rel="home" href="'.B2_HOME_URI.'">';

        $body_class = get_body_class();

        if(in_array('post-style-2',$body_class)){
            $html .= $img_logo_white != '' ? '<img itemprop="logo" src="'.$img_logo_white.'" class="light-logo"><img class="block-logo" src="'.$img_logo.'">' : '<p class="site-title">'.$text_logo.'</p>';
        }elseif($img_logo){
            $html .= '<img itemprop="logo" src="'.$img_logo.'">';
        }else{
            $html .= '<p class="site-title">'.$text_logo.'</p>';
        };
        $html .= '</a>';

        return apply_filters('b2_header_logo_link',$html,$text_logo,$img_logo);
    }

    public static function cart(){

        $html = '<div class="user-tools-item"><span>'.b2_get_icon('b2-shopping-cart-2-line').'</span></div>';
        
        return apply_filters('b2_header_user_cart',$html);
    }

    public static function user_info(){

        $arg = apply_filters('b2_user_menu_list',array(
            // 'home'=>array(
            //     'text'=>'<span><b v-text="userData.user_display_name"></b><b v-if="userData.verify" v-html="userData.verify_icon"></b></span>',
            //     'link'=>'<a :href="userData.user_link"',
            //     'new'=>false,
            //     'class'=>'class="top-my-home"',
            //     'attr'=>'',
            //     'des'=>__('个人中心','b2'),
            //     'icon'=>'<img :src="userData.avatar" class="avatar" />'
            // ),
            'directmessage'=>array(
                'text'=>__('私信列表','b2'),
                'link'=>'<a href="'.b2_get_custom_page_url('directmessage').'"',
                'new'=>false,
                'class'=>'',
                'attr'=>'',
                'des'=>__('所有往来私信','b2'),
                'icon'=>b2_get_icon('b2-mail-send-line')
            ),
            'gold'=>array(
                'text'=>__('财富管理','b2'),
                'link'=>'<a href="'.b2_get_custom_page_url('gold').'"',
                'new'=>false,
                'class'=>'',
                'attr'=>'',
                'des'=>sprintf(__('%s、积分管理','b2'),B2_MONEY_NAME),
                'icon'=>b2_get_icon('b2-bit-coin-line')
            ),
            'distribution'=>array(
                'text'=>__('推广中心','b2'),
                'link'=>'<a href="'.b2_get_custom_page_url('distribution').'"',
                'new'=>true,
                'class'=>'',
                'attr'=>'',
                'des'=>__('推广有奖励','b2'),
                'icon'=>b2_get_icon('b2-share-line')
            ),
            'task'=>array(
                'text'=>__('任务中心','b2'),
                'link'=>'<a href="'.b2_get_custom_page_url('task').'"',
                'new'=>true,
                'class'=>'',
                'attr'=>'',
                'des'=>__('每日任务','b2'),
                'icon'=>b2_get_icon('b2-task-line')
            ),
            'vip'=>array(
                'text'=>__('成为会员','b2'),
                'link'=>'<a href="'.b2_get_custom_page_url('vips').'"',
                'new'=>false,
                'class'=>'',
                'attr'=>'',
                'des'=>__('购买付费会员','b2'),
                'icon'=>b2_get_icon('b2-vip-crown-2-line')
            ),
            'certification'=>array(
                'text'=>__('认证服务','b2'),
                'link'=>'<a href="'.b2_get_custom_page_url('verify').'"',
                'new'=>true,
                'class'=>'',
                'attr'=>'',
                'des'=>__('申请认证','b2'),
                'icon'=>b2_get_icon('b2-shield-user-line')
            ),
            'dark_room'=>array(
                'text'=>__('小黑屋','b2'),
                'link'=>'<a href="'.b2_get_custom_page_url('dark-room').'"',
                'new'=>true,
                'class'=>'',
                'attr'=>'',
                'des'=>__('关进小黑屋的人','b2'),
                'icon'=>b2_get_icon('b2-skull-2-line')
            ),
            'orders'=>array(
                'text'=>__('我的订单','b2'),
                'link'=>'<a :href="userData.link+\'/orders\'"',
                'new'=>false,
                'class'=>'',
                'attr'=>'',
                'des'=>__('查看我的订单','b2'),
                'icon'=>b2_get_icon('b2-file-list-2-line')
            ),
            'settings'=>array(
                'text'=>__('我的设置','b2'),
                'link'=>'<a :href="userData.link+\'/settings\'"',
                'new'=>false,
                'class'=>'',
                'attr'=>'',
                'des'=>__('编辑个人资料','b2'),
                'icon'=>b2_get_icon('b2-user-settings-line')
            ),
            'admin_panel'=>array(
                'text'=>__('进入后台管理','b2'),
                'link'=>'<a href="'.get_admin_url().'"',
                'new'=>false,
                'class'=>'class="admin-panel" v-if="userData.is_admin"',
                'attr'=>'',
                'des'=>'',
                'icon'=>b2_get_icon('b2-settings-3-line')
            )
        ));

        $allow = b2_get_option('verify_main','verify_allow');
        if(!$allow){
            unset($arg['certification']);
        };

        $distribution = b2_get_option('distribution_main','distribution_open');
        if((int)$distribution === 0){
            unset($arg['distribution']);
        }

        // $open_shop = b2_get_option('shop_main','shop_open');
        // if((int)$open_shop === 0){
        //     unset($arg['orders']);
        // }

        $allow = b2_get_option('template_top','user_menus');

        if($allow){
            $new_arg = array();
            
            foreach ($allow as $v) {
                if(isset($arg[$v])){
                    $new_arg[$v] = $arg[$v];
                }
            }

            $new_arg['admin_panel'] = $arg['admin_panel'];

        }else{
            $new_arg = $arg;
        }

        $link = '';

        foreach($new_arg as $v){
            $link .= '<li>'.$v['link'].' '.$v['attr'].' '.$v['class'].'>'.$v['icon'].'<p>'.$v['text'].'<span class="top-user-link-des">'.$v['des'].'</span></p>'.($v['new'] ? '<i class="menu-new">NEW</i>' : '').'</a></li>';
        }

        $html = '<div class="top-user-box" v-if="b2token" v-cloak>
            <div class="top-user-avatar avatar-parent" @click.stop="showDropMenu">
                <img :src="userData.avatar" class="avatar b2-radius"/>
                <span v-if="userData.user_title">'.b2_get_icon('b2-vrenzhengguanli').'</span>
            </div>
            <div :class="[\'top-user-box-drop jt b2-radius\',{\'show\':showDrop}]" v-cloak>
                <div class="top-user-info-box" v-if="role.user_data">
                    <div class="top-user-info-box-name">
                        <img :src="userData.avatar" class="avatar b2-radius"/>
                        <div class="top-user-name">
                            <h2>{{userData.name}}<span v-if="userData.user_title">'.__('已认证','b2').'</span></h2>
                            <div>
                                <div v-html="role.user_data.lv.lv.icon"></div>
                                <div v-html="role.user_data.lv.vip.icon"></div>
                            </div>
                        </div>
                        <a :href="userData.link" class="link-block" target="_blank"></a>
                        <div class="login-out user-tips" data-title="'.__('退出登录','b2').'"><a href="javascript:void(0)" @click="out">'.b2_get_icon('b2-login-circle-line').'</a></div>
                    </div>
                    <div class="top-user-info-box-count" v-if="role.user_data">
                        <p>
                            <span>'.__('文章','b2').'</span>
                            <b v-text="role.user_data.post_count"></b>
                        </p>
                        <p>
                            <span>'.__('评论','b2').'</span>
                            <b v-text="role.user_data.comment_count"></b>
                        </p>
                        <p>
                            <span>'.__('关注','b2').'</span>
                            <b v-text="role.user_data.following"></b>
                        </p>
                        <p>
                            <span>'.__('粉丝','b2').'</span>
                            <b v-text="role.user_data.followers"></b>
                        </p>
                        <a :href="userData.link" class="link-block" target="_blank"></a>
                    </div>
                    <div class="user-w-gold">
                        <div class="user-money user-tips" data-title="'.B2_MONEY_NAME.'"><a href="'.b2_get_custom_page_url('gold').'" target="_blank"><i>'.B2_MONEY_SYMBOL.'</i>{{role.user_data.money}}</a></div> 
                        <div class="user-credit user-tips" data-title="'.__('积分','b2').'"><a href="'.b2_get_custom_page_url('gold').'" target="_blank">'.b2_get_icon('b2-coin-line').'{{role.user_data.credit}}</a></div>
                    </div>
                    <div class="user-w-rw b2-radius">
                        <div class="user-w-rw-bg" :style="\'width:\'+role.user_data.task+\'%\'"></div>
                        <a class="link-block" href="'.b2_get_custom_page_url('task').'" target="_blank"><span>'.sprintf(__('您已完成今天任务的%s','b2'),'<b v-text="role.user_data.task+\'%\'"></b>').'</span></a>
                    </div>
                </div>
                <ul>
                    '.$link.'
                </ul>
            </div>
        </div>';

        return apply_filters('b2_header_user_info',$html);
    }

    public static function user_msg(){
        $html = '
            <a class="user-tools-item msg-new dmsg-icon" href="'.b2_get_custom_page_url('message').'">
                <span :class="msg.count ? \'opt-1\' : \'\'">'.b2_get_icon('b2-news').'<b v-show="msg.count"></b></span>
            </a>
        ';

        return apply_filters('b2_header_user_msg',$html);
    }

    public static function user_dmsg(){
        $html = '<div class="user-tools-item dmsg-icon" @click.stop="showDmsgBox()" onselectstart="return false;">
            <span :class="dmsg.count ? \'opt-1 user-tips\' : \'user-tips\'" data-title="'.__('消息','b2').'">'.b2_get_icon('b2-notification-3-line').'<b v-show="dmsg.count"></b></span>
            <div class="new-dmsg-list b2-radius jt" v-show="dmsg.show" v-cloak @click.stop="">
                <div class="new-dmsg-title" v-if="dmsg.count">'.__('您有','b2').'{{dmsg.count}}'.__('条新的私信','b2').'</div>
                <div class="new-dmsg-title" v-else>'.__('您没有新的私信','b2').'</div>
                <ul v-if="dmsg.data.length > 0">
                    <li v-for="item in dmsg.data" @click="jumpTo(\''.b2_get_custom_page_url('directmessage').'/to/\'+item.from.id)">
                        <img class="avatar" :src="item.from.avatar">
                        <div class="new-dmsg-content">
                            <h2 v-text="item.from.name"></h2>
                            <div class="" v-html="item.content"></div>
                        </div>
                    </li>
                </ul>
                <div class="new-dmsg-footer">
                    <button class="text" @click="b2Dmsg.show = true;b2Dmsg.select = \'select\'">'.b2_get_icon('b2-mail-send-line').__('写新私信','b2').'</button>
                    <a href="'.b2_get_custom_page_url('directmessage').'">'.__('查看全部私信','b2').'</a>
                </div>
            </div>
        </div>';

        return apply_filters('b2_header_user_dmsg',$html);
    }

    /**
     * 顶部用户栏
     *
     * @return string 登录注册，消息，搜索等按钮的html
     * @author Li Ruchun <lemolee@163.com>
     * @version 1.0.0
     * @since 2018
     */
    public static function user(){

        if(is_audit_mode()) return '';

        $html = '<div class="header-user">
        <div class="change-theme" v-cloak>
            <div class="mobile-show">
                <button @click="b2SearchBox.show = true">'.b2_get_icon('b2-search-line').'</button>
            </div>
            <div class="mobile-hidden user-tips" v-show="login" v-cloak data-title="'.__('发起','b2').'">
                <button @click="showBox">'.b2_get_icon('b2-add-circle-line').'</button>
            </div>
            <div v-show="login" v-cloak>
                <a href="'.b2_get_custom_page_url('message').'" data-title="'.__('消息','b2').'" class="user-tips">'.b2_get_icon('b2-notification-3-line').'<b class="bar-mark" v-if="count > 0" v-cloak></b></a>
            </div>
        </div>
        <div class="top-user-info">';

        //搜索、购物车、通知按钮
        $html .= '<div class="user-tools" v-if="b2token">
            '.self::user_info().'
        </div>';

        $html .= '<div class="login-button" v-if="!b2token" v-cloak>';

        $html .= '<div class="header-login-button" v-cloak>
        <button class="empty mobile-hidden" @click="login(1)">'.__('登录','b2').'</button>
        '.(b2_get_option('normal_login','allow_register') ? '<button class="mobile-hidden" @click="login(2)">'.__('快速注册','b2').'</button>' : '').'
        </div>
        <div class="button text empty mobile-show" @click="login(1)">'.b2_get_icon('b2-account-circle-line').'</div>';

        $html .= '</div>';

        $html .= '</div></div>';

        return apply_filters('b2_header_user',$html);
    }
}