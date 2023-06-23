<?php
use B2\Modules\Common\User;
add_action('cmb2_admin_init',function(){
    $options = new_cmb2_box(array(
        'id'           => 'b2_refresh_control',
        'object_types' => array( 'options-page' ),
        'option_key'   => 'b2_refresh_control',
        'tab_group'    => 'b2_refresh_options',
        'parent_slug'  => 'b2_tz_main_control',
        'menu_title'   => __('刷新数据','b2'),
        'display_cb'   => function(){
            if(isset($_POST['action']) && sanitize_text_field($_POST['action'])=='refresh'){
            	$users = get_users(array('number'=>-1));
				foreach($users as $user){
				    $user_id = $user->ID;
				    tj_check_user_vip_time($user_id);
				    User::get_user_lv($user_id);
				}
				echo '<div class="" style="padding:11px 15px;margin: 5px 15px 2px 2px;box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);background:#fff;border-left:4px solid green">刷新完成</div>';
			}
            ?>
            <div class="wrap">
                <h1>刷新数据</h1>
                <form method="post">
                    <input type="hidden" name="action" value="refresh">
                    <input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce( 'check-nonce' );?>">    
                    <p class="submit">
                        <input type="submit" name="submit" id="submit" class="button button-primary" value="刷新">
                    </p>
                </form>
            <h2><?php echo __('说明','b2'); ?></h2>
            <p>为了解决主题某些状态刷新需要用户操作触发，可能导致统计不准等问题，增加手动刷新功能，站长可以在本页手动刷新</p>
            <p>点击刷新按钮之后，耐心等待，用户数量越多，等待时间越长</p>
            <h2>刷新内容：</h2>
            <ul>
            	<li>①用户VIP状态</li>
            	<li>②用户LV状态</li>
            </ul>
            </div>
        <?php
        }
    ));
},99);


function tj_check_user_vip_time($user_id){

    $user_lv_time = get_user_meta($user_id,'zrz_vip_time',true);

    //获取结束时间
    $vip_data = b2_get_option('normal_user','user_vip_group');

    $vip = get_user_meta($user_id,'zrz_vip',true);
    if(!$vip){
    	return true;
    }
    $vip = (string)preg_replace('/\D/s','',$vip);
    
    $day = $vip_data[$vip];
    $day = $day['time'];

    //如果是永久会员
    if(isset($user_lv_time['end']) && (string)$user_lv_time['end'] === '0' && (int)$day === 0){
        return false;
    }

    //如果不是永久会员
    if(isset($user_lv_time['end']) && (string)$user_lv_time['end'] !== '0' && (int)$day !== 0){
        //检查是否过期
        if($user_lv_time['end'] < strtotime(date('Y-m-d H:i:s'))){
            delete_user_meta($user_id,'zrz_vip');
            delete_user_meta($user_id,'zrz_vip_time');
            return true;
        }

        return false;
    }

    $current = strtotime(date('Y-m-d H:i:s'));
    $end = strtotime(date('Y-m-d H:i:s',strtotime('+'.$day.' day')));

    //如果是永久会员，并且管理员重新设置了非永久会员
    if(isset($user_lv_time['end']) && (string)$user_lv_time['end'] === '0' && (int)$day !== 0){
        update_user_meta($user_id,'zrz_vip_time',array(
            'start'=>$current,
            'end'=>$end
        ));

        return false;
    }

    //如果不是永久会员，管理员设置了永久会员
    if(isset($user_lv_time['end']) && (string)$user_lv_time['end'] !== '0' && (int)$day === 0){
        update_user_meta($user_id,'zrz_vip_time',array(
            'start'=>$current,
            'end'=>'0'
        ));

        return false;
    }

    //如果没有设置会员，重新计算时间
    if(!isset($user_lv_time['end'])){
        update_user_meta($user_id,'zrz_vip_time',array(
            'start'=>$current,
            'end'=>$end
        ));
        return false;
    }

    return true;
}