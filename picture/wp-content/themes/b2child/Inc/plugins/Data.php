<?php
/**
 * 
 * description: 原始数据获取，你想要的我都给你!
 * 
**/
function B2Danmu_allinfo_data($data){
    global $wpdb;
    $table_name = $wpdb->prefix . 'zrz_order';
    $all_select = $wpdb->get_results( "SELECT * FROM $table_name WHERE order_state='q' AND order_type<>'mission'  ORDER BY id DESC LIMIT 20",ARRAY_A);
    $data = array();
    foreach($all_select as $key=>$v) {
    	//获得user_id的名字
    	$user_id_name = get_userdata($all_select[$key]['user_id']);
    	$user_id_name_true = isset($user_id_name->display_name) ? esc_attr($user_id_name->display_name) : '';
    	$user_id_link=get_author_posts_url($all_select[$key]['user_id']);
    	//获得msg_users的名字
    	$msg_users_name = $all_select[$key]['msg_users'];
    	//$msg_users_name_tian= trim($msg_users_name,"[]");
    	$msg_users_name_jun=preg_replace( '/[\W]/', '', $msg_users_name);
    	$msg_users_name_ya = get_userdata($msg_users_name_jun);
    	//获取用户名		    
    	$msg_users_name_true = isset($msg_users_name_ya->display_name) ? esc_attr($msg_users_name_ya->display_name) : '';
    	$msg_users_link=get_author_posts_url($msg_users_name_jun);
    	if($all_select[$key]['order_type']=='vip') {
    		$avatar = get_avatar_url($all_select[$key]['user_id'],array('size'=>160));
    		$infeo_link=$user_id_link;
    		$msg_credit=$all_select[$key]['order_price'];
    		$answer_user_page='<a href="/vips" target="_blank" class="info_zianv2">VIP会员</a>';
    		$answer='豪爽地拿出'.$msg_credit.'元RMB买下<i></i>'.$answer_user_page.'<i></i>大佬威武';
    	} 
    	elseif($all_select[$key]['order_type']=='cg') {
    		$avatar = get_avatar_url($all_select[$key]['user_id'],array('size'=>160));
    		$infeo_link=$user_id_link;
    		$answer='花了'.$all_select[$key]['order_price'].'元RMB购买积分';
    		
    	}
    	elseif($all_select[$key]['order_type']=='ds') {
    		$avatar = get_avatar_url($all_select[$key]['user_id'],array('size'=>160));
    		$infeo_link=$user_id_link;
    		$msg_credit=$all_select[$key]['order_price'];
    			$answer_user_page='<a href="'.get_permalink($all_select[$key]['post_id']).'" target="_blank" class="info_zianv">'.get_post($all_select[$key]['post_id'])->post_title.'</a>';
    		$answer='打赏<i></i>'.$answer_user_page.'<i></i>的作者'.$msg_credit.'元RMB';
    	}   elseif($all_select[$key]['order_type']=='x'&& $all_select[$key]['pay_type']=='credit' ) {
    		$avatar = get_avatar_url($all_select[$key]['user_id'],array('size'=>160));
    		$infeo_link=$user_id_link;
    		$msg_credit=$all_select[$key]['order_price'];
    	    		$answer_user_page='<a href="'.get_permalink($all_select[$key]['post_id']).'" target="_blank" class="info_zianv">'.get_post($all_select[$key]['post_id'])->post_title.'</a>';
    		$answer='花了'.$msg_credit.'积分购买<i></i>'.$answer_user_page.'<i></i>';
    	}
    	 elseif($all_select[$key]['order_type']=='x'&& $all_select[$key]['pay_type']<>'credit' ) {
    		$avatar = get_avatar_url($all_select[$key]['user_id'],array('size'=>160));
    		$infeo_link=$user_id_link;
    		$msg_credit=$all_select[$key]['order_price'];
      		$answer_user_page='<a href="'.get_permalink($all_select[$key]['post_id']).'" target="_blank" class="info_zianv">'.get_post($all_select[$key]['post_id'])->post_title.'</a>';
    		$answer='花了'.$msg_credit.'元RMB购买<i></i>'.$answer_user_page.'<i></i>';
    	}
    	elseif($all_select[$key]['order_type']=='w'&& $all_select[$key]['pay_type']=='credit' ) {
    		$avatar = get_avatar_url($all_select[$key]['user_id'],array('size'=>160));
    		$infeo_link=$user_id_link;
    		$msg_credit=$all_select[$key]['order_price'];
    	    		$answer_user_page='<a href="'.get_permalink($all_select[$key]['post_id']).'" target="_blank" class="info_zianv">'.get_post($all_select[$key]['post_id'])->post_title.'</a>';
    		$answer='花了'.$msg_credit.'积分购买隐藏资源<i></i>'.$answer_user_page.'<i></i>';
    	}
    	 elseif($all_select[$key]['order_type']=='w'&& $all_select[$key]['pay_type']<>'credit' ) {
    		$avatar = get_avatar_url($all_select[$key]['user_id'],array('size'=>160));
    		$infeo_link=$user_id_link;
    		$msg_credit=$all_select[$key]['order_price'];
      		$answer_user_page='<a href="'.get_permalink($all_select[$key]['post_id']).'" target="_blank" class="info_zianv">'.get_post($all_select[$key]['post_id'])->post_title.'</a>';
    		$answer='花了'.$msg_credit.'元RMB购买隐藏资源<i></i>'.$answer_user_page.'<i></i>';
    	}
    	elseif($all_select[$key]['order_type']=='v'&& $all_select[$key]['pay_type']=='credit' ) {
    		$avatar = get_avatar_url($all_select[$key]['user_id'],array('size'=>160));
    		$infeo_link=$user_id_link;
    		$msg_credit=$all_select[$key]['order_price'];
    	    		$answer_user_page='<a href="'.get_permalink($all_select[$key]['post_id']).'" target="_blank" class="info_zianv">'.get_post($all_select[$key]['post_id'])->post_title.'</a>';
    		$answer='花了'.$msg_credit.'积分购买视频<i></i>'.$answer_user_page.'<i></i>';
    	}
    	 elseif($all_select[$key]['order_type']=='v'&& $all_select[$key]['pay_type']<>'credit' ) {
    		$avatar = get_avatar_url($all_select[$key]['user_id'],array('size'=>160));
    		$infeo_link=$user_id_link;
    		$msg_credit=$all_select[$key]['order_price'];
      		$answer_user_page='<a href="'.get_permalink($all_select[$key]['post_id']).'" target="_blank" class="info_zianv">'.get_post($all_select[$key]['post_id'])->post_title.'</a>';
    		$answer='花了'.$msg_credit.'元RMB购买视频<i></i>'.$answer_user_page.'<i></i>';
    	}
    	elseif($all_select[$key]['order_type']=='gx') {
    		$avatar = get_avatar_url($all_select[$key]['user_id'],array('size'=>160));
    		$infeo_link=$user_id_link;
    		$msg_credit=$all_select[$key]['order_price'];
    		$answer_user_page='<a href="'.get_permalink($all_select[$key]['post_id']).'" target="_blank" class="info_zianv">'.get_post($all_select[$key]['post_id'])->post_title.'</a>';
    		$answer='花了'.$msg_credit.'元RMB购买商城产品<i></i>'.$answer_user_page.'<i></i>';
    	}
		elseif($all_select[$key]['order_type']=='d') {
    		$avatar = get_avatar_url($all_select[$key]['user_id'],array('size'=>160));
    		$infeo_link=$user_id_link;
    		$msg_credit=$all_select[$key]['order_price'];
    		$answer_user_page='<a href="'.get_permalink($all_select[$key]['post_id']).'" target="_blank" class="info_zianv">'.get_post($all_select[$key]['post_id'])->post_title.'</a>';
    		$answer='花了'.$msg_credit.'积分兑换商城产品<i></i>'.$answer_user_page.'<i></i>';
    	}
		elseif($all_select[$key]['order_type']=='c') {
    		$avatar = get_avatar_url($all_select[$key]['user_id'],array('size'=>160));
    		$infeo_link=$user_id_link;
    		$msg_credit=$all_select[$key]['order_price'];
    		$answer_user_page='<a href="'.get_permalink($all_select[$key]['post_id']).'" target="_blank" class="info_zianv">'.get_post($all_select[$key]['post_id'])->post_title.'</a>';
    		$answer='花了'.$msg_credit.'积分抽奖商城产品<i></i>'.$answer_user_page.'<i></i>';
    	}
    	 elseif($all_select[$key]['order_type']=='cz') {
    		$avatar = get_avatar_url($all_select[$key]['user_id'],array('size'=>160));
    		$infeo_link=$user_id_link;
    		$answer='充值<i></i>'.$all_select[$key]['order_price'].'<i></i>元RMB 感谢您的信任';
    	}elseif($all_select[$key]['order_type']=='verify') {
    		$avatar = get_avatar_url($all_select[$key]['user_id'],array('size'=>160));
    		$infeo_link=$user_id_link;
    		$msg_credit=$all_select[$key]['order_price'];
    		$answer='花了'.$msg_credit.'元RMB<i></i><a href="/verify" target="_blank" class="info_zianv2">进行了高级认证</a>';
    	}  else {
    		$infeo_link=$user_id_link;
    		$answer='欢迎大佬们光临';
    	}
    	$answer_list = array(
    		'user_id'=>$all_select[$key]['user_id'],
    		'umsg_id'=>$msg_users_name_jun,
    		'user_id_name'=>isset($all_select[$key]['user_id']->display_name) ? esc_attr($all_select[$key]['user_id']->display_name) : '',
    		'avatar'=>$avatar,
    		'link'=>$infeo_link,
    	   'answer'=>$answer,
    	 );
    	$data[] = $answer_list;
    }
    return $data;
}
