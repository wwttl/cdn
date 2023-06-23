<?php
use B2\Modules\Common\User;

function add_zrz_vip_filter() {

    if ( isset( $_GET[ 'zrz_vip' ]) ) {
        $section = $_GET[ 'zrz_vip' ];
        $section = !empty( $section[ 0 ] ) ? $section[ 0 ] : $section[ 1 ];
    } else {
        $section = -1;
    }
    echo ' <select name="zrz_vip[]" style="float:none;margin-left:14px">
    	<option value="no">TJ_用户筛选...</option>';
    $roles = User::get_user_roles();
    foreach($roles as $key => $value){
	    $selected = $key == $section ? ' selected="selected"' : '';
	    echo '<option value="' . $key . '"' . $selected . '>' . $value['name'] . '</option>';
    }
    echo '</select>';
    echo '<input type="submit" class="button" value="筛选">';

////////////////////////////////////////////////////////////////////////////////////////
	if ( isset( $_GET[ 'tj_filter' ]) ) {
	    $section = $_GET[ 'tj_filter' ];
	    $section = !empty( $section[ 0 ] ) ? $section[ 0 ] : $section[ 1 ];
	} else {
	    $section = -1;
	}
	echo ' <select name="tj_filter[]" style="float:none;margin-left:14px">
	    <option value="no">TJ_状态筛选...</option>';
	$roles = array(
	    'today_login' => '今日登陆',
	    'today_sign' => '今日注册',
	);
	foreach($roles as $key => $value){
	    $selected = $key == $section ? ' selected="selected"' : '';
	    echo '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';
	}
	echo '</select>';
	echo '<input type="submit" class="button" value="筛选">';
}
add_action( 'restrict_manage_users', 'add_zrz_vip_filter' );

function filter_users_by_zrz_vip( $query ) {

    global $pagenow;

    if ( is_admin() && 
         'users.php' == $pagenow && 
         isset( $_GET[ 'zrz_vip' ] ) && 
         is_array( $_GET[ 'zrz_vip' ] )
        ) {
        $section = $_GET[ 'zrz_vip' ];
        $section = !empty( $section[ 0 ] ) ? $section[ 0 ] : $section[ 1 ];
        if(strpos($section,'vip') !== false){
	        $meta_query = array(
	            array(
	                'key' => 'zrz_vip',
	                'value' => $section
	            )
	        );
	        $query->set( 'meta_key', 'zrz_vip' );
	        $query->set( 'meta_query', $meta_query );        	
        }elseif (strpos($section,'lv') !== false) {
	        $meta_query = array(
	            array(
	                'key' => 'zrz_lv',
	                'value' => $section
	            )
	        );
	        $query->set( 'meta_key', 'zrz_lv' );
	        $query->set( 'meta_query', $meta_query );
        }
    }
    global $pagenow;

	if ( is_admin() && 
	     'users.php' == $pagenow && 
	     isset( $_GET[ 'tj_filter' ] ) && 
	     is_array( $_GET[ 'tj_filter' ] )
	    ) {
	    $section = $_GET[ 'tj_filter' ];
	    $section = !empty( $section[ 0 ] ) ? $section[ 0 ] : $section[ 1 ];
	    if(strpos($section,'today_login') !== false){
	        $meta_query = array(
	            array(
	                'key' => 'user_last_action',
	                'value' => tj_today_time(),
	                'compare' => '>'
	            )
	        );
	        $query->set( 'meta_key', 'user_last_action' );
	        $query->set( 'meta_query', $meta_query );
	    }elseif (strpos($section,'today_sign') !== false) {
	    	$todaytime = tj_today_time();
	    	$query->set('date_query',[
	    	    'date_query' => [
	    	    	'after'     => date("Y-m-d H:i:s",$todaytime-8*60*60),
            		'inclusive' => true,
			    ] 	
	    	]);
	    }
	}
}
add_filter( 'pre_get_users', 'filter_users_by_zrz_vip' );
function tj_today_time(){
    date_default_timezone_set("Asia/Shanghai");
    ini_set('date.timezone','Asia/Shanghai');
    return strtotime("today");
}
function tz_add_user_id_column($columns) {
	$columns['a_username'] = '昵称';
    $columns['user_id'] = 'ID';
    $columns['user_lv'] = '会员';
    $columns['vip_endtime'] = '会员到期时间';
    $columns['credit'] = '积分';
    $columns['money'] = '余额';
    $columns['lastaction'] = '最后登录';
    $columns['lastip'] = '最后IP';
    unset($columns['email']);
    unset($columns['role']);
    unset($columns['posts']);
    $columns['email'] = '电子邮件';
    $columns['role'] = '角色';
    $columns['posts'] = '文章';
    return $columns;
}
add_filter('manage_users_columns', 'tz_add_user_id_column', 10, 3);
function tz_show_user_id_column_content($value, $column_name, $user_id) {
    
    if ( 'user_id' == $column_name )
        return $user_id;
    if ( 'credit' == $column_name )
        return get_user_meta($user_id,'zrz_credit_total',true) ? get_user_meta($user_id,'zrz_credit_total',true) : 0;
    if ( 'money' == $column_name )
        return get_user_meta($user_id,'zrz_rmb',true) ? get_user_meta($user_id,'zrz_rmb',true) : 0;
    if ( 'lastaction' == $column_name )
        return get_user_meta($user_id,'user_last_action',true) ? date("Y-m-d H:i:s",get_user_meta($user_id,'user_last_action',true)) : 0;
    if ( 'vip_endtime' == $column_name){
    	$time = get_user_meta($user_id,'zrz_vip_time',true) ? get_user_meta($user_id,'zrz_vip_time',true) : '';
    	return isset($time['end']) ? $time['end'] == 0 ? '永久' : date('Y-m-d H:i:s',$time['end']) : '';
    }
    if ( 'lastip' == $column_name )
        return get_user_meta($user_id,'user_last_ip',true) ? get_user_meta($user_id,'user_last_ip',true) : 0;
    return $value;
}
add_action('manage_users_custom_column',  'tz_show_user_id_column_content', 20, 3);

