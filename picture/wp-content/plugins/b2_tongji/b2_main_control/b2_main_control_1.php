<?php
add_action('cmb2_admin_init','add_tz_main_control_t_page','50');
function add_tz_main_control_t_page(){
    $login = new_cmb2_box(array(
        'id'           => 'b2_tz_main_control_2',
        'object_types' => array( 'options-page' ),
        'option_key'   => 'b2_tz_main_control_2',
        'tab_group'    => 'b2_tz_main_options',
        'parent_slug'  => '/admin.php?page=b2_tz_main_control',
        'tab_title'    => __('用户分组','b2'),
        'menu_title'   => __('用户分组','b2'),
        'display_cb'   => 'tz_main_function_2',
    ));
}
function tz_main_function_2($cmb_options) {
  $tabs = tj_cb_options_page_tabs( $cmb_options );
  $TZ_control = new TZ_control();
  $user_lv = $TZ_control->tz_get_user_lv();
?>
    <link href="<?php echo B2_TJ_URL.'assets/bootstrap.css';?>" rel="stylesheet">
    <style type="text/css">
		#wpwrap {
			    background: #eee;
		}
        .wrap h1.wp-heading-inline {
            color: #20aee3;
        }
        .bg{
            background-color: #fff;
        }
        .inner-card-icon{
  			width: 62px;
  			height: 62px;
  			font-size: 25px;
  			color: #ffffff;
  			display: flex;
        }
        .bor{
        	 border-right: solid 1px #e8ecf1;
        }
        .downnum{
          background: #86ceeb;
          color: white;
          padding: 5px;
          border-radius: 3px;
          margin: 10px;
        }
    </style>
<div class="wrap option-<?php echo $cmb_options->option_key; ?>">
		<h2>B2用户中心</h2>
	    <h2 class="nav-tab-wrapper">
	        <?php foreach ( $tabs as $option_key => $tab_title ) : ?>
	            <a class="nav-tab<?php if ( isset( $_GET['page'] ) && $option_key === $_GET['page'] ) : ?> nav-tab-active<?php endif; ?>" href="<?php menu_page_url( $option_key ); ?>"><?php echo wp_kses_post( $tab_title ); ?></a>
	        <?php endforeach; ?>
	    </h2>


        <div class="container row mt-3">
        	
        
        <!--<link href="<?php echo B2_TJ_URL.'assets/Chart.min.css';?>" rel="stylesheet">-->
        <!--<script src="<?php echo B2_TJ_URL.'assets/Chart.min.js';?>"></script>-->


          <div class="col-12">
            <div class="bg shadow p-2">
            
            <?php
            echo '<h3>VIP</h3>';
            foreach ($user_lv['vip'] as $key => $value) {
            	echo $value['name'].'：'.$value['num'].'<br>';
            }
            echo '<h3>LV</h3>';
            foreach ($user_lv['lv'] as $key => $value) {
            	echo $value['name'].'：'.$value['num'].'<br>';
            }
            ?>
                <!--<canvas id="myChart_vip"></canvas>-->
            </div>
            <!--<script>
var ctx = document.getElementById('myChart_vip').getContext('2d');
var myChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: [<?php
            foreach ($user_lv['vip'] as $key => $value) {echo "'".$value['name']."'".",";}
            foreach ($user_lv['lv'] as $key => $value) {echo "'".$value['name']."'".",";}
            ?>],
        datasets: [
            {
                label:['VIP'],
                data: [<?php
                foreach ($user_lv['vip'] as $key => $value) {echo $value['num'].',';}
                foreach ($user_lv['lv'] as $key => $value) {echo '0'.',';}
                ?>],
                backgroundColor:[<?php
                foreach ($user_lv['vip'] as $key => $value) {echo "'".$value['color']."'".",";}
                foreach ($user_lv['lv'] as $key => $value) {echo "'".$value['color']."'".",";}
                ?>],
            },
            {
                label: ['LV'],
                data: [<?php
                foreach ($user_lv['vip'] as $key => $value) {echo '0'.',';}
                foreach ($user_lv['lv'] as $key => $value) {echo $value['num'].',';}
                ?>],
                backgroundColor:[<?php
                foreach ($user_lv['vip'] as $key => $value) {echo "'".$value['color']."'".",";}
                foreach ($user_lv['lv'] as $key => $value) {echo "'".$value['color']."'".",";}
                ?>],
            }
        ]
    }
});

</script>-->
          </div>
        </div>


    </div>
<?php 
}