<?php namespace B2\Modules\Templates\Widgets;

class RecommendedCircle extends \WP_Widget{

    //小工具slug
	protected $widget_slug = 'b2-widget-recommended-circle';

    //短代码名
	protected static $shortcode = 'b2_widget_recommended_circle';

    //CMB2
	protected $cmb2 = null;

    //默认设置
	protected static $defaults = array();
    
    //实例
	protected $_instance = array();
    
    //cmb2项目
	protected $cmb2_fields = array();

    
	public function __construct() {
		$circle_name = b2_get_option('normal_custom','custom_circle_name');
		parent::__construct(
			$this->widget_slug,
			sprintf(__( 'B2-推荐%s', 'b2' ),$circle_name),
			array(
				'classname' => $this->widget_slug,
				'customize_selective_refresh' => true,
				'description' => sprintf(__( '“推荐%s”小工具', 'b2' ),$circle_name),
			)
		);

		//默认设置项
		self::$defaults = array(
			'about_title'=>sprintf(__('推荐%s','b2'),$circle_name),
            'circle_ids'  => '',
			'show_mobile'=>0
		);

		//设置项
		$this->cmb2_fields = array(
			array(
				'name'   => __('标题','b2'),
				'id_key' => 'about_title',
				'id'     => 'about_title',
				'type'   => 'text'
            ),
            array(
				'name'   => sprintf(__('推荐%s的ID','b2'),$circle_name),
				'id_key' => 'circle_ids',
				'id'     => 'circle_ids',
				'type'   => 'textarea_small',
				'desc'=>sprintf(__('请直接填写要显示的%sID，每个ID占一行，推荐只在%s页面使用以达到最好的显示效果。','b2'),$circle_name,$circle_name)
            ),
			array(
				'name'=>__('移动端是否显示','b2'),
				'id_key'=>'show_mobile',
				'id'=>'show_mobile',
				'type'=>'radio_inline',
				'options'=>array(
					1=>__('显示','b2'),
					0=>__('隐藏','b2')
				)
			)
        );
        
        //关于我们短代码
		add_shortcode( self::$shortcode, array( __CLASS__, 'b2_widget_circle_info' ) );
	}
	
	/**
     * 刷新缓存
     *
     * @return void
     * @author Li Ruchun <lemolee@163.com>
     * @version 1.0.0
     * @since 2018
     */
	public function flush_widget_cache() {
		wp_cache_delete( $this->id, 'widget' );
	}

    /**
     * 显示小工具
     *
     * @param [type] $args
     * @param [type] $instance
     *
     * @return void
     * @author Li Ruchun <lemolee@163.com>
     * @version 1.0.0
     * @since 2018
     */
	public function widget( $args, $instance ) {
	
		echo self::get_widget( array(
			'args'     => $args,
			'instance' => $instance,
			'cache_id' => $this->id,
		) );
	}
    
