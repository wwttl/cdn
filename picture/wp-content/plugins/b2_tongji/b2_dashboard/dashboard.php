<?php

add_action('wp_dashboard_setup', function(){
if(is_user_logged_in() && current_user_can('manage_options')){
    wp_add_dashboard_widget('b2_tj_widgget', '网站数据统计', function () {
        $TZ_control = new TZ_control();
        $gongdan_link = home_url('/wp-admin/admin.php?page=b2_request_list');
        $pen_post = wp_count_posts('post')->pending;
        $pen_post_link = home_url('/wp-admin/edit.php');
        $pen_circle = wp_count_posts('circle')->pending;
        $pen_circle_link = home_url('/wp-admin/edit.php?post_type=circle');
        $pen_fn = wp_count_posts('newsflashes')->pending;
        $pen_fn_link = home_url('/wp-admin/edit.php?post_type=newsflashes');
        $pen_com = wp_count_comments()->moderated;
        $pen_com_link = home_url('/wp-admin/edit-comments.php');
        $more = home_url('/wp-admin/admin.php?page=b2_tz_main_control');
        $jinrishouru = $TZ_control->tz_get_today_money();
        $jinrilink = home_url('/wp-admin/admin.php?page=b2_orders_list');
        $this_month = $TZ_control->tz_get_month_money();
        $last_month = $TZ_control->tz_get_last_money();
        $renzheng_shenhe = $TZ_control->get_renzhengshenhe_num();
        $renzheng_shenhe_link = home_url('/wp-admin/admin.php?page=b2_verify_list&status1=4');
        $shangchengdaifahuo_link = home_url('/wp-admin/admin.php?page=b2_orders_list&order_type=gx');
        $shangchengdaifahuo = $TZ_control->tz_get_c_order();
        echo '
        <style>
        .activity-block li{
            padding: 5px;
            border-radius: 3px;
        }
        .activity-block li.have{
            background: red;
            color: white;
        }
        .activity-block li.have *{
            color: white!important;
        }
        </style>
        <div id="published-posts" class="activity-block">
            <h3>收入统计</h3>
            <ul>
                <li>
                    <span>上月收入</span>
                    <a href="#">
                        '.$last_month.'
                    </a>
                </li>
                <li>
                    <span>本月收入</span>
                    <a href="#">
                        '.$this_month.'
                    </a>
                </li>
                <li>
                    <span>今日收入</span>
                    <a href="'.$jinrilink.'">
                        '.$jinrishouru[0].'
                    </a>
                </li>
            </ul>
        </div>
        <div id="published-posts" class="activity-block">
            <h3>待处理事项</h3>
            <ul>
                <li class="'.($TZ_control->get_status_count('unreplied')>0 ? 'have' : '').'">
                    <a href="'.$gongdan_link.'"><span>未处理工单</span>
                        '.$TZ_control->get_status_count('unreplied').'</a>
                </li>
                <li class="'.($pen_post>0 ? 'have' : '').'">
                    <a href="'.$pen_post_link.'"><span>待审文章</span>
                        '.$pen_post.'</a>
                </li>
                <li class="'.($pen_circle>0 ? 'have' : '').'"><a href="'.$pen_circle_link.'">
                    <span>待审圈子</span>
                        '.$pen_circle.'
                </a></li>
                <li class="'.($pen_fn>0 ? 'have' : '').'">
                    <a href="'.$pen_fn_link.'"><span>待审快讯</span>
                        '.$pen_fn.'</a>
                </li>
                <li class="'.($pen_com>0 ? 'have' : '').'">
                    <a href="'.$pen_com_link.'"><span>待审评论</span>
                        '.$pen_com.'</a>
                </li>
                <li class="'.($renzheng_shenhe>0 ? 'have' : '').'">
                   <a href="'.$renzheng_shenhe_link.'"> <span>待审认证</span>
                        '.$renzheng_shenhe.'</a>
                </li>
                <li class="'.($shangchengdaifahuo>0 ? 'have' : '').'">
                    <a href="'.$shangchengdaifahuo_link.'"><span>商城待发货</span>
                        '.$shangchengdaifahuo.'</a>
                </li>
            </ul>
        </div>
        <div id="published-posts" class="activity-block">
            <h3>
                <a href="'.$more.'">查看详细统计</a>
            </h3>
        </div>
';

    });
}

});