add_filter('manage_users_sortable_columns', 'tz_users_sortable_columns' );
add_action('pre_user_query', 'tz_users_search_order' );
function tz_users_sortable_columns($sortable_columns){
    $sortable_columns['user_id'] = 'ID';
    $sortable_columns['credit'] = 'credit';
    $sortable_columns['money'] = 'money';
    $sortable_columns['lastaction'] = 'lastaction';
    return $sortable_columns;
}
function tz_users_search_order($obj){
    $order = isset($_REQUEST['order']) ? $_REQUEST['order'] : false;
    $order_by = isset($_REQUEST['orderby']) ? $_REQUEST['orderby'] : false;
    global $wpdb;
    if($order_by == 'ID'){
        if( !in_array($order,array('asc','desc')) ){
            $_REQUEST['order'] = 'desc';
        }
        $obj->query_orderby = 'ORDER BY ID '.$_REQUEST['order'];
    }
    if($order_by == 'credit'){
        $obj->query_from .= " left outer join {$wpdb->usermeta} m1 ON {$wpdb->users}.ID=m1.user_id AND (m1.meta_key='zrz_credit_total')"; 
        $obj->query_orderby = ' ORDER BY (m1.meta_value+0) '. $order;
        //$obj->query_orderby = 'ORDER BY credit '.$_REQUEST['order'];
    }
    if($order_by =='money'){
        $obj->query_from .= " left outer join {$wpdb->usermeta} m1 ON {$wpdb->users}.ID=m1.user_id AND (m1.meta_key='zrz_rmb')"; 
        $obj->query_orderby = ' ORDER BY (m1.meta_value+0) '. $order;
        //$obj->query_vars["meta_key"] = "zrz_rmb";
        //$obj->query_vars["orderby"] = "meta_value_num";
    }
    if($order_by =='lastaction'){
        $obj->query_from .= " left outer join {$wpdb->usermeta} m1 ON {$wpdb->users}.ID=m1.user_id AND (m1.meta_key='user_last_action')"; 
        $obj->query_orderby = ' ORDER BY (m1.meta_value+0) '. $order;
    }
    //var_dump($obj);
}


