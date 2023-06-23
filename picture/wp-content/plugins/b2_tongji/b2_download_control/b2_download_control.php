<?php
use B2\Modules\Common\User;
use B2\Modules\Common\Post;
//b2版本大于1.8.0可用
//解决无提取码等问题需要b2版本大于2.2.6
class TZ_download{
	public function __construct(){
		global $wpdb;
        $this->wpdb = $wpdb;
        add_filter( 'b2_download_file', array($this,'tz_download_record') );
	}
    public function get_download_data($post_id,$index,$i){
        $str = get_post_meta($post_id,'b2_single_post_download_group',true)[$index]['url'];
        if(!$str) return array();
    
        $str = trim($str, " \t\n\r\0\x0B\xC2\xA0");
        $str = explode(PHP_EOL, $str );
    
        $arg = array();
    
        foreach ($str as $k => $v) {
            $_v = explode('|', $v);
            if(!isset($_v[0]) && !isset($_v[1])) continue;
    
            $attr = array(
                'tq'=>'',
                'jy'=>''
            );
    
            if(isset($_v[2])){
                $attr = array(
                    'tq'=>'',
                    'jy'=>''
                );
        
                //检查字符串是否
                $a = explode(',',$_v[2]);
                
                foreach ($a as $k => $v) {
                    $b = explode('=', $v);
                    if(!isset($b[0]) && !isset($b[1])) continue;
        
                    $attr[$b[0]] = trim($b[1], " \t\n\r\0\x0B\xC2\xA0");
                }
            }
    
            //加密下载地址
            $arg[] = array(
                'name'=>$_v[0],
                'attr'=>$attr
            );
    
        }
        return $arg[$i];
    }
    
	public function tz_download_record( $data ) {
		
		$user_id = $data->user_id; //下载用户
		$post_id = $data->post_id; //所在文章
		$index = $data->index;         //第几个下载框
		$url = $data->url;         //资源链接
		$i = $data->i;         //第几号资源
		
		$get_down_data = $this->get_download_data($post_id,$index,$i);

		$file_name = $get_down_data['name']; //资源名称
		$file_tq = $get_down_data['attr']['tq'];//提取码
		$file_jy = $get_down_data['attr']['jy'];//解压码
		$this -> tz_download_record_insert($user_id,$post_id,$index+1,$i+1,$file_name,$url,$file_tq,$file_jy);
		
	    return $data;
	}

	public function tz_download_record_insert($user_id,$post_id,$index,$i,$file_name,$url,$file_tq,$file_jy){
		$data = array();
		$data['TZ_user'] = (int)$user_id;
		$data['TZ_post'] = (int)$post_id;
		$data['TZ_index'] = (string)$index;
		$data['TZ_post_i'] = (int)$i;
		$data['TZ_post_i_file_name'] = (string)$file_name;
		$data['TZ_post_i_file_url'] = (string)$url;
		$data['TZ_post_i_file_tq'] = (string)$file_tq;
        $data['TZ_post_i_file_jy'] = (string)$file_jy;
        $data['ip'] = tj_get_user_ip();
	  	$table_name = $this->wpdb->prefix . 'TZ_download';
		$this->wpdb->insert( $table_name, $data);
		//$todaytime = strtotime("today");
		//$todaytime = date("Y-m-d H:i:s",$todaytime);
		//$b = $this->wpdb->get_results("SELECT * FROM $table_name where TZ_user = $user_id and TZ_date > $todaytime",ARRAY_A);
		//foreach($b as $k => $v){
		//	if( $v['ip'] !== b2_get_user_ip() ){
		//		global $wpdb;
	    //		$table_name = $wpdb->prefix . 'Tj_User_Error';
	    //		$data = array(
        //            'type' => 'common_user',
        //            'des' => '同用户多IP登陆',
        //            'data' => implode("丨",$user_id),
        //            'ip' => $v['ip'],
        //        );
        //        $wpdb->insert( $table_name, $data);
		//	}
		//}
		
		
	}
}

$TZ_download = new TZ_download();


