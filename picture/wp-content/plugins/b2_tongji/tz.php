<?php
use B2\Modules\Common\User;
class TZ_control{
	private $wpdb;
	private $time;

	public function __construct(){
		global $wpdb;
        $this->wpdb = $wpdb;
        $this->time = $this -> tz_get_time();
	}
	public function randrgb(){ 
	  $color = array("rgb(255, 99, 132)", "rgb(255, 159, 64)", "rgb(255, 205, 86)", "rgb(75, 192, 192)", "rgb(54, 162, 235)");
	  return $color[array_rand($color,1)];
	}
	public function get_this_month(){
		date_default_timezone_set("Asia/Shanghai");
	    $y = date("Y", time()); //年 
	    $m = date("m", time()); //月 
	    $d = date("d", time()); //日 
	    $t0 = date('t'); // 本月一共有几天 
	    $r=array();
	    $r['start_month'] = date("Y-m-d H:i:s",mktime(0, 0, 0, $m, 1, $y)); // 创建本月开始时间 
	    $r['end_month'] = date("Y-m-d H:i:s",mktime(23, 59, 59, $m, $t0, $y)); // 创建本月结束时间
	    return $r;
	}
	public function get_last_month(){
	    date_default_timezone_set("Asia/Shanghai");
		$thismonth = date('m');
		$thisyear = date('Y');
		if ($thismonth == 1) {
			$lastmonth = 12;
			$lastyear = $thisyear - 1;
		} else {
			$lastmonth = $thismonth - 1;
			$lastyear = $thisyear;
		}
		$lastStartDay = $lastyear . '-' . $lastmonth . '-1';
		$lastEndDay = $lastyear . '-' . $lastmonth . '-' . date('t', strtotime($lastStartDay));
		$b_time = strtotime($lastStartDay);//上个月的月初时间戳
		$e_time = strtotime($lastEndDay)+86399;//上个月的月末时间戳
		$r=array();
	    $r['start_month'] = date("Y-m-d H:i:s",$b_time);
	    $r['end_month'] = date("Y-m-d H:i:s",$e_time);
	    return $r;
	}

