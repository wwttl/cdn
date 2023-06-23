<?php
use B2\Modules\Common\User;

add_action('cmb2_admin_init','add_dailysign_control_page','50');
function add_dailysign_control_page(){
    $login = new_cmb2_box(array(
        'id'           => 'b2_dailysign_control',
        'object_types' => array( 'options-page' ),
        'option_key'   => 'b2_dailysign_control',
        'tab_group'    => 'b2_dailysign_options',
        'parent_slug'  => 'b2_tz_main_control',
        'tab_title'    => __('统计','b2'),
        'menu_title'   => __('签到管理','b2'),
        'display_cb'   => 'dailysign_function'
    ));
}
function dailysign_function() {
    $cardlisttable = new B2_dailysign_List_Table();
    $cardlisttable->prepare_items();
?>
  <div class="wrap">
	  <h1 class="wp-heading-inline">签到管理</h1>
	  <form id="movies-filter" method="get">
	    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
	    <?php 
	    	$cardlisttable->display() ?>
	  </form>
  </div>
<?php 
}

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class B2_dailysign_List_Table extends WP_List_Table {

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
            case 'user':
            case 'get':
            case 'total':
            case 'date':
            case 'always':
                return $item[$column_name];
            default:
                return print_r($item,true);
        }
    }

    //编辑按钮
    function column_ID($item){
        //Build row actions
        $actions = array(
            //'delete'    => sprintf('<a href="?page=%s&action=%s&id=%s">删除</a>',$_REQUEST['page'],'delete',$item['id']),
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
            'user'=>'用户',
            'date'=>'签到时间',
            'always'=>'连续签到',
            'get'=>'增加积分',
            'total'=>'积分总数',
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
            //'delete'    => '删除'
        );
        return $actions;
    }
    function process_bulk_action() {
        if( 'delete'===$this->current_action() ) {
            global $wpdb; 
            $table_name = $wpdb->prefix . 'zrz_message';
            if(is_array($_GET['id'])){
                foreach ($_GET['id'] as $key => $value) {
                    $wpdb->delete( $table_name, array( 'msg_id' => $value ) );
                }
            }else{
                $wpdb->delete( $table_name, array( 'msg_id' => $_GET['id'] ) );
            }
        }
        
    }
    function filter_data($data){
        foreach($data as $key => $val){
            $user = User::get_user_public_data($val['user_id']);
            $data[$key]['user'] = '<a href="'.$user['link'].'" target="_blank">'.$user['name'].'</a>';
            $data[$key]['get'] = $val['msg_credit'];
            $data[$key]['total'] = $val['msg_credit_total'];
            $data[$key]['date'] = $val['msg_date'];
            $data[$key]['id'] = $val['msg_id'];
            $data[$key]['always'] = User::get_user_mission_data($val['user_id'])['always'].'天';
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

        global $wpdb;
        $order = 'ORDER BY msg_id DESC';
        $table_name = $wpdb->prefix . 'zrz_message';
        $where = 'where msg_type=16';
        $per_page = 30;
        $limit = $per_page;
        $paged = $current_page;
        $offset = ($paged-1)*$per_page;
        $pages = $wpdb->get_var( "SELECT count(*) FROM $table_name $where $order");
        $cards = $wpdb->get_results( "SELECT * FROM $table_name $where $order LIMIT $offset,$limit" ,ARRAY_A );
        $cards = $this->filter_data($cards);

        //排序方式
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'msg_id';
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order==='asc') ? -$result : $result;
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