add_action('wp_footer',function(){
    ?>
    <div id="tjuser"></div>
    <?php
});
add_action('wp_enqueue_scripts', function(){
    wp_enqueue_script( 'tj_user',B2_TJ_URL.'b2_user_control/tj_user.js', array(), B2_TJ_VERSION , true );
} ,99);
add_action( 'rest_api_init', function(){
    register_rest_route('b2/v1','/tjuser',array(
        'methods'=>'post',
        'callback'=> function($request){
            $res = tj_user_fun($request);
            if(isset($res['error'])){
                return new \WP_Error('tj_error',$res['error'],array('status'=>403));
            }else{
                return new \WP_REST_Response($res,200);
            } 
        },
        'permission_callback' => '__return_true'
    ));
});


function tj_user_fun($request){
	date_default_timezone_set( 'Asia/Shanghai' );
	ini_set('date.timezone','Asia/Shanghai');
    $user_id = get_current_user_id();
    $user_ip = tj_get_user_ip();
	if($user_id){
		if (!isset($_SESSION)) {
	        session_start();
	    }
	    /////////////////////////////////////////////////////////////
	    if(isset($_SESSION['error_user_id'])){
	    	if(!in_array($user_id,$_SESSION['error_user_id'])){
	    		$_SESSION['error_user_id'][] = $user_id;
	    		//记录异常数据
	    		global $wpdb;
	    		$table_name = $wpdb->prefix . 'Tj_User_Error';
	    		if(isset($_SESSION['error_user_id_recodeid'])){
	    			$arg = array( 'id' => $_SESSION['error_user_id_recodeid'] );
            		$data = array(
	                    'data' => implode("丨",$_SESSION['error_user_id']),
	                    'ip' => $user_ip,
	                );
			        $wpdb->update(
			            $table_name,
			            $data,
			            $arg
			        );
	    		}else{
	                $data = array(
	                    'type' => 'common_device',
	                    'des' => '同设备多用户登陆',
	                    'data' => implode("丨",$_SESSION['error_user_id']),
	                    'ip' => $user_ip,
	                    'user' => $user_id,
	                );
	                $wpdb->insert( $table_name, $data);
	                $_SESSION['error_user_id_recodeid'] = $wpdb->insert_id;	    			
	    		}
	    	}
	    }else{
	    	$_SESSION['error_user_id'][] = $user_id;
	    }
	    /////////////////////////////////////////////////////////////////////////////////
	    $user_last_action = get_user_meta($user_id,'user_last_action',true) ? get_user_meta($user_id,'user_last_action',true) : 0;
        $today_time = tj_today_time();
    	if($user_last_action<$today_time){
    		$login_user_option = get_option('tj_user_login_num') ? get_option('tj_user_login_num') : array();
    		$login_user_option['a'.$today_time] = isset($login_user_option['a'.$today_time]) ? intval($login_user_option['a'.$today_time]) + 1 : 1;
    		if(count($login_user_option)>7){
    			array_shift($login_user_option);
    		}
    		update_option('tj_user_login_num',$login_user_option);
    	}
    	//////////////////////////////////////////////////////////////////////////////////////
    	$user_meta_ip = get_user_meta($user_id,'user_last_ip',true);
    	if($user_meta_ip){
    	    if($user_meta_ip !== $user_ip){
    	        
    	        $city = '城市获取失败';
    	        $city2 = '城市获取失败2';
        	    //$response = wp_remote_get( 'http://ip-api.com/json/'.$user_meta_ip.'?lang=zh-CN' );
        	    $response = wp_remote_get( 'http://whois.pconline.com.cn/ipJson.jsp?ip='.$user_meta_ip.'&json=true' );
                if ( is_array( $response ) && !is_wp_error($response) && $response['response']['code'] == '200' ) {
                	$body = $response['body'];
                	$position = json_decode(iconv('GB2312', 'UTF-8', $body));
                	$city = $position->pro.$position->city;
                	
                    //$response = wp_remote_get( 'http://ip-api.com/json/'.$user_ip.'?lang=zh-CN' );
                    $response = wp_remote_get( 'http://whois.pconline.com.cn/ipJson.jsp?ip='.$user_ip.'&json=true' );
                    if ( is_array( $response ) && !is_wp_error($response) && $response['response']['code'] == '200' ) {
                    	$body = $response['body'];
                    	$position = json_decode(iconv('GB2312', 'UTF-8', $body));
                    	$city2 = $position->pro.$position->city;
                    }
                    
                }
    	        
    	        if($city!==$city2){
        	        $today_time = date("Y-m-d H:i:s",strtotime("today"));
    	    		global $wpdb;
    	    		$table_name = $wpdb->prefix . 'Tj_User_Error';
    	    		$error_data = $wpdb->get_results( "SELECT * FROM $table_name WHERE type = 'different_ip' and user = $user_id and date > '$today_time'" ,ARRAY_A);
    	    		if(isset($error_data[0])){
    	    		    $date_id = $error_data[0]['id'];
    	    		    $arg = array( 'id' => $date_id );
    	    		    $data_ip_array = explode('丨',$error_data[0]['data']);
    	    		    $data_ip_array_ip = array();
    	    		    foreach($data_ip_array as $v){
    	    		        $data_ip_array_ip[] = explode(',',$v)[0];
    	    		    }
    	    		    if(!in_array($user_ip,$data_ip_array_ip)){
    	    		        $data_ip_array[] = $user_ip.','.$city2;
                    		$data = array(
        	                    'data' => implode("丨",$data_ip_array),
        	                    'ip' => $user_ip,
        	                );
        			        $wpdb->update(
        			            $table_name,
        			            $data,
        			            $arg
        			        );	    		        
    	    		    }
    	    		}else{
    	    		    $ip_array = array($user_meta_ip.','.$city,$user_ip.','.$city2);
        	    		$data = array(
                            'type' => 'different_ip',
                            'des' => '用户多IP登陆',
                            'data' => implode("丨",$ip_array),
                            'ip' => $user_ip,
                            'user' => $user_id,
                        );
                        $wpdb->insert( $table_name, $data);
                        $insert_id = $wpdb->insert_id;	    		    
    	    		}    	            
    	        }

	    		
    	    }
    	}
    	/////////////////////////////////////////////////////////////////////////////////////
		update_user_meta($user_id,'user_last_ip',$user_ip);
		update_user_meta($user_id,'user_last_action',time());
	}
    return 'success';
}