	public function tz_get_time(){
	    date_default_timezone_set("Asia/Shanghai");
		$a = strtotime(date("Y-m-d H:i:s"));//当前时间戳
		$todaytime = strtotime("today");//今日起始时间戳

		return array(
			'a'=>array(
				date("Y-m-d H:i:s",$todaytime),
				date("Y-m-d H:i:s",$todaytime-24*60*60*1),
				date("Y-m-d H:i:s",$todaytime-24*60*60*2),
				date("Y-m-d H:i:s",$todaytime-24*60*60*3),
				date("Y-m-d H:i:s",$todaytime-24*60*60*4),
				date("Y-m-d H:i:s",$todaytime-24*60*60*5),
				date("Y-m-d H:i:s",$todaytime-24*60*60*6)
			),
			'b'=>array(
				date("Y-m-d H:i:s",$todaytime-8*60*60),
				date("Y-m-d H:i:s",$todaytime-24*60*60*1-8*60*60),
				date("Y-m-d H:i:s",$todaytime-24*60*60*2-8*60*60),
				date("Y-m-d H:i:s",$todaytime-24*60*60*3-8*60*60),
				date("Y-m-d H:i:s",$todaytime-24*60*60*4-8*60*60),
				date("Y-m-d H:i:s",$todaytime-24*60*60*5-8*60*60),
				date("Y-m-d H:i:s",$todaytime-24*60*60*6-8*60*60)
			)
		);
	}
	//返回近七天注册用户数量
	public function tz_get_user_num(){ 
		$time = $this -> time;
		$time = $time['b'];
		
		$table_name = $this->wpdb->prefix . 'users';
		$a = $this->wpdb->get_var("SELECT COUNT(ID) FROM $table_name where user_registered > '$time[0]'");
		$b = $this->wpdb->get_var("SELECT COUNT(ID) FROM $table_name where user_registered > '$time[1]' and user_registered < '$time[0]'");
		$c = $this->wpdb->get_var("SELECT COUNT(ID) FROM $table_name where user_registered > '$time[2]' and user_registered < '$time[1]'");
		$d = $this->wpdb->get_var("SELECT COUNT(ID) FROM $table_name where user_registered > '$time[3]' and user_registered < '$time[2]'");
		$e = $this->wpdb->get_var("SELECT COUNT(ID) FROM $table_name where user_registered > '$time[4]' and user_registered < '$time[3]'");
		$f = $this->wpdb->get_var("SELECT COUNT(ID) FROM $table_name where user_registered > '$time[5]' and user_registered < '$time[4]'");
		$g = $this->wpdb->get_var("SELECT COUNT(ID) FROM $table_name where user_registered > '$time[6]' and user_registered < '$time[5]'");
		$h = $this->wpdb->get_var("SELECT COUNT(ID) FROM $table_name");//用户总数
		return array($a,$b,$c,$d,$e,$f,$g,$h);
	}
	//返回近七天用户签到数量
	public function tz_get_user_sign_num(){

	    
		$time = $this->time;
		$time = $time['a'];
		$table_name = $this->wpdb->prefix . 'zrz_message';
		
		//临时兼容
        $args = array(
            'number' => -1,
            'meta_key' => 'b2_mission_today',
            'meta_value' => $time[0],
            'meta_compare' => '>=',
        );
        $user_query = new \WP_User_Query($args);
        $a = $user_query->get_total();
        //var_dump($time[0]);
		
		//$a = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where msg_type=16 and msg_date > '$time[0]'");
		$b = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where msg_type=16 and msg_date > '$time[1]' and msg_date < '$time[0]'");
		$c = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where msg_type=16 and msg_date > '$time[2]' and msg_date < '$time[1]'");
		$d = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where msg_type=16 and msg_date > '$time[3]' and msg_date < '$time[2]'");
		$e = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where msg_type=16 and msg_date > '$time[4]' and msg_date < '$time[3]'");
		$f = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where msg_type=16 and msg_date > '$time[5]' and msg_date < '$time[4]'");
		$g = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where msg_type=16 and msg_date > '$time[6]' and msg_date < '$time[5]'");
		return array($a,$b,$c,$d,$e,$f,$g);
	}
	//评论数量
	public function tz_get_user_comment_num(){
		$time = $this->time;
		$time = $time['a'];
		$table_name = $this->wpdb->prefix . 'comments';
		$a = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where comment_date > '$time[0]'");
		$b = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where comment_date > '$time[1]' and comment_date < '$time[0]'");
		$c = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where comment_date > '$time[2]' and comment_date < '$time[1]'");
		$d = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where comment_date > '$time[3]' and comment_date < '$time[2]'");
		$e = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where comment_date > '$time[4]' and comment_date < '$time[3]'");
		$f = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where comment_date > '$time[5]' and comment_date < '$time[4]'");
		$g = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where comment_date > '$time[6]' and comment_date < '$time[5]'");
		return array($a,$b,$c,$d,$e,$f,$g);
	}
	//文章数量
	public function tz_get_user_post_num(){
		$time = $this->time;
		$time = $time['a'];
		$table_name = $this->wpdb->prefix . 'posts';
		$a = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where post_type='post' and post_status='publish' and post_date > '$time[0]'");
		$b = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where post_type='post' and post_status='publish' and post_date > '$time[1]' and post_date < '$time[0]'");
		$c = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where post_type='post' and post_status='publish' and post_date > '$time[2]' and post_date < '$time[1]'");
		$d = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where post_type='post' and post_status='publish' and post_date > '$time[3]' and post_date < '$time[2]'");
		$e = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where post_type='post' and post_status='publish' and post_date > '$time[4]' and post_date < '$time[3]'");
		$f = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where post_type='post' and post_status='publish' and post_date > '$time[5]' and post_date < '$time[4]'");
		$g = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where post_type='post' and post_status='publish' and post_date > '$time[6]' and post_date < '$time[5]'");
		return array($a,$b,$c,$d,$e,$f,$g);
	}
	//圈子数量
	public function tz_get_user_circle_num(){
		$time = $this->time;
		$time = $time['a'];
		$table_name = $this->wpdb->prefix . 'posts';
		$a = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where post_type='circle' and post_status='publish' and post_date > '$time[0]'");
		$b = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where post_type='circle' and post_status='publish' and post_date > '$time[1]' and post_date < '$time[0]'");
		$c = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where post_type='circle' and post_status='publish' and post_date > '$time[2]' and post_date < '$time[1]'");
		$d = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where post_type='circle' and post_status='publish' and post_date > '$time[3]' and post_date < '$time[2]'");
		$e = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where post_type='circle' and post_status='publish' and post_date > '$time[4]' and post_date < '$time[3]'");
		$f = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where post_type='circle' and post_status='publish' and post_date > '$time[5]' and post_date < '$time[4]'");
		$g = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where post_type='circle' and post_status='publish' and post_date > '$time[6]' and post_date < '$time[5]'");
		return array($a,$b,$c,$d,$e,$f,$g);
	}
	//用户各类等级分布
	public function tz_get_user_lv(){
		$roles = User::get_user_roles();
		$array = array();
		$array['vip']['normal'] = array();
		$table_name = $this->wpdb->prefix . 'usermeta';
		$table_name_a = $this->wpdb->prefix . 'users';
		$users_all = $this->wpdb->get_var("select count(id) from $table_name_a");
		foreach ($roles as $key => $value) {
			if(strpos($key,'vip') !== false){
				$num = $this->wpdb->get_results( "select COUNT(*) from $table_name_a a join $table_name b on a.ID = b.user_id and b.meta_key = 'zrz_vip' and meta_value = '$key'" ,ARRAY_A);
				$array['vip'][$key]['num'] = $num[0]["COUNT(*)"];
				$array['vip'][$key]['color'] = isset($value['color']) ? $value['color'] : $this->randrgb();
				$array['vip'][$key]['name'] = $value['name'];
				
				$users_all =  $users_all - $num[0]["COUNT(*)"];
				$array['vip']['normal']['num'] = $users_all;
				$array['vip']['normal']['color'] = '#224fec';
				$array['vip']['normal']['name'] = '普通用户';
			}elseif(strpos($key,'lv') !== false){
				$num = $this->wpdb->get_results( "select COUNT(*) from $table_name_a a join $table_name b on a.ID = b.user_id and b.meta_key = 'zrz_lv' and meta_value = '$key'" ,ARRAY_A);	
				$array['lv'][$key]['num'] = $num[0]["COUNT(*)"];
				$array['lv'][$key]['name'] = $value['name'];
				$array['lv'][$key]['color'] = $this->randrgb();
			}
		}
		//var_dump($array);
		return $array;
	}
	//返回未发货订单
	public function tz_get_c_order(){
	    $table_name = $this->wpdb->prefix . 'zrz_order';
	    $num = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where order_state='f'");
	    return $num;
	}
	//返回近七天用户购买VIP的数量
	public function tz_get_user_vip_buy_num(){
		$time = $this->time;
		$time = $time['a'];
		$table_name = $this->wpdb->prefix . 'zrz_order';
		$roles = User::get_user_roles();
		$array = array();
		foreach ($roles as $key => $value) {
			if(strpos($key,'vip') !== false){
				$num = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where order_type='vip' and order_key='$key' and order_state='q' and order_date > '$time[0]'");
				$array[$value['name']][] = $num;
				$num = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where order_type='vip' and order_key='$key' and order_state='q' and order_date > '$time[1]' and order_date < '$time[0]'");
				$array[$value['name']][] = $num;
				$num = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where order_type='vip' and order_key='$key' and order_state='q' and order_date > '$time[2]' and order_date < '$time[1]'");
				$array[$value['name']][] = $num;
				$num = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where order_type='vip' and order_key='$key' and order_state='q' and order_date > '$time[3]' and order_date < '$time[2]'");
				$array[$value['name']][] = $num;
				$num = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where order_type='vip' and order_key='$key' and order_state='q' and order_date > '$time[4]' and order_date < '$time[3]'");
				$array[$value['name']][] = $num;
				$num = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where order_type='vip' and order_key='$key' and order_state='q' and order_date > '$time[5]' and order_date < '$time[4]'");
				$array[$value['name']][] = $num;
				$num = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where order_type='vip' and order_key='$key' and order_state='q' and order_date > '$time[6]' and order_date < '$time[5]'");
				$array[$value['name']][] = $num;
				$array[$value['name']][] = $value['color'];
			}
		}
		return $array;
	}
	//返回近七天下载量
	public function tz_get_user_download_num(){
		$time = $this->time;
		$time = $time['a'];
		$table_name = $this->wpdb->prefix . 'TZ_download';
		$a = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where TZ_date > '$time[0]'");
		$b = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where TZ_date > '$time[1]' and TZ_date < '$time[0]'");
		$c = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where TZ_date > '$time[2]' and TZ_date < '$time[1]'");
		$d = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where TZ_date > '$time[3]' and TZ_date < '$time[2]'");
		$e = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where TZ_date > '$time[4]' and TZ_date < '$time[3]'");
		$f = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where TZ_date > '$time[5]' and TZ_date < '$time[4]'");
		$g = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name where TZ_date > '$time[6]' and TZ_date < '$time[5]'");
		return array($a,$b,$c,$d,$e,$f,$g);
	}

