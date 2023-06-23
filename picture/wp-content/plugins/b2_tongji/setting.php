<?php
add_action('cmb2_admin_init',function(){
    $setting = new_cmb2_box(array(
        'id'           => 'b2_tongji_options',
        'object_types' => array( 'options-page' ),
        'option_key'      => 'b2_tongji_options',
        'tab_group'    => 'b2_tongji_options',
        'parent_slug'     => 'b2_tz_main_control',
        'tab_title'    => __('插件设置', 'b2'),
        'menu_title'   => __('插件设置', 'b2')
    ));
    $setting->add_field(array(
        'name'       => esc_html__('是否开启搜索统计', 'cmb2'),
        'desc'       => esc_html__('是否开启搜索统计', 'cmb2'),
        'id'         => 'search',
        'type'       => 'checkbox',
    ));
    $setting->add_field(array(
        'name'       => esc_html__('是否开启汇率换算统计', 'cmb2'),
        'desc'       => esc_html__('是否开启汇率换算统计,开启后全站统一用paypal开启后设置的汇率进行输出', 'cmb2'),
        'id'         => 'rate',
        'type'       => 'checkbox',
    ));
    $setting->add_field(array(
        'name'       => esc_html__('是否开启签到管理', 'cmb2'),
        'desc'       => esc_html__('是否开启签到管理', 'cmb2'),
        'id'         => 'qiandao',
        'type'       => 'checkbox',
    ));
    $setting->add_field(array(
        'name'       => esc_html__('彻底删除插件', 'cmb2'),
        'desc'       => esc_html__('是否在删除插件时不保留数据', 'cmb2'),
        'id'         => 'delete',
        'type'       => 'checkbox',
    ));
    $setting->add_field(array(
        'name'       => esc_html__('接口密码', 'cmb2'),
        'desc'       => esc_html__('接口请求数据所需的密码', 'cmb2'),
        'id'         => 'apisec',
        'type'       => 'text',
    ));
},99);