add_action('cmb2_admin_init','add_usererror_control_page','51');
function add_usererror_control_page(){
    $login = new_cmb2_box(array(
        'id'           => 'b2_usererror_control',
        'object_types' => array( 'options-page' ),
        'option_key'   => 'b2_usererror_control',
        'tab_group'    => 'b2_usererror_options',
        'parent_slug'  => 'b2_tz_main_control',
        'tab_title'    => __('统计','b2'),
        'menu_title'   => __('用户异常管理','b2'),
        'display_cb'   => 'usererror_function'
    ));
}
function usererror_function() {
    $usererror = new B2_usererror_List_Table();
    $usererror->prepare_items();
    $status = isset($_GET["status"]) ? esc_sql($_GET["status"]) : 'all';
?>
  <div class="wrap">
	  <h1 class="wp-heading-inline">用户异常处理</h1>
	  <br>
       <ul class="subsubsub">
            <li class="all">
                <a href="<?php echo home_url('/wp-admin/admin.php?page=b2_usererror_control&status=all'); ?>" class="<?php echo $status === 'all' ? 'current' : ''; ?>">
                    <?php echo __('所有','b2'); ?>
                </a> |</li>
            <li class="mine"><a href="<?php echo home_url('/wp-admin/admin.php?page=b2_usererror_control&status=0'); ?>" class="<?php echo $status === '0' ? 'current' : ''; ?>"><?php echo __('多IP','b2'); ?></a> |</li>
            <li class="publish"><a href="<?php echo home_url('/wp-admin/admin.php?page=b2_usererror_control&status=1'); ?>" class="<?php echo $status === '1' ? 'current' : ''; ?>"><?php echo __('多用户','b2'); ?></a></li>
        </ul>
	  <form id="movies-filter" method="get">
	    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
	    <?php 
	    	$usererror->display() ?>
	  </form>
  </div>
<?php 
}



