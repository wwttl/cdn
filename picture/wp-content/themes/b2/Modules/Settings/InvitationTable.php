<?php 
namespace B2\Modules\Settings;

use \WP_List_Table;

if( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

//邀请码表格
class InvitationTable extends WP_List_Table {

    function __construct() {
        global $status, $page;

        parent::__construct(array(
            'singular' => 'id',
            'ajax' => false  
        ));
    }

    function column_default($item, $column_name) {
        switch ($column_name) {
            case 'invitation_owner':
                if($item->$column_name){
                    return b2_get_userdata($item->$column_name,'link');
                }
                return __('未指定','b2');
            case 'invitation_user':
                if($item->$column_name){
                    return b2_get_userdata($item->$column_name,'link');
                }
                return __('未指定','b2');
            case 'invitation_status':
                return $item->$column_name == 0 ? '<span style="color:green">'.__('未使用','b2').'</span>' : '<span style="color:red">'.__('已使用','b2').'</span>';
            case 'invitation_credit':
                return $item->$column_name;
            default:
            return $item->$column_name;
        }
    }
    
    function get_status_count($status){
        global $wpdb;
        $table_name = $wpdb->prefix . 'zrz_invitation';
        if($status === 'all'){
            $rowcount = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        }else{
            $rowcount = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE invitation_status = $status");
        }
        
        return $rowcount ? $rowcount : 0;
    }

    function delete_coupons($ids){
        global $wpdb;
        $table_name = $wpdb->prefix . 'zrz_invitation';

        if(is_array($ids)){
            foreach ($ids as $id) {
                $wpdb->query(
                    $wpdb->prepare( 
                        "DELETE FROM $table_name WHERE id = %d",
                        $id
                    )
                );
            }
        }
    }

    function column_id($item){

        $actions = array(
            'delete'    => sprintf('<a href="?page=%s&action=%s&id=%s'.(isset($_REQUEST['paged']) ? '&paged="%s"' : '').'">'.__('删除','b2').'</a>','b2_invitation_list','delete',$item->id,isset($_REQUEST['paged']) ? $_REQUEST['paged'] : 1),
        );

        return sprintf('%1$s %2$s',
            $item->id,
            $this->row_actions($actions)
        );
    }

    function column_cb($item){

        return sprintf(
            '<input type="checkbox" name="id[]" value="%1$s" />',
            $item->id
        );
    }

    function get_columns() {
        return $columns = array(
            'cb' => '<input type="checkbox" />',
            'id' => __('ID','b2'),
            'invitation_nub' => __('邀请码','b2'),
            'invitation_owner' => __('创建人','b2'),
            'invitation_status' => __('邀请码状态','b2'),
            'invitation_user'=>__('使用者','b2'),
            'invitation_credit'=>__('奖励值','b2'),
        );
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'id' => array('id',false),
            'invitation_nub' => array('invitation_nub',false),
            'invitation_owner' => array('invitation_owner',false),
            'invitation_status' => array('invitation_status',false),
            'invitation_user'=>array('invitation_user',false),
            'invitation_credit'=>array('invitation_credit',false),
        );
        return $sortable_columns;
    }

    function display_tablenav( $which ) {

        ?>
        <div class="tablenav <?php echo esc_attr( $which ); ?>">

            <?php if ( $this->has_items() ): ?>
                <div class="alignleft actions bulkactions">
                    <?php $this->bulk_actions( $which ); ?>
                </div>
            <?php endif;
                $this->extra_tablenav( $which );
                $this->pagination( $which );
            ?>

            <br class="clear" />
        </div>
        <?php
    }

    function get_bulk_actions() {
        $actions = array(
            'delete'    => __('删除','ziranzhi2')
        );
        return $actions;
    }

    function prepare_items($val ='') {

        $this->process_bulk_action();

        global $wpdb; 
        $table_name = $wpdb->prefix . 'zrz_invitation';

        $query = "SELECT * FROM $table_name";

        //搜索
        $s = isset($_GET["s"]) ? esc_sql($_GET["s"]) : '';
        if(!empty($s)){
            $query.= $wpdb->prepare("
                WHERE invitation_nub LIKE %s
                OR invitation_owner = %d
                OR invitation_user = %d
                OR invitation_credit = %s
                ",
                $s, $s, $s,$s
            );
        }

        //状态筛选
        $status = isset($_GET["invitation_status"]) ? esc_sql($_GET["invitation_status"]) : '';
        if ($status !== '' && $status != 'all') {
            $query.=" WHERE `invitation_status` = $status";
        }

        //排序
        $orderby = isset($_GET["orderby"]) ? esc_sql($_GET["orderby"]) : 'id';
        $order = isset($_GET["order"]) ? esc_sql($_GET["order"]) : 'DESC';
        if (!empty($orderby) & !empty($order)) {
            $query.=' ORDER BY ' . $orderby . ' ' . $order;
        }

        $totalitems = $wpdb->query($query);

        $perpage = 20;

        $paged = isset($_GET["paged"]) ? esc_sql($_GET["paged"]) : '';

        if (empty($paged) || !is_numeric($paged) || $paged <= 0) {
            $paged = 1;
        }

        $totalpages = ceil($totalitems / $perpage);

        if (!empty($paged) && !empty($perpage)) {
            $offset = ($paged - 1) * $perpage;
            $query.=' LIMIT ' . (int) $offset . ',' . (int) $perpage;
        }

        $this->set_pagination_args(array(
            "total_items" => $totalitems,
            "total_pages" => $totalpages,
            "per_page" => $perpage,
        ));

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);

        $this->items = $wpdb->get_results($query);
    }
}