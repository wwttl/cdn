<?php
function tj_cb_options_page_tabs( $cmb_options ) {
    $tab_group = $cmb_options->cmb->prop( 'tab_group' );
    $tabs      = array();
    foreach ( \CMB2_Boxes::get_all() as $cmb_id => $cmb ) {
        if ( $tab_group === $cmb->prop( 'tab_group' ) ) {
            $tabs[ $cmb->options_page_keys()[0] ] = $cmb->prop( 'tab_title' )
                ? $cmb->prop( 'tab_title' )
                : $cmb->prop( 'title' );
        }
    }
    return $tabs;
}
add_action('cmb2_admin_init','add_tz_main_control_page','1');
function add_tz_main_control_page(){

    $login = new_cmb2_box(array(
        'id'           => 'b2_tz_main_control',
        'title'	       =>	__('B2统计中心','b2'),
        'icon_url'	   =>	'dashicons-admin-generic',
        'object_types' => array( 'options-page' ),
        'option_key'   => 'b2_tz_main_control',
        'show_on'	   =>	array(
            'options-page'	=>'b2_tz_main_control',
        ),
        'tab_group'    => 'b2_tz_main_options',
        'tab_title'    => __('网站信息','b2'),
        'menu_title'   => __('B2统计中心','b2'),
        'display_cb'   => 'tz_main_function'
    ));
}
function tz_main_function($cmb_options) {
  $tabs = tj_cb_options_page_tabs( $cmb_options );
  $TZ_control = new TZ_control();
  $new_user = $TZ_control->tz_get_user_num();
  $user_sign = $TZ_control->tz_get_user_sign_num();
  $comment = $TZ_control->tz_get_user_comment_num();
  $post = $TZ_control->tz_get_user_post_num();
  $circle = $TZ_control->tz_get_user_circle_num();
  $user_download = $TZ_control->tz_get_user_download_num();
  $user_vip_buy =  $TZ_control->tz_get_user_vip_buy_num();
  $user_vip_buy_money =  $TZ_control->tz_get_user_buy_vip_money();
  $user_vip_buy_cz =  $TZ_control->tz_get_user_cz_money();
  $user_vip_buy_down =  $TZ_control->tz_get_user_down_money();
  $user_vip_cz_credit =  $TZ_control->tz_get_user_cz_credit();
  $user_vip_w_money =  $TZ_control->tz_get_user_w_money();
  $user_card =  $TZ_control->tz_get_card_num();
  $jinrishouru = $TZ_control->tz_get_today_money();
  $this_month = $TZ_control->tz_get_month_money();
  $last_month = $TZ_control->tz_get_last_money();
  $daily_login = get_option('tj_user_login_num');
  $user_ds = $TZ_control->tz_get_user_ds_money();
  $invitecode_state = $TZ_control->tz_get_invitecode_state();
  $today_paypal = $TZ_control->tz_get_today_money_paypal();
?>

	<script src="<?php echo B2_TJ_URL.'assets/vue.js';?>"></script>
	
	<link href="<?php echo B2_TJ_URL.'assets/vuetify.min.css';?>" rel="stylesheet">
	<script src="<?php echo B2_TJ_URL.'assets/vuetify.min.js';?>"></script>

	<link href="<?php echo B2_TJ_URL.'assets/Chart.min.css';?>" rel="stylesheet">
	<script src="<?php echo B2_TJ_URL.'assets/Chart.min.js';?>"></script>
    
    <style>
    	#wpwrap{
    		background: #f0f2f5;	
    	}
    </style>