if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class B2_usererror_List_Table extends WP_List_Table {

    function __construct(){
        global $status, $page;
              
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'id',    //singular name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );        
    }
  
    //默认的项目
    function column_default($item, $column_name){
        //var_dump($column_name);
        switch($column_name){
            case 'id':
            case 'date':
            case 'type':
            case 'des':
            case 'user':
            case 'lv':
            case 'ip':
            case 'data':
                return $item[$column_name];
            default:
                return $item->$column_name;
        }
    }

    //编辑按钮
    function column_ID($item){
        //Build row actions
        $actions = array(
            'delete'    => sprintf('<a href="?page=%s&action=%s&id=%s">删除</a>',$_REQUEST['page'],'delete',$item['id']),
        );
        
        //Return the title contents
        return sprintf('%1$s%2$s',
            $item['id'],
            $this->row_actions($actions)
        );
    }
    //批量操作回调
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            $item['id']                //The value of the checkbox should be the record's id
        );
    }
    function get_columns(){
        $columns = array(
            'cb'=> '<input type="checkbox" />', //选择框
            'id'=>'ID',
			'date'=>'记录时间',
			'type'=>'类型',
			'des'=>'描述',
			'user'=>'用户',
			'lv'=>'等级',
			'ip'=>'记录IP',
			'data'=>'数据',
        );
        return $columns;
    }
    function get_sortable_columns() {
        $sortable_columns = array(
            //'id'     => array('id',false),     //true means it's already sorted
        );
        return $sortable_columns;
    }
    function get_bulk_actions() {
        $actions = array(
            'delete'    => '删除'
        );
        return $actions;
    }
    function process_bulk_action() {
        if( 'delete'===$this->current_action() ) {
            global $wpdb; 
            $table_name = $wpdb->prefix . 'Tj_User_Error';
            if(is_array($_GET['id'])){
                foreach ($_GET['id'] as $key => $value) {
                    $wpdb->delete( $table_name, array( 'id' => $value ) );
                }
            }else{
                $wpdb->delete( $table_name, array( 'id' => $_GET['id'] ) );
            }
        }
    }
    function filter_data($data){
        foreach($data as $key => $val){
            $lv22 = User::get_user_lv($data[$key]['user']);
            $user_data = User::get_user_public_data($data[$key]['user']);
            $data[$key]['user'] = '<a href="'.$user_data['link'].'" target="_blank">'.$user_data['name'].'</a>';


            $data[$key]['lv'] = $lv22['vip']['icon'].' '.$lv22['lv']['icon'];
                            
			if($val['type']=='common_device'){
			    $data_user_list = explode("丨",$val['data']);
    			$array = array();
                foreach ($data_user_list as $user){
                	$user_data = User::get_user_public_data($user);
                	$array[] = '<a href="'.$user_data['link'].'" target="_blank">'.$user_data['name'].'</a>';
                }
                $data[$key]['data'] = implode("<br>",$array);
			}
            
            if($val['type']=='different_ip'){
                $different = explode("丨",$data[$key]['data']);
                foreach ($different as $k => $v){
                    $each = explode(",",$v);
                    $different[$k] = $v.','.'<a href="https://www.ip138.com/iplookup.asp?ip='.$each[0].'&action=2" target="_blank">核实</a>';
                }
                //str_replace("丨","<br>",$data[$key]['data']);
                $data[$key]['data'] = implode("<br>",$different);
            }

        }
        return $data;
    }

    function prepare_items() {
        global $wpdb; 
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);      
        $this->process_bulk_action();
        $current_page = $this->get_pagenum();
        $status = isset($_GET["status"]) ? esc_sql($_GET["status"]) : 'all';
        global $wpdb;
        $order = 'ORDER BY id DESC';
        if($status=='0'){
            $where = 'where type = \'different_ip\'';
        }elseif($status=='1'){
            $where = 'where type = \'common_device\'';
        }else{
            $where = '';
        }

        $table_name = $wpdb->prefix . 'Tj_User_Error';
        $per_page = 20;
        $limit = $per_page;
        $paged = $current_page;
        $offset = ($paged-1)*$per_page;
        $pages = $wpdb->get_var( "SELECT count(*) FROM $table_name $where");
        $cards = $wpdb->get_results( "SELECT * FROM $table_name $where $order LIMIT $offset,$limit" ,ARRAY_A );
        $cards = $this->filter_data($cards);

        //排序方式
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'id';
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order==='desc') ? -$result : $result;
        }
        //usort($cards, 'usort_reorder');

        //总数
        $this->items = $cards;
        $this->set_pagination_args( array(
            'total_items' => $pages,                  
            'per_page'    => $per_page,                     
            'total_pages' => ceil($pages/$limit)   
        ) );
    }
}