	//返回近七天购买vip收入
	public function tz_get_user_buy_vip_money(){
		$time = $this->time;
		$time = $time['a'];
		$table_name = $this->wpdb->prefix . 'zrz_order';
		$a = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name WHERE order_type = 'vip' and order_state = 'q' and order_date > '$time[0]'" ,ARRAY_A);
		$b = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name where order_type = 'vip' and order_state = 'q' and order_date > '$time[1]' and order_date < '$time[0]'" ,ARRAY_A);
		$c = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name where order_type = 'vip' and order_state = 'q' and order_date > '$time[2]' and order_date < '$time[1]'" ,ARRAY_A);
		$d = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name where order_type = 'vip' and order_state = 'q' and order_date > '$time[3]' and order_date < '$time[2]'" ,ARRAY_A);
		$e = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name where order_type = 'vip' and order_state = 'q' and order_date > '$time[4]' and order_date < '$time[3]'" ,ARRAY_A);
		$f = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name where order_type = 'vip' and order_state = 'q' and order_date > '$time[5]' and order_date < '$time[4]'" ,ARRAY_A);
		$g = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name where order_type = 'vip' and order_state = 'q' and order_date > '$time[6]' and order_date < '$time[5]'" ,ARRAY_A);
		return tj_change_rate(array(
		    $a[0]['total'],
		    $b[0]['total'],
		    $c[0]['total'],
		    $d[0]['total'],
		    $e[0]['total'],
		    $f[0]['total'],
		    $g[0]['total']
		));
	}
	//返回近七天余额充值收入
	public function tz_get_user_cz_money(){
		$time = $this->time;
		$time = $time['a'];
		$table_name = $this->wpdb->prefix . 'zrz_order';
		$a = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name WHERE order_type = 'cz' and order_state = 'q' and order_date > '$time[0]'" ,ARRAY_A);
		$b = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name where order_type = 'cz' and order_state = 'q' and order_date > '$time[1]' and order_date < '$time[0]'" ,ARRAY_A);
		$c = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name where order_type = 'cz' and order_state = 'q' and order_date > '$time[2]' and order_date < '$time[1]'" ,ARRAY_A);
		$d = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name where order_type = 'cz' and order_state = 'q' and order_date > '$time[3]' and order_date < '$time[2]'" ,ARRAY_A);
		$e = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name where order_type = 'cz' and order_state = 'q' and order_date > '$time[4]' and order_date < '$time[3]'" ,ARRAY_A);
		$f = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name where order_type = 'cz' and order_state = 'q' and order_date > '$time[5]' and order_date < '$time[4]'" ,ARRAY_A);
		$g = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name where order_type = 'cz' and order_state = 'q' and order_date > '$time[6]' and order_date < '$time[5]'" ,ARRAY_A);
		return tj_change_rate(array(
		    $a[0]['total'],
		    $b[0]['total'],
		    $c[0]['total'],
		    $d[0]['total'],
		    $e[0]['total'],
		    $f[0]['total'],
		    $g[0]['total']
		    ));
	}
	//返回近七天积分充值收入
	public function tz_get_user_cz_credit(){
		$time = $this->time;
		$time = $time['a'];
		$table_name = $this->wpdb->prefix . 'zrz_order';
		$a = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name WHERE order_type = 'cg' and order_state = 'q' and order_date > '$time[0]'" ,ARRAY_A);
		$b = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name where order_type = 'cg' and order_state = 'q' and order_date > '$time[1]' and order_date < '$time[0]'" ,ARRAY_A);
		$c = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name where order_type = 'cg' and order_state = 'q' and order_date > '$time[2]' and order_date < '$time[1]'" ,ARRAY_A);
		$d = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name where order_type = 'cg' and order_state = 'q' and order_date > '$time[3]' and order_date < '$time[2]'" ,ARRAY_A);
		$e = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name where order_type = 'cg' and order_state = 'q' and order_date > '$time[4]' and order_date < '$time[3]'" ,ARRAY_A);
		$f = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name where order_type = 'cg' and order_state = 'q' and order_date > '$time[5]' and order_date < '$time[4]'" ,ARRAY_A);
		$g = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name where order_type = 'cg' and order_state = 'q' and order_date > '$time[6]' and order_date < '$time[5]'" ,ARRAY_A);
		return tj_change_rate(array(
			$a[0]['total'],
		    $b[0]['total'],
		    $c[0]['total'],
		    $d[0]['total'],
		    $e[0]['total'],
		    $f[0]['total'],
		    $g[0]['total']
		));
	}
	//返回近七天付费下载收入
	public function tz_get_user_down_money(){
		$time = $this->time;
		$time = $time['a'];
		$table_name = $this->wpdb->prefix . 'zrz_order';
		$a = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name WHERE order_type = 'x' and order_state = 'q' and money_type = '0' and order_date > '$time[0]'" ,ARRAY_A);
		$b = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name where order_type = 'x' and order_state = 'q' and money_type = '0' and order_date > '$time[1]' and order_date < '$time[0]'" ,ARRAY_A);
		$c = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name where order_type = 'x' and order_state = 'q' and money_type = '0' and order_date > '$time[2]' and order_date < '$time[1]'" ,ARRAY_A);
		$d = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name where order_type = 'x' and order_state = 'q' and money_type = '0' and order_date > '$time[3]' and order_date < '$time[2]'" ,ARRAY_A);
		$e = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name where order_type = 'x' and order_state = 'q' and money_type = '0' and order_date > '$time[4]' and order_date < '$time[3]'" ,ARRAY_A);
		$f = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name where order_type = 'x' and order_state = 'q' and money_type = '0' and order_date > '$time[5]' and order_date < '$time[4]'" ,ARRAY_A);
		$g = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name where order_type = 'x' and order_state = 'q' and money_type = '0' and order_date > '$time[6]' and order_date < '$time[5]'" ,ARRAY_A);
		return tj_change_rate(array(
			$a[0]['total'],
		    $b[0]['total'],
		    $c[0]['total'],
		    $d[0]['total'],
		    $e[0]['total'],
		    $f[0]['total'],
		    $g[0]['total']
		));
	}
	//返回近七天文章隐藏付费收入
	public function tz_get_user_w_money(){
		$time = $this->time;
		$time = $time['a'];
		$table_name = $this->wpdb->prefix . 'zrz_order';
		$a = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name WHERE order_type = 'w' and order_state = 'q' and money_type = '0' and order_date > '$time[0]'" ,ARRAY_A);
		$b = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name where order_type = 'w' and order_state = 'q' and money_type = '0' and order_date > '$time[1]' and order_date < '$time[0]'" ,ARRAY_A);
		$c = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name where order_type = 'w' and order_state = 'q' and money_type = '0' and order_date > '$time[2]' and order_date < '$time[1]'" ,ARRAY_A);
		$d = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name where order_type = 'w' and order_state = 'q' and money_type = '0' and order_date > '$time[3]' and order_date < '$time[2]'" ,ARRAY_A);
		$e = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name where order_type = 'w' and order_state = 'q' and money_type = '0' and order_date > '$time[4]' and order_date < '$time[3]'" ,ARRAY_A);
		$f = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name where order_type = 'w' and order_state = 'q' and money_type = '0' and order_date > '$time[5]' and order_date < '$time[4]'" ,ARRAY_A);
		$g = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name where order_type = 'w' and order_state = 'q' and money_type = '0' and order_date > '$time[6]' and order_date < '$time[5]'" ,ARRAY_A);
		return tj_change_rate(array(
			$a[0]['total'],
		    $b[0]['total'],
		    $c[0]['total'],
		    $d[0]['total'],
		    $e[0]['total'],
		    $f[0]['total'],
		    $g[0]['total']
		));
	}
	//返回近七天文章打赏收入
	public function tz_get_user_ds_money(){
		$time = $this->time;
		$time = $time['a'];
		$table_name = $this->wpdb->prefix . 'zrz_order';
		$a = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name WHERE order_type = 'ds' and order_state = 'q' and money_type = '0' and order_date > '$time[0]'" ,ARRAY_A);
		$b = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name where order_type = 'ds' and order_state = 'q' and money_type = '0' and order_date > '$time[1]' and order_date < '$time[0]'" ,ARRAY_A);
		$c = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name where order_type = 'ds' and order_state = 'q' and money_type = '0' and order_date > '$time[2]' and order_date < '$time[1]'" ,ARRAY_A);
		$d = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name where order_type = 'ds' and order_state = 'q' and money_type = '0' and order_date > '$time[3]' and order_date < '$time[2]'" ,ARRAY_A);
		$e = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name where order_type = 'ds' and order_state = 'q' and money_type = '0' and order_date > '$time[4]' and order_date < '$time[3]'" ,ARRAY_A);
		$f = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name where order_type = 'ds' and order_state = 'q' and money_type = '0' and order_date > '$time[5]' and order_date < '$time[4]'" ,ARRAY_A);
		$g = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name where order_type = 'ds' and order_state = 'q' and money_type = '0' and order_date > '$time[6]' and order_date < '$time[5]'" ,ARRAY_A);
		return tj_change_rate(array(
			$a[0]['total'],
		    $b[0]['total'],
		    $c[0]['total'],
		    $d[0]['total'],
		    $e[0]['total'],
		    $f[0]['total'],
		    $g[0]['total']
		));
	}
	//返回近七天卡密充值数量
	public function tz_get_card_num(){
		$time = $this->time;
		$time = $time['a'];
		$table_name = $this->wpdb->prefix . 'zrz_message';
		$a = $this->wpdb->get_results("SELECT SUM(BINARY(msg_credit)) AS total FROM $table_name where msg_type=55 and msg_date > '$time[0]'",ARRAY_A);
		$b = $this->wpdb->get_results("SELECT SUM(BINARY(msg_credit)) AS total FROM $table_name where msg_type=55 and msg_date > '$time[1]' and msg_date < '$time[0]'",ARRAY_A);
		$c = $this->wpdb->get_results("SELECT SUM(BINARY(msg_credit)) AS total FROM $table_name where msg_type=55 and msg_date > '$time[2]' and msg_date < '$time[1]'",ARRAY_A);
		$d = $this->wpdb->get_results("SELECT SUM(BINARY(msg_credit)) AS total FROM $table_name where msg_type=55 and msg_date > '$time[3]' and msg_date < '$time[2]'",ARRAY_A);
		$e = $this->wpdb->get_results("SELECT SUM(BINARY(msg_credit)) AS total FROM $table_name where msg_type=55 and msg_date > '$time[4]' and msg_date < '$time[3]'",ARRAY_A);
		$f = $this->wpdb->get_results("SELECT SUM(BINARY(msg_credit)) AS total FROM $table_name where msg_type=55 and msg_date > '$time[5]' and msg_date < '$time[4]'",ARRAY_A);
		$g = $this->wpdb->get_results("SELECT SUM(BINARY(msg_credit)) AS total FROM $table_name where msg_type=55 and msg_date > '$time[6]' and msg_date < '$time[5]'",ARRAY_A);
		return tj_change_rate(array(
			$a[0]['total'],
		    $b[0]['total'],
		    $c[0]['total'],
		    $d[0]['total'],
		    $e[0]['total'],
		    $f[0]['total'],
		    $g[0]['total']
		));
	}
	public function tz_get_today_money(){
		$time = $this->time;
		$time = $time['a'];
		$table_name = $this->wpdb->prefix . 'zrz_order';
		$a = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name WHERE order_state = 'q' and money_type = '0' and order_date > '$time[0]' and pay_type != 'balance'" ,ARRAY_A);
		$b = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name WHERE order_state = 'q' and money_type = '0' and order_date > '$time[1]' and order_date < '$time[0]' and pay_type != 'balance'" ,ARRAY_A);
		$c = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name WHERE order_state = 'q' and money_type = '0' and order_date > '$time[2]' and order_date < '$time[1]' and pay_type != 'balance'" ,ARRAY_A);
		$d = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name WHERE order_state = 'q' and money_type = '0' and order_date > '$time[3]' and order_date < '$time[2]' and pay_type != 'balance'" ,ARRAY_A);
		$e = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name WHERE order_state = 'q' and money_type = '0' and order_date > '$time[4]' and order_date < '$time[3]' and pay_type != 'balance'" ,ARRAY_A);
		$f = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name WHERE order_state = 'q' and money_type = '0' and order_date > '$time[5]' and order_date < '$time[4]' and pay_type != 'balance'" ,ARRAY_A);
		$g = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name WHERE order_state = 'q' and money_type = '0' and order_date > '$time[6]' and order_date < '$time[5]' and pay_type != 'balance'" ,ARRAY_A);
		$table_name = $this->wpdb->prefix . 'zrz_message';
		$aa = $this->wpdb->get_results("SELECT SUM(BINARY(msg_credit)) AS total FROM $table_name where msg_type=55 and msg_date > '$time[0]'",ARRAY_A);
		$bb = $this->wpdb->get_results("SELECT SUM(BINARY(msg_credit)) AS total FROM $table_name where msg_type=55 and msg_date > '$time[1]' and msg_date < '$time[0]'",ARRAY_A);
		$cc = $this->wpdb->get_results("SELECT SUM(BINARY(msg_credit)) AS total FROM $table_name where msg_type=55 and msg_date > '$time[2]' and msg_date < '$time[1]'",ARRAY_A);
		$dd = $this->wpdb->get_results("SELECT SUM(BINARY(msg_credit)) AS total FROM $table_name where msg_type=55 and msg_date > '$time[3]' and msg_date < '$time[2]'",ARRAY_A);
		$ee = $this->wpdb->get_results("SELECT SUM(BINARY(msg_credit)) AS total FROM $table_name where msg_type=55 and msg_date > '$time[4]' and msg_date < '$time[3]'",ARRAY_A);
		$ff = $this->wpdb->get_results("SELECT SUM(BINARY(msg_credit)) AS total FROM $table_name where msg_type=55 and msg_date > '$time[5]' and msg_date < '$time[4]'",ARRAY_A);
		$gg = $this->wpdb->get_results("SELECT SUM(BINARY(msg_credit)) AS total FROM $table_name where msg_type=55 and msg_date > '$time[6]' and msg_date < '$time[5]'",ARRAY_A);
		return tj_change_rate(array(
			round( ($a[0]['total']+$aa[0]['total']) , 2),
			round( ($b[0]['total']+$bb[0]['total']) , 2),
			round( ($c[0]['total']+$cc[0]['total']) , 2),
			round( ($d[0]['total']+$dd[0]['total']) , 2),
			round( ($e[0]['total']+$ee[0]['total']) , 2),
			round( ($f[0]['total']+$ff[0]['total']) , 2),
			round( ($g[0]['total']+$gg[0]['total']) , 2)
		));
	}
	public function tz_get_today_money_paypal(){
		$time = $this->time;
		$time = $time['a'];
		$table_name = $this->wpdb->prefix . 'zrz_order';
		$a = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name WHERE order_state = 'q' and money_type = '0' and order_date > '$time[0]' and pay_type = 'paypal'" ,ARRAY_A);
		$b = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name WHERE order_state = 'q' and money_type = '0' and order_date > '$time[1]' and order_date < '$time[0]' and pay_type = 'paypal'" ,ARRAY_A);
		$c = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name WHERE order_state = 'q' and money_type = '0' and order_date > '$time[2]' and order_date < '$time[1]' and pay_type = 'paypal'" ,ARRAY_A);
		$d = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name WHERE order_state = 'q' and money_type = '0' and order_date > '$time[3]' and order_date < '$time[2]' and pay_type = 'paypal'" ,ARRAY_A);
		$e = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name WHERE order_state = 'q' and money_type = '0' and order_date > '$time[4]' and order_date < '$time[3]' and pay_type = 'paypal'" ,ARRAY_A);
		$f = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name WHERE order_state = 'q' and money_type = '0' and order_date > '$time[5]' and order_date < '$time[4]' and pay_type = 'paypal'" ,ARRAY_A);
        $g = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name WHERE order_state = 'q' and money_type = '0' and order_date > '$time[6]' and order_date < '$time[5]' and pay_type = 'paypal'" ,ARRAY_A);
        $rate = tj_get_option('b2_normal_pay','paypal_rate');
		return tj_change_rate(array(
			round( ($a[0]['total'])*$rate , 2),
			round( ($b[0]['total'])*$rate , 2),
			round( ($c[0]['total'])*$rate , 2),
			round( ($d[0]['total'])*$rate , 2),
			round( ($e[0]['total'])*$rate , 2),
			round( ($f[0]['total'])*$rate , 2),
			round( ($g[0]['total'])*$rate , 2)
		));
	}
	public function tz_get_month_money(){
		$month = $this ->get_this_month();
		$month = $month['start_month'];
		$table_name = $this->wpdb->prefix . 'zrz_order';
		$a = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name WHERE order_state = 'q' and money_type = '0' and order_date > '$month' and pay_type != 'balance'" ,ARRAY_A);
		$table_name = $this->wpdb->prefix . 'zrz_message';
		$b = $this->wpdb->get_results("SELECT SUM(BINARY(msg_credit)) AS total FROM $table_name where msg_type=55 and msg_date > '$month'",ARRAY_A);
		$a = isset($a[0]['total']) ? $a[0]['total'] : 0;
		$b = isset($b[0]['total']) ? $b[0]['total'] : 0;
		return tj_change_rate(round( ($a + $b) , 2));
	}
	public function tz_get_last_money(){
		$month = $this ->get_last_month();
		$moa = $month['start_month'];
	    $mob = $month['end_month'];
		$table_name = $this->wpdb->prefix . 'zrz_order';
		$a = $this->wpdb->get_results( "SELECT SUM(BINARY(order_total)) AS total FROM $table_name WHERE order_state = 'q' and money_type = '0' and order_date > '$moa' and order_date < '$mob' and pay_type != 'balance'" ,ARRAY_A);
		$table_name = $this->wpdb->prefix . 'zrz_message';
		$b = $this->wpdb->get_results("SELECT SUM(BINARY(msg_credit)) AS total FROM $table_name where msg_type=55 and msg_date > '$moa' and msg_date < '$mob'",ARRAY_A);
		$a = isset($a[0]['total']) ? $a[0]['total'] : 0;
		$b = isset($b[0]['total']) ? $b[0]['total'] : 0;
		return tj_change_rate(round( ($a + $b) , 2));
	}
	