add_action('cmb2_admin_init','add_download_control_page','50');
function add_download_control_page(){
    $login = new_cmb2_box(array(
        'id'           => 'b2_download_control',
        'object_types' => array( 'options-page' ),
        'option_key'   => 'b2_download_control',
        'tab_group'    => 'b2_download_options',
        'parent_slug'  => 'b2_tz_main_control',
        'tab_title'    => __('下载','b2'),
        'menu_title'   => __('下载管理','b2'),
        'display_cb'   => 'download_function'
    ));
}
function download_function() {
    $cardlisttable = new B2_download_List_Table();
    $cardlisttable->prepare_items();
?>
  <div class="wrap">
	  <h1 class="wp-heading-inline">下载统计</h1>
	  <form id="movies-filter" method="get">
	    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
	    <?php if(isset($_GET['user']) || isset($_GET['post']) ){ ?>
	    <p><a class="button" href="<?php echo home_url('/wp-admin/admin.php?page=b2_download_control'); ?>">后退</a></p>
	    <?php } ?>
	    <?php 
	    	$cardlisttable->display() ?>
	  </form>
  </div>
<?php 
}

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class B2_download_List_Table extends WP_List_Table {
	function __construct(){
        global $status, $page;
              
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'TZ_id',    //singular name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );        
    }


    //默认的项目
    function column_default($item, $column_name){
        //var_dump($column_name);
        switch($column_name){
			case 'TZ_id':
			case 'TZ_date':
			case 'TZ_user':
			case 'ip':
			case 'TZ_post':
			case 'TZ_index':
			case 'TZ_post_i':
			case 'TZ_post_i_file_name':
			case 'TZ_post_i_file_url':
			case 'TZ_post_i_file_tq':
            case 'TZ_post_i_file_jy':
                return $item[$column_name];
            default:
                return print_r($item,true);
        }
    }
    //编辑按钮
    function column_ID($item){
        //Build row actions
        $actions = array(
            'delete'    => sprintf('<a href="?page=%s&action=%s&tz_id=%s">删除</a>',$_REQUEST['page'],'delete',$item['TZ_id']),
        );
        
        //Return the title contents
        return sprintf('%1$s%2$s',
            $item['tz_id'],
            $this->row_actions($actions)
        );
    }
    //批量操作回调
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            $item['TZ_id']                //The value of the checkbox should be the record's id
        );
    }
    function get_columns(){
        $columns = array(
            'cb'=> '<input type="checkbox" />', //选择框
			'TZ_id'=>'ID',
			'TZ_date'=>'下载时间',
			'TZ_user'=>'下载用户',
			'ip'=>'IP地址',
			'TZ_post'=>'所在文章',
			'TZ_index'=>'几号下载框',
			'TZ_post_i'=>'几号资源',
			'TZ_post_i_file_name'=>'资源名称',
			'TZ_post_i_file_url'=>'资源链接',
			'TZ_post_i_file_tq'=>'提取码',
            'TZ_post_i_file_jy'=>'解压码',
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
            $table_name = $wpdb->prefix . 'TZ_download';
            if(is_array($_GET['tz_id'])){
                foreach ($_GET['tz_id'] as $key => $value) {
                    $wpdb->delete( $table_name, array( 'TZ_id' => $value ) );
                }
            }else{
                $wpdb->delete( $table_name, array( 'TZ_id' => $_GET['tz_id'] ) );
            }
        }
        
    }
    function filter_data($data){
    	$url = home_url('/wp-admin/admin.php?page=b2_download_control');
        foreach($data as $key => $val){
			$post_title = get_the_title($val['TZ_post']);
			$data[$key]['TZ_index'] =  $val['TZ_index'] ? '第'.($val['TZ_index']).'个' : '未记录';
			$data[$key]['TZ_post_i'] = $val['TZ_post_i'] ? '第'.($val['TZ_post_i']).'个' : '未记录';
			$data[$key]['TZ_post'] = '<a href="'.$url.'&post='.$val['TZ_post'].'">'.$post_title.'</a>';
			if($val['TZ_user']==0){
				$data[$key]['TZ_user'] = '游客';
			}elseif(!isset($_GET['user'])){
	            $TZ_user = User::get_user_public_data($val['TZ_user']);
	            $data[$key]['TZ_user'] = '<a href="'.$url.'&user='.$val['TZ_user'].'">'.$TZ_user['name'].'</a>';				
			}else{
	            $TZ_user = User::get_user_public_data($val['TZ_user']);
	            $data[$key]['TZ_user'] = '<a href="'.$TZ_user['link'].'" target="_blank">'.$TZ_user['name'].'</a>';	
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
		
		$where = '';
		if(isset($_GET['user'])){
			$where = 'where TZ_user = '.intval($_GET['user']);
		}
		if(isset($_GET['post'])){
			$where = 'where TZ_post = '.intval($_GET['post']);
		}
        global $wpdb;
        $order = 'ORDER BY TZ_id DESC';
        $table_name = $wpdb->prefix . 'TZ_download';
        $per_page = 10;
        $limit = $per_page;
        $paged = $current_page;
        $offset = ($paged-1)*$per_page;
        $pages = $wpdb->get_var( "SELECT count(*) FROM $table_name $where");
        $cards = $wpdb->get_results( "SELECT * FROM $table_name $where $order LIMIT $offset,$limit" ,ARRAY_A );
        $cards = $this->filter_data($cards);

        //排序方式
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'tz_id';
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