    /**
     * 显示小工具短代码内容
     *
     * @param [type] $atts
     *
     * @return void
     * @author Li Ruchun <lemolee@163.com>
     * @version 1.0.0
     * @since 2018
     */
	public static function get_widget( $atts ) {

        $atts['args']['cache_id'] = $atts['cache_id'];

		//获取设置项
		$instance = shortcode_atts(
			self::$defaults,
			! empty( $atts['instance'] ) ? (array) $atts['instance'] : array(),
			self::$shortcode
		);

		$atts = shortcode_atts(
			array(
				'instance'      => array(),
				'before_widget' => '',
				'after_widget'  => '',
				'before_title'  => '',
				'after_title'   => '',
				'cache_id'      => '',
				'flush_cache'   => isset( $_GET['delete-trans'] ), 
			),
			isset( $atts['args'] ) ? (array) $atts['args'] : array(),
			self::$shortcode
        );

		// if ( empty( $atts['cache_id'] ) ) {
		// 	$atts['cache_id'] = md5( serialize( $atts ) );
        // }

		// if(B2_OPEN_CACHE){
		// 	$widget = ! $atts['flush_cache']
		// 	? wp_cache_get( $atts['cache_id'], 'widget' )
        //     : '';
		// }else{
		// 	$widget = '';
		// }

		// if(!empty($widget)) return $widget;

		$circle_name = b2_get_option('normal_custom','custom_circle_name');

		$arr = array();
		$str = trim($instance['circle_ids'], " \t\n\r\0\x0B\xC2\xA0");
		if($str){
			$arr[] = (int)get_option('b2_circle_default');

			$arg = explode(PHP_EOL, $str );
			foreach ($arg as $k => $v) {
				$arr[] = trim($v, " \t\n\r\0\x0B\xC2\xA0");
			}
		}

		$li = '';

		foreach ($arr as $k => $v) {
			$li .= '<li></li>';
		}

		$html = '
			<div class="recommended-widget">
				<ul class="recommended-circle-gujia" ref="recommendedGujia" data-ids=\''.json_encode($arr).'\'>
					'.($li === '' ? '<div class="b2-pd b2-b-t fs12">'.sprintf(__('请前往小工具中设置推荐%s','b2'),$circle_name).'</div>' : $li).'
				</ul>
				<ul v-if="data != \'\'" v-cloak>
					<li v-for="(item,index) in data" :key="item.id" :class="item.id == current ? \'picked\' : \'\'">
						<a :href="item.link" @click.stop.self="go($event,index)" class="b2-radius">
						'.b2_get_img(array(
							'src_data'=>':src="item.icon" @click.stop.self="go($event,index)"',
							'source_data'=>':srcset="item.icon_webp"',
							'class'=>array('b2-radius')
						)).'
						<span v-text="item.name" @click.stop.self="go($event,index)"></span>
						</a>
					</li>
				</ul>
				<div class="widget-mission-footer b2-color">
					<a href="'.b2_get_custom_page_url('all-circles').'" target="_blank">所有圈子</a>
				</div>
			</div>';

		// 如果 $widget 是空的， 重建缓存
		if ( empty( $widget )) {
			$widget = '';
	
			$widget .= !$instance['show_mobile'] ? str_replace('class="','class="mobile-hidden ',$atts['before_widget']) : $atts['before_widget'];
			$widget .= '<div class="b2-widget-title">';
			$widget .= $atts['before_title']. esc_attr( $instance['about_title'] ) .$atts['after_title'];
			$widget .= '</div>';
			$widget .= '<div class="b2-widget-box">'.$html.'</div>';
			$widget .= $atts['after_widget'];
			
			// if(B2_OPEN_CACHE){
			// 	wp_cache_set( $atts['cache_id'], $widget, 'widget', WEEK_IN_SECONDS );
			// }
			
		}

		return $widget;
	}
    
    /**
     * 更新小工具
     *
     * @param array $new_instance
     * @param array $old_instance
     *
     * @return void
     * @author Li Ruchun <lemolee@163.com>
     * @version 1.0.0
     * @since 2018
     */
	public function update( $new_instance, $old_instance ) {
		$this->flush_widget_cache();
		$sanitized = $this->cmb2( true )->get_sanitized_values( $new_instance );
		return $sanitized;
	}
    
    /**
     * 小工具表单
     *
     * @param array $instance
     *
     * @return void
     * @author Li Ruchun <lemolee@163.com>
     * @version 1.0.0
     * @since 2018
     */
	public function form( $instance ) {
		// 如果没有设置项使用默认
		$this->_instance = wp_parse_args( (array) $instance, self::$defaults );
		$cmb2 = $this->cmb2();
		$cmb2->object_id( $this->option_name );
		\CMB2_hookup::enqueue_cmb_css();
		\CMB2_hookup::enqueue_cmb_js();
		$cmb2->show_form();
	}
    
    /**
     * 创建实例
     *
     * @param bool $saving
     *
     * @return void
     * @author Li Ruchun <lemolee@163.com>
     * @version 1.0.0
     * @since 2018
     */
	public function cmb2( $saving = false ) {

		$cmb2 = new \CMB2( array(
			'id'      => $this->option_name .'_box', 
			'hookup'  => false,
			'show_on' => array(
				'key'   => 'options-page',
				'value' => array( $this->option_name )
			),
		), $this->option_name );
		foreach ( $this->cmb2_fields as $field ) {
			if ( ! $saving ) {
				$field['id'] = $this->get_field_name( $field['id'] );
			}
			$field['default_cb'] = array( $this, 'default_cb' );
			$cmb2->add_field( $field );
		}
		return $cmb2;
	}
	/**
	 * 设置默认项
	 *
	 * @param  array      $field_args CMB2的设置项
	 * @param  CMB2_Field $field CMB2 选项对象
	 *
	 * @return mixed      Field value.
	 */
	public function default_cb( $field_args, $field ) {
		return isset( $this->_instance[ $field->args( 'id_key' ) ] )
			? $this->_instance[ $field->args( 'id_key' ) ]
			: null;
	}
}