	public function tz_get_invitecode_state(){
		$time = $this->time;
		$time = $time['a'];
		$table_name = $this->wpdb->prefix . 'zrz_message';
		$a = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE `msg_type`= 46 and msg_date > '$time[0]'");
		$b = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE `msg_type`= 46 and msg_date > '$time[1]' and msg_date < '$time[0]'");
		$c = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE `msg_type`= 46 and msg_date > '$time[2]' and msg_date < '$time[1]'");
		$d = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE `msg_type`= 46 and msg_date > '$time[3]' and msg_date < '$time[2]'");
		$e = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE `msg_type`= 46 and msg_date > '$time[4]' and msg_date < '$time[3]'");
		$f = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE `msg_type`= 46 and msg_date > '$time[5]' and msg_date < '$time[4]'");
		$g = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE `msg_type`= 46 and msg_date > '$time[6]' and msg_date < '$time[5]'");
		return array(
		    $a,$b,$c,$d,$e,$f,$g
		);
	}
	
	public function get_status_count($status){
        global $wpdb;
        $table_name = $wpdb->prefix . 'zrz_directmessage';

        if($status === 'all'){
            $from = "(SELECT * FROM $table_name WHERE `to`=0 GROUP BY `mark`)";
        }else{
            $status = $status === 'replied' ? 1 : 0;
            $from = "(SELECT * FROM $table_name WHERE `to`=0 AND `status`=$status GROUP BY `mark`)";
        }
        
        $query = "SELECT COUNT(*) FROM $from b ";

        $rowcount = $wpdb->get_var($query);
        
        return $rowcount ? $rowcount : 0;
    }
    
    public function get_renzhengshenhe_num(){
        global $wpdb;
        $table_name = $wpdb->prefix . 'b2_verify';
        $query = "SELECT COUNT(*) FROM $table_name where `status` = 4 ";
        $count = $wpdb->get_var($query);
        return $count;
    }
}