<div class="wrap option-<?php echo $cmb_options->option_key; ?>">
    <?php if ( get_admin_page_title() ) : ?>
        <h2><?php echo wp_kses_post( get_admin_page_title() ); ?></h2>
    <?php endif; ?>
    <h2 class="nav-tab-wrapper">
        <?php foreach ( $tabs as $option_key => $tab_title ) : ?>
            <a class="nav-tab<?php if ( isset( $_GET['page'] ) && $option_key === $_GET['page'] ) : ?> nav-tab-active<?php endif; ?>" href="<?php menu_page_url( $option_key ); ?>"><?php echo wp_kses_post( $tab_title ); ?></a>
        <?php endforeach; ?>
    </h2>

	<div id="tj_ana">
		  
		<v-container class="grey lighten-5">
			<v-row>
			  <v-col cols="12" sm="3">
			  	<div style="background: white;padding: 20px 24px 8px;">
					<span style="color: rgba(0,0,0,.45);font-size: 14px;line-height: 22px;"><a href="<?php echo admin_url('admin.php?page=b2_orders_list');?>">今日收入</a></span>
					<span style="overflow: hidden;text-overflow: ellipsis;word-break: break-all;white-space: nowrap;color: #000;margin-top: 4px;margin-bottom: 0;font-size: 30px;line-height: 38px;height: 38px;display: block;"><?php echo tj_money_type(); ?><?php echo $jinrishouru[0];?><?php if( ((int)tj_get_option('b2_normal_pay','paypal_open') === 1) && !tj_get_option('b2_tongji_options','rate')){echo '（'.tj_get_option('b2_normal_pay','paypal_currency_code').' '.$today_paypal[0].'）';}?></span>
					<v-sparkline
						:value="value"
						:gradient="gradient"
						:smooth="radius || false"
						:padding="padding"
						:line-width="width"
						:stroke-linecap="lineCap"
						:gradient-direction="gradientDirection"
						:fill="fill"
						:type="type"
						:auto-line-width="autoLineWidth"
						auto-draw
					></v-sparkline>
					<v-divider></v-divider>
					<v-row>
				        <v-col cols="auto" class="mr-auto"><span style="color: rgba(0,0,0,.45);font-size: 14px;line-height: 22px;">本月收入：<?php echo tj_money_type(); ?><?php echo $this_month; ?></span></v-col>
				        <v-col cols="auto"><span style="color: rgba(0,0,0,.45);font-size: 14px;line-height: 22px;">上月收入：<?php echo tj_money_type(); ?><?php echo $last_month; ?></span></v-col>
					</v-row>
			  	</div>
			  </v-col>
			  <v-col cols="12" sm="3">
			  	<div style="background: white;padding: 20px 24px 8px;">
					<span style="color: rgba(0,0,0,.45);font-size: 14px;line-height: 22px;"><a href="<?php echo admin_url('/users.php?tj_filter%5B0%5D=today_sign');?>">今日注册</a></span>
					<span style="overflow: hidden;text-overflow: ellipsis;word-break: break-all;white-space: nowrap;color: #000;margin-top: 4px;margin-bottom: 0;font-size: 30px;line-height: 38px;height: 38px;display: block;"><?php echo $new_user[0]; ?></span>
					<v-sparkline
						:value="value2"
						:gradient="gradient2"
						:smooth="radius || false"
						:padding="padding"
						:line-width="width"
						:stroke-linecap="lineCap"
						:gradient-direction="gradientDirection"
						:fill="fill"
						:type="type"
						:auto-line-width="autoLineWidth"
						auto-draw
					></v-sparkline>
					<v-divider></v-divider>
					<v-row>
				        <v-col cols="auto" class="mr-auto"><span style="color: rgba(0,0,0,.45);font-size: 14px;line-height: 22px;">昨天注册：<?php echo $new_user[1]; ?></span></v-col>
				        <v-col cols="auto"><span style="color: rgba(0,0,0,.45);font-size: 14px;line-height: 22px;">  </span></v-col>
					</v-row>
			  	</div>
			  </v-col>
			  <v-col cols="12" sm="3">
			  	<div style="background: white;padding: 20px 24px 8px;">
					<span style="color: rgba(0,0,0,.45);font-size: 14px;line-height: 22px;"><a href="<?php echo admin_url('/users.php?tj_filter%5B0%5D=today_login');?>">今日登录</a></span>
					<span style="overflow: hidden;text-overflow: ellipsis;word-break: break-all;white-space: nowrap;color: #000;margin-top: 4px;margin-bottom: 0;font-size: 30px;line-height: 38px;height: 38px;display: block;"><?php echo isset($daily_login['a'.strtotime("today")]) ? $daily_login['a'.strtotime("today")] : 0; ?></h2></span>
					<v-sparkline
						:value="value3"
						:gradient="gradient3"
						:smooth="radius || false"
						:padding="padding"
						:line-width="width"
						:stroke-linecap="lineCap"
						:gradient-direction="gradientDirection"
						:fill="fill"
						:type="type"
						:auto-line-width="autoLineWidth"
						auto-draw
					></v-sparkline>
					<v-divider></v-divider>
					<v-row>
				        <v-col cols="auto" class="mr-auto"><span style="color: rgba(0,0,0,.45);font-size: 14px;line-height: 22px;">昨天登录：<?php echo isset($daily_login['a'.(strtotime("today")-1*86400)]) ? $daily_login['a'.(strtotime("today")-1*86400)] : 0; ?></span></v-col>
				        <v-col cols="auto"><span style="color: rgba(0,0,0,.45);font-size: 14px;line-height: 22px;"> </span></v-col>
					</v-row>
			  	</div>
			  </v-col>
			  <v-col cols="12" sm="3">
			  	<div style="background: white;padding: 20px 24px 8px;">
					<span style="color: rgba(0,0,0,.45);font-size: 14px;line-height: 22px;">今日签到</span>
					<span style="overflow: hidden;text-overflow: ellipsis;word-break: break-all;white-space: nowrap;color: #000;margin-top: 4px;margin-bottom: 0;font-size: 30px;line-height: 38px;height: 38px;display: block;"><?php echo $user_sign[0]; ?></h2></span>
					<v-sparkline
						:value="value4"
						:gradient="gradient4"
						:smooth="radius || false"
						:padding="padding"
						:line-width="width"
						:stroke-linecap="lineCap"
						:gradient-direction="gradientDirection"
						:fill="fill"
						:type="type"
						:auto-line-width="autoLineWidth"
						auto-draw
					></v-sparkline>
					<v-divider></v-divider>
					<v-row>
				        <v-col cols="auto" class="mr-auto"><span style="color: rgba(0,0,0,.45);font-size: 14px;line-height: 22px;">昨天签到：<?php echo $user_sign[1]; ?></span></v-col>
				        <v-col cols="auto"><span style="color: rgba(0,0,0,.45);font-size: 14px;line-height: 22px;"> </span></v-col>
					</v-row>
			  	</div>
			  </v-col>
			</v-row>
			<v-row>
			  <v-col cols="12" sm="6">
				<div style="background: white; padding: 20px 24px 8px;"><canvas id="myChart_money" ></canvas></div>
			  </v-col>
			  <v-col cols="12" sm="6">
				<div style="background: white; padding: 20px 24px 8px;"><canvas id="myChart" ></canvas></div>
			  </v-col>
			  <v-col cols="12" sm="6">
				<div style="background: white; padding: 20px 24px 8px;"><canvas id="myChart_second" ></canvas></div>
			  </v-col>
			</v-row>
		</v-container>
			
	</div>

    <script>
      const gradients = [
	    ['#222'],
	    ['#42b3f4'],
	    ['red', 'orange', 'yellow'],
	    ['purple', 'violet'],
	    ['#00c6ff', '#F0F', '#FF0'],
	    ['#f72047', '#ffd200', '#1feaea'],
	  ]
    
    	var b2tj_ana = new Vue({
    		el:'#tj_ana',
		    data(){
		        return {
					width: 2,
					radius: 10,
					padding: 0,
					lineCap: 'round',
					gradient: gradients[5],
					gradient2: gradients[4],
					gradient3: gradients[3],
					gradient4: gradients[2],
					value: [
						<?php echo $jinrishouru[6]; ?>,
                        <?php echo $jinrishouru[5]; ?>,
                        <?php echo $jinrishouru[4]; ?>,
                        <?php echo $jinrishouru[3]; ?>,
                        <?php echo $jinrishouru[2]; ?>,
                        <?php echo $jinrishouru[1]; ?>,
                        <?php echo $jinrishouru[0]; ?>,
                    ],
                    value2: [
						<?php echo $new_user[6]; ?>,
                        <?php echo $new_user[5]; ?>,
                        <?php echo $new_user[4]; ?>,
                        <?php echo $new_user[3]; ?>,
                        <?php echo $new_user[2]; ?>,
                        <?php echo $new_user[1]; ?>,
                        <?php echo $new_user[0]; ?>,
                    ],
                    value3: [
                		<?php echo isset($daily_login['a'.(strtotime("today")-6*86400)]) ? $daily_login['a'.(strtotime("today")-6*86400)] : 0; ?>,
					    <?php echo isset($daily_login['a'.(strtotime("today")-5*86400)]) ? $daily_login['a'.(strtotime("today")-5*86400)] : 0; ?>,
					    <?php echo isset($daily_login['a'.(strtotime("today")-4*86400)]) ? $daily_login['a'.(strtotime("today")-4*86400)] : 0; ?>,
					    <?php echo isset($daily_login['a'.(strtotime("today")-3*86400)]) ? $daily_login['a'.(strtotime("today")-3*86400)] : 0; ?>,
					    <?php echo isset($daily_login['a'.(strtotime("today")-2*86400)]) ? $daily_login['a'.(strtotime("today")-2*86400)] : 0; ?>,
					    <?php echo isset($daily_login['a'.(strtotime("today")-1*86400)]) ? $daily_login['a'.(strtotime("today")-1*86400)] : 0; ?>,
					    <?php echo isset($daily_login['a'.(strtotime("today")-0*86400)]) ? $daily_login['a'.(strtotime("today")-0*86400)] : 0; ?>,
                    ],
                    value4: [
                    	<?php echo $user_sign[6]; ?>,
                    	<?php echo $user_sign[5]; ?>,
                    	<?php echo $user_sign[4]; ?>,
                    	<?php echo $user_sign[3]; ?>,
                    	<?php echo $user_sign[2]; ?>,
                    	<?php echo $user_sign[1]; ?>,
                    	<?php echo $user_sign[0]; ?>,
                    ],
					gradientDirection: 'top',
					gradients,
					fill: false,
					type: 'trend',
					autoLineWidth: true,
		        }
		    },
    	})
    	

    </script>

    <script>
      var ctx = document.getElementById('myChart_money').getContext('2d');
      var myChart = new Chart(ctx, {
          type: 'line',
          data: {
              labels: [
              '<?php echo date("Y/m/d",strtotime("-6 day")); ?>',
              '<?php echo date("Y/m/d",strtotime("-5 day")); ?>',
              '<?php echo date("Y/m/d",strtotime("-4 day")); ?>',
              '<?php echo date("Y/m/d",strtotime("-3 day")); ?>',
              '<?php echo date("Y/m/d",strtotime("-2 day")); ?>',
              '<?php echo date("Y/m/d",strtotime("-1 day")); ?>',
              '<?php echo date("Y/m/d",strtotime("today"));  ?>'],
              datasets: [
              {
                  label: '收入',
                  data: [
                    <?php echo $jinrishouru[6]; ?>,
                    <?php echo $jinrishouru[5]; ?>,
                    <?php echo $jinrishouru[4]; ?>,
                    <?php echo $jinrishouru[3]; ?>,
                    <?php echo $jinrishouru[2]; ?>,
                    <?php echo $jinrishouru[1]; ?>,
                    <?php echo $jinrishouru[0]; ?>,
                  ],
                  borderColor:'blue',
                  backgroundColor:'skyBlue',
                  borderWidth: 1,
                  fill: false,
              },
             {
                  label: 'VIP收入',
                  data: [
                    <?php echo round(($user_vip_buy_money[6] ? $user_vip_buy_money[6] : 0),2); ?>,
                    <?php echo round(($user_vip_buy_money[5] ? $user_vip_buy_money[5] : 0),2); ?>,
                    <?php echo round(($user_vip_buy_money[4] ? $user_vip_buy_money[4] : 0),2); ?>,
                    <?php echo round(($user_vip_buy_money[3] ? $user_vip_buy_money[3] : 0),2); ?>,
                    <?php echo round(($user_vip_buy_money[2] ? $user_vip_buy_money[2] : 0),2); ?>,
                    <?php echo round(($user_vip_buy_money[1] ? $user_vip_buy_money[1] : 0),2); ?>,
                    <?php echo round(($user_vip_buy_money[0] ? $user_vip_buy_money[0] : 0),2); ?>,
                  ],
                  borderColor:'red',
                  backgroundColor:'pink',
                  borderWidth: 1,
                  fill: false,
              },
              {
                  label: '余额充值',
                  data: [
                    <?php echo round(($user_vip_buy_cz[6] ? $user_vip_buy_cz[6] : 0),2); ?>,
                    <?php echo round(($user_vip_buy_cz[5] ? $user_vip_buy_cz[5] : 0),2); ?>,
                    <?php echo round(($user_vip_buy_cz[4] ? $user_vip_buy_cz[4] : 0),2); ?>,
                    <?php echo round(($user_vip_buy_cz[3] ? $user_vip_buy_cz[3] : 0),2); ?>,
                    <?php echo round(($user_vip_buy_cz[2] ? $user_vip_buy_cz[2] : 0),2); ?>,
                    <?php echo round(($user_vip_buy_cz[1] ? $user_vip_buy_cz[1] : 0),2); ?>,
                    <?php echo round(($user_vip_buy_cz[0] ? $user_vip_buy_cz[0] : 0),2); ?>,
                  ],
                  borderColor:'blue',
                  backgroundColor:'skyBlue',
                  borderWidth: 1,
                  fill: false,
              },
              {
                  label: '下载收入',
                  data: [
                    <?php echo round(($user_vip_buy_down[6] ? $user_vip_buy_down[6] : 0),2); ?>,
                    <?php echo round(($user_vip_buy_down[5] ? $user_vip_buy_down[5] : 0),2); ?>,
                    <?php echo round(($user_vip_buy_down[4] ? $user_vip_buy_down[4] : 0),2); ?>,
                    <?php echo round(($user_vip_buy_down[3] ? $user_vip_buy_down[3] : 0),2); ?>,
                    <?php echo round(($user_vip_buy_down[2] ? $user_vip_buy_down[2] : 0),2); ?>,
                    <?php echo round(($user_vip_buy_down[1] ? $user_vip_buy_down[1] : 0),2); ?>,
                    <?php echo round(($user_vip_buy_down[0] ? $user_vip_buy_down[0] : 0),2); ?>,
                  ],
                  borderColor:'green',
                  backgroundColor:'LawnGreen',
                  borderWidth: 1,
                  fill: false,
              },
              {
                  label: '积分充值收入',
                  data: [
                    <?php echo round(($user_vip_cz_credit[6] ? $user_vip_cz_credit[6] : 0),2); ?>,
                    <?php echo round(($user_vip_cz_credit[5] ? $user_vip_cz_credit[5] : 0),2); ?>,
                    <?php echo round(($user_vip_cz_credit[4] ? $user_vip_cz_credit[4] : 0),2); ?>,
                    <?php echo round(($user_vip_cz_credit[3] ? $user_vip_cz_credit[3] : 0),2); ?>,
                    <?php echo round(($user_vip_cz_credit[2] ? $user_vip_cz_credit[2] : 0),2); ?>,
                    <?php echo round(($user_vip_cz_credit[1] ? $user_vip_cz_credit[1] : 0),2); ?>,
                    <?php echo round(($user_vip_cz_credit[0] ? $user_vip_cz_credit[0] : 0),2); ?>,
                  ],
                  borderColor:'orange',
                  backgroundColor:'orange',
                  borderWidth: 1,
                  fill: false,
              },
              {
                  label: '文章隐藏',
                  data: [
                    <?php echo round(($user_vip_w_money[6] ? $user_vip_w_money[6] : 0),2); ?>,
                    <?php echo round(($user_vip_w_money[5] ? $user_vip_w_money[5] : 0),2); ?>,
                    <?php echo round(($user_vip_w_money[4] ? $user_vip_w_money[4] : 0),2); ?>,
                    <?php echo round(($user_vip_w_money[3] ? $user_vip_w_money[3] : 0),2); ?>,
                    <?php echo round(($user_vip_w_money[2] ? $user_vip_w_money[2] : 0),2); ?>,
                    <?php echo round(($user_vip_w_money[1] ? $user_vip_w_money[1] : 0),2); ?>,
                    <?php echo round(($user_vip_w_money[0] ? $user_vip_w_money[0] : 0),2); ?>,
                  ],
                  borderColor:'#9E9E9E',
                  backgroundColor:'#9E9E9E',
                  borderWidth: 1,
                  fill: false,
              },
              {
                  label: '卡密充值',
                  data: [
                    <?php echo round(($user_card[6] ? $user_card[6] : 0),2); ?>,
                    <?php echo round(($user_card[5] ? $user_card[5] : 0),2); ?>,
                    <?php echo round(($user_card[4] ? $user_card[4] : 0),2); ?>,
                    <?php echo round(($user_card[3] ? $user_card[3] : 0),2); ?>,
                    <?php echo round(($user_card[2] ? $user_card[2] : 0),2); ?>,
                    <?php echo round(($user_card[1] ? $user_card[1] : 0),2); ?>,
                    <?php echo round(($user_card[0] ? $user_card[0] : 0),2); ?>,
                  ],
                  borderColor:'#9C27B0',
                  backgroundColor:'#9C27B0',
                  borderWidth: 1,
                  fill: false,
              },
              {
                  label: '文章打赏',
                  data: [
                    <?php echo round(($user_ds[6] ? $user_ds[6] : 0),2); ?>,
                    <?php echo round(($user_ds[5] ? $user_ds[5] : 0),2); ?>,
                    <?php echo round(($user_ds[4] ? $user_ds[4] : 0),2); ?>,
                    <?php echo round(($user_ds[3] ? $user_ds[3] : 0),2); ?>,
                    <?php echo round(($user_ds[2] ? $user_ds[2] : 0),2); ?>,
                    <?php echo round(($user_ds[1] ? $user_ds[1] : 0),2); ?>,
                    <?php echo round(($user_ds[0] ? $user_ds[0] : 0),2); ?>,
                  ],
                  borderColor:'#9C27B0',
                  backgroundColor:'#9C27B0',
                  borderWidth: 1,
                  fill: false,
              }

              
              
        ]
          }
      });
    </script>

    <script>
      var ctx = document.getElementById('myChart').getContext('2d');
      var myChart = new Chart(ctx, {
          type: 'line',
          data: {
              labels: [
              '<?php echo date("Y/m/d",strtotime("-6 day")); ?>',
              '<?php echo date("Y/m/d",strtotime("-5 day")); ?>',
              '<?php echo date("Y/m/d",strtotime("-4 day")); ?>',
              '<?php echo date("Y/m/d",strtotime("-3 day")); ?>',
              '<?php echo date("Y/m/d",strtotime("-2 day")); ?>',
              '<?php echo date("Y/m/d",strtotime("-1 day")); ?>',
              '<?php echo date("Y/m/d",strtotime("today"));  ?>'],
              datasets: [
             {
                  label: '用户总数',
                  data: [
                    <?php echo $new_user[7]-$new_user[6]-$new_user[5]-$new_user[4]-$new_user[3]-$new_user[2]-$new_user[1]; ?>,
                    <?php echo $new_user[7]-$new_user[6]-$new_user[5]-$new_user[4]-$new_user[3]-$new_user[2]; ?>,
                    <?php echo $new_user[7]-$new_user[6]-$new_user[5]-$new_user[4]-$new_user[3]; ?>,
                    <?php echo $new_user[7]-$new_user[6]-$new_user[5]-$new_user[4]; ?>,
                    <?php echo $new_user[7]-$new_user[6]-$new_user[5]; ?>,
                    <?php echo $new_user[7]-$new_user[6]; ?>,
                    <?php echo $new_user[7]; ?>,
                  ],
                  borderColor:'blue',
                  backgroundColor:'skyBlue',
                  borderWidth: 1,
                  fill: false,
       
              },
              {
                  label: '每日登录',
                  data: [
				    <?php echo isset($daily_login['a'.(strtotime("today")-6*86400)]) ? $daily_login['a'.(strtotime("today")-6*86400)] : 0; ?>,
				    <?php echo isset($daily_login['a'.(strtotime("today")-5*86400)]) ? $daily_login['a'.(strtotime("today")-5*86400)] : 0; ?>,
				    <?php echo isset($daily_login['a'.(strtotime("today")-4*86400)]) ? $daily_login['a'.(strtotime("today")-4*86400)] : 0; ?>,
				    <?php echo isset($daily_login['a'.(strtotime("today")-3*86400)]) ? $daily_login['a'.(strtotime("today")-3*86400)] : 0; ?>,
				    <?php echo isset($daily_login['a'.(strtotime("today")-2*86400)]) ? $daily_login['a'.(strtotime("today")-2*86400)] : 0; ?>,
				    <?php echo isset($daily_login['a'.(strtotime("today")-1*86400)]) ? $daily_login['a'.(strtotime("today")-1*86400)] : 0; ?>,
				    <?php echo isset($daily_login['a'.(strtotime("today")-0*86400)]) ? $daily_login['a'.(strtotime("today")-0*86400)] : 0; ?>,
                  ],
                  borderColor:'#9C27B0',
                  backgroundColor:'#9C27B0',
                  borderWidth: 1,
                  fill: false,
              },
              {
                  label: '邀请码使用',
                  data: [
				    <?php echo $invitecode_state[6]; ?>,
                    <?php echo $invitecode_state[5]; ?>,
                    <?php echo $invitecode_state[4]; ?>,
                    <?php echo $invitecode_state[3]; ?>,
                    <?php echo $invitecode_state[2]; ?>,
                    <?php echo $invitecode_state[1]; ?>,
                    <?php echo $invitecode_state[0]; ?>,
                  ],
                  borderColor:'green',
                  backgroundColor:'LawnGreen',
                  borderWidth: 1,
                  fill: false,
              },
             {
                  label: '签到数',
                  data: [
                    <?php echo $user_sign[6]; ?>,
                    <?php echo $user_sign[5]; ?>,
                    <?php echo $user_sign[4]; ?>,
                    <?php echo $user_sign[3]; ?>,
                    <?php echo $user_sign[2]; ?>,
                    <?php echo $user_sign[1]; ?>,
                    <?php echo $user_sign[0]; ?>,
                  ],
                  borderColor:'red',
                  backgroundColor:'pink',
                  borderWidth: 1,
                  fill: false,
              },
              <?php foreach ($user_vip_buy as $key => $value) { ?>
              {
                  label: '<?php echo $key.'购买'; ?>',
                  data: [
                    <?php echo $value[6]; ?>,
                    <?php echo $value[5]; ?>,
                    <?php echo $value[4]; ?>,
                    <?php echo $value[3]; ?>,
                    <?php echo $value[2]; ?>,
                    <?php echo $value[1]; ?>,
                    <?php echo $value[0]; ?>,
                  ],
                  borderColor:'<?php echo $value[7]; ?>',
                  backgroundColor:'<?php echo $value[7]; ?>',
                  borderWidth: 1,
                  fill: false,
              },
              <?php } ?>
        
        ]
          }
      });
    </script>

    <script>
      var ctx = document.getElementById('myChart_second').getContext('2d');
      var myChart = new Chart(ctx, {
          type: 'line',
          data: {
              labels: [
              '<?php echo date("Y/m/d",strtotime("-6 day")); ?>',
              '<?php echo date("Y/m/d",strtotime("-5 day")); ?>',
              '<?php echo date("Y/m/d",strtotime("-4 day")); ?>',
              '<?php echo date("Y/m/d",strtotime("-3 day")); ?>',
              '<?php echo date("Y/m/d",strtotime("-2 day")); ?>',
              '<?php echo date("Y/m/d",strtotime("-1 day")); ?>',
              '<?php echo date("Y/m/d",strtotime("today"));  ?>'],
              datasets: [
                {
                  label: '评论',
                  data: [
                    <?php echo $comment[6]; ?>,
                    <?php echo $comment[5]; ?>,
                    <?php echo $comment[4]; ?>,
                    <?php echo $comment[3]; ?>,
                    <?php echo $comment[2]; ?>,
                    <?php echo $comment[1]; ?>,
                    <?php echo $comment[0]; ?>,
                  ],
                  borderColor:'blue',
                  backgroundColor:'skyBlue',
                  borderWidth: 1,
                  fill: false,
                },
                {
                  label: '文章',
                  data: [
                    <?php echo $post[6]; ?>,
                    <?php echo $post[5]; ?>,
                    <?php echo $post[4]; ?>,
                    <?php echo $post[3]; ?>,
                    <?php echo $post[2]; ?>,
                    <?php echo $post[1]; ?>,
                    <?php echo $post[0]; ?>,
                  ],
                  borderColor:'red',
                  backgroundColor:'pink',
                  borderWidth: 1,
                  fill: false,
                },
                {
                  label: '圈子',
                  data: [
                    <?php echo $circle[6]; ?>,
                    <?php echo $circle[5]; ?>,
                    <?php echo $circle[4]; ?>,
                    <?php echo $circle[3]; ?>,
                    <?php echo $circle[2]; ?>,
                    <?php echo $circle[1]; ?>,
                    <?php echo $circle[0]; ?>,
                  ],
                  borderColor:'#9C27B0',
                  backgroundColor:'#9C27B0',
                  borderWidth: 1,
                  fill: false,
                },
                {
                  label: '下载量',
                  data: [
                    <?php echo $user_download[6]; ?>,
                    <?php echo $user_download[5]; ?>,
                    <?php echo $user_download[4]; ?>,
                    <?php echo $user_download[3]; ?>,
                    <?php echo $user_download[2]; ?>,
                    <?php echo $user_download[1]; ?>,
                    <?php echo $user_download[0]; ?>,
                  ],
                  borderColor:'green',
                  backgroundColor:'LawnGreen',
                  borderWidth: 1,
                  fill: false,
                }
            ]
          }
      });
    </script>

</div>
<?php 
}