<?php if ( ! defined( 'ABSPATH' )  ) { die; }
if ( ! function_exists( 'lmy_get_option' ) ) {
	function lmy_get_option( $option = '', $default = null ) {
		$options = get_option( 'b2child' );
		return ( isset( $options[$option] ) ) ? $options[$option] : $default;
	}
}
$blogpath =  get_template_directory_uri() . '/img';
$imagepath =  B2_CHILD_URI . '/Inc/images/';
$imagemodular =  B2_CHILD_URI . '/Inc/images/Modular/';
$prefix = 'b2child';
CSF::createOptions( $prefix, array(
	'framework_title'         => '子主题选项',
	'framework_class'         => 'be-box',

	'menu_title'              => '子主题选项',
	'menu_slug'               => 'b2child-options',
	'menu_type'               => 'menu',
	'menu_capability'         => 'manage_options',
	'menu_icon'               => null,
	'menu_position'           => null,
	'menu_hidden'             => false,
	'menu_parent'             => 'themes.php',

	'show_bar_menu'           => true,
	'show_sub_menu'           => false,
	'show_in_network'         => true,
	'show_in_customizer'      => false,

	'show_search'             => false,
	'show_reset_all'          => true,
	'show_reset_section'      => true,
	'show_footer'             => true,
	'show_all_options'        => true,
	'show_form_warning'       => true,
	'sticky_header'           => true,
	'save_defaults'           => true,
	'ajax_save'               => true,

	'admin_bar_menu_icon'     => 'cx icon-s9',
	'admin_bar_menu_priority' => 80,

	'footer_text'             => '',
	'footer_after'            => '',
	'footer_credit'           => '',

	'database'                => '',
	'transient_time'          => 0,

	'contextual_help'         => array(),
	'contextual_help_sidebar' => '',

	'enqueue_webfont'         => true,
	'async_webfont'           => false,

	'output_css'              => true,

	'nav'                     => 'normal',
	'theme'                   => 'be',
	'class'                   => '',

	'defaults'                => array(),

));
CSF::createSection( $prefix, array(
	'title'       => '操作说明',
	'icon'  => 'dashicons dashicons-buddicons-groups',
	'description' => '主题选项操作说明',
	'fields'      => array(

		array(
			'class'    => 'be-home-help',
			'title'   => '右上按钮',
			'type'    => 'content',
			'content' => '<i class="dashicons dashicons-editor-help"></i>主题使用说明链接&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="dashicons dashicons-plus-alt"></i>展开所有设置选项&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="dashicons dashicons-update-alt"></i>保存设置',
		),
		array(
			'class'    => 'be-home-help',
			'title'   => '主题使用说明',
			'type'    => 'content',
			'content' => '子主题和一般的主题安装方法相同，直接在wp后台->外观->设置中上传启用即可，前提是您已经安装并且激活了B2主题',
		),
		
		array(
			'title'   => '当前版本',
			'type'    => 'content',
			'content' => '当前版本：'.THEME_VERSION.' | <a href="https://www.wwttl.com" target="_blank" rel="external nofollow" class="url themes-inf">主题日志</a> |
                    <a href="https://www.wwttl.com" target="_blank" rel="external nofollow" class="url themes-inf">定制主题</a>',
		),
	)
));

//基本设置
CSF::createSection( $prefix, array(
	'id'    => 'admin_setting',
	'title' => '基本设置',
	'icon'        => 'dashicons dashicons-admin-generic',
) );
CSF::createSection( $prefix, array(
	'parent'      => 'admin_setting',
	'title'       => '基础设置',
	'icon'        => '',
	'description' => '网站全局基础设置',
	'fields'      => array(

		array(
			'id'       => 'page_fps',
			'type'     => 'switcher',
			'title'    => 'FPS帧',
		),

		array(
			'id'       => 'page_jzf12',
			'type'     => 'switcher',
			'title'    => '禁止f12',
		),
		array(
			'id'       => 'page_jyyj',
			'type'     => 'switcher',
			'title'    => '禁止右键',
		),


		array(
			'id'       => 'page_dmgl',
			'type'     => 'switcher',
			'title'    => '代码高亮一键复制',
		),
		array(
			'id'       => 'page_jnr',
			'type'     => 'switcher',
			'title'    => '每年12月13日全站变灰',
		),
    )
));
CSF::createSection( $prefix, array(
	'parent'      => 'admin_setting',
	'title'       => 'SEO设置',
	'icon'        => '',
	'description' => 'SEO相关的设置',
	'fields'      => array(
		array(
			'id'       => 'keyword_link',
			'type'     => 'switcher',
			'title'    => '全局关键词',
			'default'  => false,
		),

		array(
			'class'    => 'be-button-url be-child-item be-child-last-item',
			'type'     => 'subheading',
			'title'    => '添加关键词',
			'content'  => '<span class="button-primary"><a href="' . home_url() . '/wp-admin/options-general.php?page=keywordlink" target="_blank">添加关键词</a></span>',
			'dependency' => array('keyword_link', '==', 'true'),
		),
		array(
			'id'       => 'image_alt',
			'type'     => 'switcher',
			'title'    => '图片Alt标签',
			'default'  => false,
		),

		array(
			'id'       => 'seo_baidu_push',
			'type'     => 'switcher',
			'title'    => '百度普通收录',
		),

		array(
			'id'       => 'seo_baidu_push_key',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'text',
			'title'    => '准入密钥',
			'dependency' => array('seo_baidu_push', '==', 'true'),
		),
		
		array(
			'id'       => 'baidu_daily',
			'type'     => 'switcher',
			'title'    => '百度快速收录',
		),

		array(
			'id'       => 'daily_token',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'text',
			'title'    => '准入密钥',
			'dependency' => array('baidu_daily', '==', 'true'),
		),
		
		array(
			'id'       => 'seo_bing_push',
			'type'     => 'switcher',
			'title'    => 'Bing推送设置',
		),

		array(
			'id'       => 'seo_bing_push_key',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'text',
			'title'    => 'bing推送key',
			'dependency' => array('seo_bing_push', '==', 'true'),
		),
		
		array(
			'id'       => 'seo_indexnow_push',
			'type'     => 'switcher',
			'title'    => 'IndexNowKey',
		),

		array(
			'id'       => 'seo_indexnow_push_key',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'text',
			'title'    => 'indexnow推送key,获取地址 <a href="https://www.bing.com/indexnow" target="_blank">https://www.bing.com/indexnow</a>',
			'after'    => '请在网站根目录创建与“indexnow推送key”一致的txt文件 如key为 “7560860ac3774aea86cb3568fb862f42” 就创建”7560860ac3774aea86cb3568fb862f42.txt“ txt内容为 “7560860ac3774aea86cb3568fb862f42',
			'dependency' => array('seo_indexnow_push', '==', 'true'),
		),
		
		array(
			'id'       => 'seo_jrtt_push',
			'type'     => 'switcher',
			'title'    => '头条推送设置',
		),

		array(
			'id'       => 'seo_jrtt_push_key',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'wp_editor',
			'title'    => '自动收录代码 不要添加script标签',
			'dependency' => array('seo_jrtt_push', '==', 'true'),
		),
		array(
			'id'       => 'time_factor',
			'type'     => 'switcher',
			'title'    => '头条搜索时间因子',
			'default'  => false,
		),
		array(
			'id'       => 'seo_sm_push',
			'type'     => 'switcher',
			'title'    => '神马推送设置',
		),

		array(
			'id'       => 'seo_sm_push_token',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'text',
			'title'    => '接口调用地址,获取地址 <a href="https://zhanzhang.sm.cn/open/mip" target="_blank">https://zhanzhang.sm.cn/open/mip</a>',
			'dependency' => array('seo_sm_push', '==', 'true'),
		),
		array(
			'id'       => 'silian_silian',
			'type'     => 'switcher',
			'title'    => '更新txt格式站点失效链接',
			'after'    => '<span class="after-perch">链接：<a href="' . home_url() . '/silian.txt" target="_blank">silian.txt</a></span>',
		),
		array(
			'id'       => 'sitemap_xml',
			'type'     => 'switcher',
			'title'    => '更新xml格式站点地图',
			'after'    => '<span class="after-perch">链接：<a href="' . home_url() . '/sitemap.xml" target="_blank">sitemap.xml</a></span>',
		),



    )
));


CSF::createSection( $prefix, array(
	'parent'      => 'admin_setting',
	'title'       => '分类设置',
	'icon'        => '',
	'description' => 'SEO相关的设置',
	'fields'      => array(




    )
));

CSF::createSection( $prefix, array(
	'parent'      => 'admin_setting',
	'title'       => '文章设置',
	'icon'        => '',
	'description' => 'SEO相关的设置',
	'fields'      => array(
		array(
			'id'       => 'h_label',
			'type'     => 'switcher',
			'title'    => 'H标签美化',
		),
		array(
			'id'       => 'h_label_css',
			'type'    => 'radio',
			'title'   => 'h标签美化样式',
			'inline'  => true,
			'options' => array(
				'h1'      => '样式1',
				'h2'      => '样式2',
				'h3'      => '样式3',
				'h4'      => '样式4',
				'h5'      => '样式5',
				'h6'      => '样式6',
				'h7'      => '样式7',
				'h8'      => '样式8',
				'h9'      => '样式9',
				'h10'      => '样式10',
			),
			'default' => 'h1',
			'dependency' => array('h_label', '==', 'true'),
		),
		array(
			'id'       => 'word_count',
			'type'     => 'switcher',
			'title'    => '显示文章字数',
		),
		array(
			'id'       => 'reading_time',
			'type'     => 'switcher',
			'title'    => '显示阅读时间',
		),

		array(
			'id'       => 'baidu_record',
			'type'     => 'switcher',
			'title'    => '显示百度收录与否',
		),
		array(
			'id'       => 'authorCard',
			'type'     => 'switcher',
			'title'    => '显示底部作者信息',
		),

		array(
			'id'       => 'all_more',
			'type'     => 'switcher',
			'title'    => '使用点击阅读全文按钮',
		),
		array(
			'id'       => 'begin_today',
			'type'     => 'switcher',
			'title'    => '往年今天的文章',
		),
		array(
			'id'       => 'g_search',
			'type'     => 'switcher',
			'title'    => '仅搜索文章标题',
		),
		array(
			'id'       => 'link_internal',
			'type'     => 'switcher',
			'title'    => '文章内链新窗口打开',
			'default'  => false,
			'after'    => '文章内链接新窗口打开，需与【用文章标签作为关键词添加内链】的选项同时使用',
		),
		array(
			'id'       => 'link_external',
			'type'     => 'switcher',
			'title'    => '文章外链添加nofollow',
			'default'  => false,
		),
	    array(
			'id'       => 'tag_add',
			'type'     => 'switcher',
			'title'    => '自动给文章添加标签（1.6）',
		),

		array(
			'id'       => 'tag_c',
			'type'     => 'switcher',
			'title'    => '以文章标签作为关键词添加内链',
			'default'  => false,
		),
		array(
			'id'       => 'chain_n',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'number',
			'title'    => '链接数量',
			'default'  => 2,
			'dependency' => array('tag_c', '==', 'true'),
		),
		array(
			'id'       => 'crumbs',
			'type'     => 'switcher',
			'title'    => '文章面包屑',
		),
		array(
			'id'       => 'weibo',
			'type'     => 'switcher',
			'title'    => '微博同步',
			'after'    => '先到微博中申请access_token：<a href="https://open.weibo.com/tools/console">https://open.weibo.com/tools/console</a>',
		),
		array(
			'id'       => 'access_token',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'text',
			'title'    => '微博access_token',
			'dependency' => array('weibo', '==', 'true'),
		),
    )
));
CSF::createSection( $prefix, array(
	'parent'      => 'admin_setting',
	'title'       => '通知设置',
	'icon'        => '',
	'description' => 'SEO相关的设置',
	'fields'      => array(
		array(
			'id'       => 'setup_email_smtp',
			'type'     => 'switcher',
			'title'    => '★ 邮件SMTP',
		),

		array(
			'id'       => 'email_name',
			'type'     => 'text',
			'title'    => '发件人名称',
			'default'  => '不错吧',
			'dependency' => array('setup_email_smtp', '==', 'true'),
		),

		array(
			'id'       => 'email_smtp',
			'type'     => 'text',
			'title'    => '邮箱SMTP服务器',
			'default'  => 'smtp.qq.com',
			'dependency' => array('setup_email_smtp', '==', 'true'),
		),

		array(
			'id'       => 'email_account',
			'type'     => 'text',
			'title'    => '邮箱账户',
			'default'  => 'tiktok027@qq.com',
			'dependency' => array('setup_email_smtp', '==', 'true'),
		),

		array(
			'id'       => 'email_authorize',
			'type'     => 'text',
			'title'    => '客户端授权密码',
			'after'    => '非邮箱登录密码',
			'default'  => 'NLSUYCUSEXUGUYHR',
			'dependency' => array('setup_email_smtp', '==', 'true'),
		),

		array(
			'id'       => 'email_port',
			'type'     => 'text',
			'title'    => '端口',
			'after'    => '不需要改',
			'default'  => '465',
			'dependency' => array('setup_email_smtp', '==', 'true'),
		),

		array(
			'id'       => 'email_secure',
			'type'     => 'text',
			'title'    => 'SSL',
			'after'    => '端口25时 留空，465时 ssl，不需要改',
			'default'  => 'ssl',
			'dependency' => array('setup_email_smtp', '==', 'true'),
		),
		array(
			'id'       => 'new_pending',
			'type'     => 'switcher',
			'title'    => '文章待审通知',
			'default'  => false,
			'after'    => '（请确认已将子主题Modules文件夹覆盖进b2主题。并正确完成邮箱SMTP设置。）',
			'dependency' => array('setup_email_smtp', '==', 'true'),
		),
		array(
			'id'       => 'circle_pending',
			'type'     => 'switcher',
			'title'    => '帖子待审通知',
			'default'  => false,
			'after'    => '（请确认已将子主题Modules文件夹覆盖进b2主题。并正确完成邮箱SMTP设置。）',
			'dependency' => array('setup_email_smtp', '==', 'true'),
		),
		array(
			'id'       => 'links_pending',
			'type'     => 'switcher',
			'title'    => '友情链接待审通知',
			'default'  => false,
			'after'    => '（请确认已正确完成邮箱SMTP设置）',
			'dependency' => array('setup_email_smtp', '==', 'true'),
		),
		array(
			'id'       => 'page_email',
			'type'     => 'switcher',
			'title'    => '邮件美化（1.5）',
			'dependency' => array('setup_email_smtp', '==', 'true'),
		),
	    array(
			'id'       => 'bot_api',
			'type'     => 'switcher',
			'title'    => '★ 开启机器人功能',
		),
		
		array(
			'id'      => 'bot_api_server',
			'type'    => 'radio',
			'title'   => '★ 1.选择机器人通道',
			'inline'  => true,
			'options' => array(
				'boe_api_qq'  => 'QQ机器人(仅支持go-cqhttp)自建',
				'boe_api_dd'  => '钉钉机器人(钉钉群机器人)',
			),
			'default' => 'boe_api_qq',
			'dependency' => array('bot_api', '==', 'true'),
		),
		
		array(
			'id'       => 'bot_api_url',
			'type'     => 'text',
			'title'    => '★ 2.配置机器人API地址',
			'default'  => 'http://127.0.0.1:5700',
			'after'    => '填写自己的go-cqhttp QQ机器人api地址 或者 钉钉机器人Webhook(https://oapi.dingtalk.com/robot/send?access_token=xxxx)',
			'dependency' => array('bot_api', '==', 'true'),
		),
		array(
			'id'       => 'bot_api_qqnum',
			'type'     => 'text',
			'title'    => '★ 2-1接收QQ机器人消息的QQ号码',
			'default'  => '2651636361',
			'dependency' => array('bot_api', '==', 'true'),
		),
		array(
			'id'       => 'bot_api_ddkey',
			'type'     => 'text',
			'title'    => '★ 2-2钉钉机器人加签秘钥',
			'default'  => '2651636361',
			'dependency' => array('bot_api', '==', 'true'),
		),
		array(
			'title'   => '★ 机器人通知项',
			'type'    => 'content',
			'dependency' => array('bot_api', '==', 'true'),
		),
		array(
			'id'       => 'bot_api_comment',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'switcher',
			'title'    => '开启新评论消息推送',
			'dependency' => array('bot_api', '==', 'true'),
		),
		array(
			'id'       => 'bot_api_reguser',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'switcher',
			'title'    => '开启新会员注册通知',
			'dependency' => array('bot_api', '==', 'true'),
		),

    )
));


CSF::createSection( $prefix, array(
	'parent'      => 'admin_setting',
	'title'       => '媒体设置',
	'icon'        => '',
	'description' => '全局媒体设置',
	'fields'      => array(
		array(
			'id'       => 'other_tpys',
			'type'     => 'switcher',
			'title'    => '图片压缩',
		),
		array(
			'class'    => 'be-button-url',
			'type'     => 'subheading',
			'title'    => '详细设置',
			'content'  => '<span class="button-primary"><a href="' . home_url() . '/wp-admin/options-general.php?page=resize-after-upload" target="_blank">设置压缩</a></span>',
			'dependency' => array('other_tpys', '==', 'true'),
		),
		array(
			'id'       => 'other_scwjcmm',
			'type'     => 'switcher',
			'title'    => '上传文件重命名',
		),
		array(
			'id'       => 'other_scwedp',
			'type'     => 'switcher',
			'title'    => '上传webp图片',
		),
		array(
			'id'       => 'other_wedpslt',
			'type'     => 'switcher',
			'title'    => 'webp图片缩略图',
		),
        array(
            'id' => 'g_cos_fieldset',
            'type' => 'fieldset',
            'fields' => array(
                array(
                    'type' => 'subheading',
                    'content' => 'DogeCloud 云存储',
                ),
                array(
                    'type' => 'content',
                    'style' => 'info',
                    'content' => 'DogeCloud 云存储提供<strong> 10 GB </strong>的免费存储额度，<strong> 20 GB </strong>每月的免费 CDN 额度，<a target="_blank" href="https://console.dogecloud.com/register.html?iuid=4666">立即注册</a>',
                ),
                array(
                    'id' => 'g_cos',
                    'type' => 'switcher',
                    'title' => '功能开关',
                    'subtitle' => '开启/关闭 DogeCloud 云存储<span style="color: #ff000c;">（注意：OSS只能选择一个开启）</span>',
                    'text_on' => '开启',
                    'text_off' => '关闭',
                ),
                array(
                    'id' => 'g_cos_bucketname',
                    'type' => 'text',
                    'title' => '空间名称',
                    'subtitle' => '空间名称可在空间基本信息中查看',
                    'desc' => '<a target="_blank" href="https://console.dogecloud.com/oss/list">点击这里</a>查询空间名称',
                    'dependency' => array('g_cos', '==', 'true'),
                ),
                array(
                    'id' => 'g_cos_url',
                    'type' => 'text',
                    'title' => '加速域名',
                    'subtitle' => '域名结尾不要添加 /',
                    'desc' => '<a target="_blank" href="https://console.dogecloud.com/oss/list">点击这里</a>查询加速域名',
                    'dependency' => array('g_cos', '==', 'true'),
                ),
                array(
                    'id' => 'g_cos_accesskey',
                    'type' => 'text',
                    'title' => 'AccessKey',
                    'subtitle' => '出于安全考虑，建议周期性地更换密钥',
                    'desc' => '<a target="_blank" href="https://console.dogecloud.com/user/keys">点击这里</a>查询 AccessKey',
                    'dependency' => array('g_cos', '==', 'true'),
                ),
                array(
                    'id' => 'g_cos_secretkey',
                    'type' => 'text',
                    'attributes' => array(
                        'type' => 'password',
                    ),
                    'title' => 'SecretKey',
                    'subtitle' => '出于安全考虑，建议周期性地更换密钥',
                    'desc' => '<a target="_blank" href="https://console.dogecloud.com/user/keys">点击这里</a>查询 SecretKey',
                    'dependency' => array('g_cos', '==', 'true'),
                ),
            ),
            'default' => array(
                'g_cos' => false,
                'g_cos_bucketname' => '',
                'g_cos_url' => '',
                'g_cos_accesskey' => '',
                'g_cos_secretkey' => '',
            ),
        ),
        array(
            'id' => 'g_imgx_fieldset',
            'type' => 'fieldset',
            'fields' => array(
                array(
                    'type' => 'subheading',
                    'content' => '火山引擎 ImageX',
                ),
                array(
                    'type' => 'content',
                    'style' => 'info',
                    'content' => '火山引擎 ImageX 提供<strong> 10 GB </strong>的免费存储额度，<strong> 10 GB </strong>每月的免费 CDN 额度，<strong> 20 TB </strong>每月的图像处理额度，<a target="_blank" href="https://www.volcengine.com/products/imagex?utm_content=ImageX&utm_medium=i4vj9y&utm_source=u7g4zk&utm_term=ImageX-kratos">立即注册</a>',
                ),
                array(
                    'id' => 'g_imgx',
                    'type' => 'switcher',
                    'title' => '功能开关',
                    'subtitle' => '开启/关闭 火山引擎 ImageX<span style="color: #ff000c;">（注意：OSS只能选择一个开启）</span>',
                    'text_on' => '开启',
                    'text_off' => '关闭',
                ),
                array(
                    'id' => 'g_imgx_region',
                    'type' => 'select',
                    'title' => '加速地域',
                    'subtitle' => '加速地域在创建服务的时候进行选择',
                    'desc' => '<a target="_blank" href="https://console.volcengine.com/imagex/service_manage/">点击这里</a>查询加速地域',
                    'options' => array(
                        'cn-north-1' => '国内',
                        'us-east-1' => '美东',
                        'ap-singapore-1' => '新加坡',
                    ),
                    'dependency' => array('g_imgx', '==', 'true'),
                ),
                array(
                    'id' => 'g_imgx_serviceid',
                    'type' => 'text',
                    'title' => '服务 ID',
                    'subtitle' => '服务 ID 可在图片服务管理中查看',
                    'desc' => '<a target="_blank" href="https://console.volcengine.com/imagex/service_manage/">点击这里</a>查询服务 ID',
                    'dependency' => array('g_imgx', '==', 'true'),
                ),
                array(
                    'id' => 'g_imgx_url',
                    'type' => 'text',
                    'title' => '加速域名',
                    'subtitle' => '域名结尾不要添加 /',
                    'desc' => '<a target="_blank" href="https://console.volcengine.com/imagex/service_manage/">点击这里</a>查询加速域名',
                    'dependency' => array('g_imgx', '==', 'true'),
                ),
                array(
                    'id' => 'g_imgx_tmp',
                    'type' => 'text',
                    'title' => '处理模板',
                    'subtitle' => '处理模板可在图片处理配置中查看',
                    'desc' => '<a target="_blank" href="https://console.volcengine.com/imagex/image_template/">点击这里</a>查询处理模板',
                    'dependency' => array('g_imgx', '==', 'true'),
                ),
                array(
                    'id' => 'g_imgx_accesskey',
                    'type' => 'text',
                    'title' => 'AccessKey',
                    'subtitle' => '出于安全考虑，建议周期性地更换密钥',
                    'desc' => '<a target="_blank" href="https://console.volcengine.com/iam/keymanage/">点击这里</a>查询 AccessKey',
                    'dependency' => array('g_imgx', '==', 'true'),
                ),
                array(
                    'id' => 'g_imgx_secretkey',
                    'type' => 'text',
                    'attributes' => array(
                        'type' => 'password',
                    ),
                    'title' => 'SecretKey',
                    'subtitle' => '出于安全考虑，建议周期性地更换密钥',
                    'desc' => '<a target="_blank" href="https://console.volcengine.com/iam/keymanage/">点击这里</a>查询 SecretKey',
                    'dependency' => array('g_imgx', '==', 'true'),
                ),
            ),
            'default' => array(
                'g_imgx' => false,
                'g_imgx_region' => 'cn-north-1',
                "g_imgx_serviceid" => "",
                "g_imgx_url" => "",
                "g_imgx_tmp" => "",
                "g_imgx_accesskey" => "",
                "g_imgx_secretkey" => "",
            ),
        ),
    )
));	


CSF::createSection( $prefix, array(
	'parent'      => 'admin_setting',
	'title'       => '前端优化',
	'icon'        => '',
	'description' => 'SEO相关的设置',
	'fields'      => array(
		array(
			'id'       => 'other_wdlqzmh',
			'type'     => 'switcher',
			'title'    => '未登录全站图片模糊',
		),
		array(
			'id'       => 'other_wdlwzmh',
			'type'     => 'switcher',
			'title'    => '未登录文章详情页内图片模糊',
		),

	    array(
			'id'       => 'html_compress',
			'type'     => 'switcher',
			'title'    => '页面HTML压缩',
			'after'    => '启用或禁用压缩 HTML',
		),
		array(
			'id'       => 'minify_javascript',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'radio',
			'title'    => '压缩内联JavaScript',
			'after'    => '此选项通常可以安全地设置开启（建议不要开启）',
			'options' => array(
				'yes'  => '开启',
				'no'  => '关闭',
			),
			'default' => 'no',
			'dependency' => array('html_compress', '==', 'true'),
		),
		array(
			'id'       => 'minify_html_comments',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'radio',
			'title'    => '删除 HTML、JavaScript和 CSS 注释',
			'after'    => '此选项通常可以安全地设置开启',
			'options' => array(
				'yes'  => '开启',
				'no'  => '关闭',
			),
			'default' => 'yes',
			'dependency' => array('html_compress', '==', 'true'),
		),
		array(
			'id'       => 'minify_html_xhtml',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'radio',
			'title'    => '从 HTML5 无效元素中删除 XHTML 结束标记',
			'after'    => '此选项通常可以安全地设置开启',
			'options' => array(
				'yes'  => '开启',
				'no'  => '关闭',
			),
			'default' => 'yes',
			'dependency' => array('html_compress', '==', 'true'),
		),
		array(
			'id'       => 'minify_html_relative',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'radio',
			'title'    => '从内部网址中移除相对网域',
			'after'    => '此选项通常可以安全地设置开启',
			'options' => array(
				'yes'  => '开启',
				'no'  => '关闭',
			),
			'default' => 'on',
			'dependency' => array('html_compress', '==', 'true'),
		),
		array(
			'id'       => 'minify_html_scheme',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'radio',
			'title'    => '从所有网址删除 (HTTP: 与 HTTPS:)',
			'after'    => '此选项通常最好关闭',
			'options' => array(
				'yes'  => '开启',
				'no'  => '关闭',
			),
			'default' => 'no',
			'dependency' => array('html_compress', '==', 'true'),
		),
		array(
			'id'       => 'minify_html_utf8',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'radio',
			'title'    => '支持多字节 UTF-8 编码（如果看到奇怪的字符）',
			'after'    => '此选项通常最好关闭',
			'options' => array(
				'yes'  => '开启',
				'no'  => '关闭',
			),
			'default' => 'no',
			'dependency' => array('html_compress', '==', 'true'),
		),

		array(
			'id'       => 'add_link',
			'type'     => 'switcher',
			'title'    => '自助友情链接',
			'default'  => true,
		),
		array(
			'id'       => 'add_link_content',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'switcher',
			'title'    => '友情链接介绍',
			'default'  => true,
			'dependency' => array('add_link', '==', 'true'),
		),
		array(
			'id'          => 'link_url',
			'class'    => 'be-child-item be-child-last-item',
			'type'        => 'select',
			'title'       => '友情链接页面',
			'placeholder' => '选择页面',
			'options'     => 'pages',
			'query_args'  => array(
				'posts_per_page' => -1
			),
			'dependency' => array('add_link', '==', 'true'),
		),
    )
));

CSF::createSection( $prefix, array(
	'parent'      => 'admin_setting',
	'title'       => '后端优化',
	'icon'        => '',
	'description' => 'SEO相关的设置',
	'fields'      => array(
		array(
			'id'       => 'xmlrpc_no',
			'type'     => 'switcher',
			'title'    => '禁用xmlrpc',
			'default'  => false,
		),

		array(
			'id'       => 'Pingback_off',
			'type'     => 'switcher',
			'title'    => '禁止Pingback',
			'default'  => false,
		),

		array(
			'id'       => 'revisions_no',
			'type'     => 'switcher',
			'title'    => '禁用文章修订',
			'default'  => false,
		),

		array(
			'id'       => 'autosaveop',
			'type'     => 'switcher',
			'title'    => '禁用文章自动保存',
			'default'  => false,
		),

		array(
			'id'       => 'be_gutenberg',
			'type'     => 'switcher',
			'title'    => '禁用 Gutenberg 编辑器',
			'default'  => false,
		),

		array(
			'id'       => 'disable_api',
			'type'     => 'switcher',
			'title'    => '禁用 REST API，连接小程序需取消',
		),

		array(
			'id'       => 'rss_off',
			'type'     => 'switcher',
			'title'    => '移除RSS订阅',
		),
		
		array(
			'id'       => 'be_safety',
			'type'     => 'switcher',
			'title'    => '阻止恶意URL请求',
			'default'  => false,
		),

		array(
			'id'       => 'emoji_off',
			'type'     => 'switcher',
			'title'    => '移除 Emoji',
			'default'  => false,
		),

		array(
			'id'       => 'remove_dns_refresh',
			'type'     => 'switcher',
			'title'    => '移除头部emoji表情的dns-refresh',
			'default'  => false,
		),

		array(
			'id'       => 'remove_separator',
			'type'     => 'switcher',
			'title'    => '移除后台菜单分隔符',
			'after'    => '这里与统计插件存在兼容问题，使用统计插件请关闭！',
		),
		
		array(
			'id'       => 'page_html',
			'type'     => 'switcher',
			'title'    => '页面添加.html后缀，更改后需保存一下固定链接设置',
		),

		array(
			'id'       => 'ban_avatars',
			'type'     => 'switcher',
			'title'    => '后台禁止头像',
			'default'  => false,
		),
		
		array(
			'id'       => 'disable_privacy',
			'type'     => 'switcher',
			'title'    => '屏蔽后台隐私',
			'default'  => false,
		),
		array(
			'id'       => 'feed_cache',
			'type'     => 'number',
			'title'    => 'RSS小工具缓存时间',
			'default'  => '',
			'after'    => '<span class="after-perch">例如：7200，2天</span>',
		),



    )
));

CSF::createSection( $prefix, array(
	'parent'      => 'admin_setting',
	'title'       => '安全设置',
	'icon'        => '',
	'description' => '前后端全局安全设置',
	'fields'      => array(
		array(
			'id'       => 'waf_yzm',
			'type'     => 'switcher',
			'title'    => '后台登录验证码',
			'after'    => '后台登录会有数字验证码',
		),
		array(
			'id'       => 'waf_url',
			'type'     => 'switcher',
			'title'    => '后台登录地址',
			'after'    => '开启后会修改后台登录地址！！！',
		),
		array(
			'id'       => 'waf_url_get',
			'type'     => 'text',
			'title'    => '参数前缀',
			'default'  => 'ATMJGY',
			'after'    =>  home_url() . '/wp-login.php?参数前缀=参数密码',
			'dependency' => array('waf_url', '==', 'true'),
		),
		array(
			'id'       => 'waf_url_pass',
			'type'     => 'text',
			'title'    => '参数密码',
			'default'  => 'Good',
			'after'    =>  home_url() . '/wp-login.php?参数前缀=参数密码',
			'dependency' => array('waf_url', '==', 'true'),
		),



    )
));

//美化模块
CSF::createSection( $prefix, array(
	'id'     => 'style_setting',
	'title'  => '美化模块',
	'icon'  => 'dashicons dashicons-admin-appearance',
) );
CSF::createSection( $prefix, array(
	'parent'      => 'style_setting',
	'title'       => '全局样式',
	'icon'        => '',
	'description' => '全局美化设置',
	'fields'      => array(
		array(
			'id'       => 'page_dtwzbt',
			'type'     => 'switcher',
			'title'    => '动态页标',
		),
		array(
			'id'       => 'page_night',
			'type'     => 'switcher',
			'title'    => '夜间模式',
		),
		array(
			'id'       => 'fillet',
			'type'     => 'switcher',
			'title'    => '全局圆角',
			'after'    => '开启后去父主题 【模块设置】 设置【圆角弧度】。15px效果相对较好',
		),
		array(
			'id'       => 'cursor',
			'type'     => 'switcher',
			'title'    => '鼠标特效',
		),
		array(
			'id'       => 'cursor_js',
			'type'    => 'radio',
			'title'   => '鼠标特效样式',
			'inline'  => true,
			'options' => array(
				'cursor1'      => '样式1',
				'cursor2'      => '样式2',
				'cursor3'      => '样式3',
				'cursor4'      => '样式4',
				'cursor5'      => '样式5',
				'cursor6'      => '样式6',
				'cursor7'      => '样式7',
				'cursor8'      => '样式8',
				'cursor9'      => '样式9',
				'cursor10'      => '样式10',
			),
			'default' => 'cursor9',
			'dependency' => array('cursor', '==', 'true'),
		),
		array(
			'id'       => 'page_fyxg',
			'type'     => 'switcher',
			'title'    => '元素飘落枫叶效果',
		),
		array(
			'id'       => 'page_dbjdt',
			'type'     => 'switcher',
			'title'    => '顶部阅读进度条',
		),
		array(
			'id'       => 'page_tc',
			'type'     => 'switcher',
			'title'    => '稀奇古怪的弹窗',
		),
		array(
			'id'       => 'page_ycxfgjt',
			'type'     => 'switcher',
			'title'    => 'PC右侧悬浮条美化',
		),
		array(
			'id'       => 'page_pace',
			'type'     => 'switcher',
			'title'    => '加载进度条',
		),
		array(
        'id' => 'pace_css',
        'class' => 'el pace_img be-child-item be-child-last-item',
        'type' => 'image_select',
        'title' => '加载进度条样式',
        'inline'  => true,
        'options' => array(
                'pace-theme-minimal' => $imagepath . 'Minimal.png',
				'pace-theme-flash' => $imagepath . 'Flash.png',
				'pace-theme-barber-shop' => $imagepath . 'Barber-Shop.png',
				'pace-theme-mac-osx' => $imagepath . 'Mac-OSX.png',
				'pace-theme-fill-left' => $imagepath . 'Fill-Left.png',
				'pace-theme-flat-top' => $imagepath . 'Flat-Top.png',
				'pace-theme-big-counter' => $imagepath . 'Big-Counter.png',
				'pace-theme-corner-indicator' => $imagepath . 'Corner-Indicator.png',
				'pace-theme-bounce' => $imagepath . 'Bounce.png',
				'pace-theme-loading-bar' => $imagepath . 'Loading-Bar.png',
				'pace-theme-center-circle' => $imagepath . 'Center-Circle.png',
				'pace-theme-center-atom' => $imagepath . 'Center-Atom.png',
				'pace-theme-center-radar' => $imagepath . 'Center-Radar.png',
				'pace-theme-center-simple' => $imagepath . 'Center-Simple.png',
        ),
        'default' => 'pace-theme-flash',
        'dependency' => array('page_pace', '==', 'true'),

      ),
	    array(
			'id'       => 'page_default',
			'type'     => 'switcher',
			'title'    => '默认样式开启',
			'default'  => true,
		),
		array(
			'id'       => 'iconfont',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'switcher',
			'title'    => '全局阿里小图标',
		),
		array(
			'id'       => 'iconfont_css',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'text',
			'title'    => '阿里小图标css',
			'dependency' => array('iconfont', '==', 'true'),
		),
		array(
			'id'       => 'iconfont_js',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'text',
			'title'    => '阿里小图标js',
			'dependency' => array('iconfont', '==', 'true'),
		),
		array(
			'id'       => 'page_bold',
			'type'     => 'switcher',
			'title'    => '部分图标加粗',
		),


    )
));
CSF::createSection( $prefix, array(
	'parent'      => 'style_setting',
	'title'       => '头部模块',
	'icon'        => '',
	'description' => '头部模块美化设置',
	'fields'      => array(
        array(
			'id'       => 'page_b2child_vip',
			'type'     => 'switcher',
			'title'    => '导航栏VIP',
			'after'    => '请将子主题Modules文件夹覆盖进b2主题',
		),
		array(
			'id'       => 'page_b2child_vip_text',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'text',
			'title'    => '第一行文字',
			'default'  => '开通铂金会员享受专属权益',
			'dependency' => array('page_b2child_vip', '==', 'true'),
		),
		array(
			'id'       => 'page_b2child_vip_rmb',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'number',
			'title'    => '价格',
			'default'  => '179',
			'dependency' => array('page_b2child_vip', '==', 'true'),
		),



    )
));
CSF::createSection( $prefix, array(
	'parent'      => 'style_setting',
	'title'       => '底部模块',
	'icon'        => '',
	'description' => '底部模块美化设置',
	'fields'      => array(
	    array(
			'id'       => 'page_qztj',
			'type'     => 'switcher',
			'title'    => '全站统计',
			'after'    => 'php8以上有Warning报错（我直接error_reporting(0) 所以你们懂！！！）如果开启报错请在根目录创建maplers.dat——————目前开启与b2有冲突',
		),
		array(
			'id'       => 'page_qztj_moren',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'number',
			'title'    => '总访问量起始数字',
			'default'  => 1000,
			'dependency' => array('page_qztj', '==', 'true'),
		),
		array(
			'id'      => 'page_qztj_wz',
			'class'   => 'be-child-item be-child-last-item',// 			'class'   => 'be-flex-color',
			'type'    => 'color',
			'default' => '',
			'title'  => '统计文字颜色',
			'dependency' => array('page_qztj', '==', 'true'),
		),
		array(
			'id'      => 'page_qztj_sz',
            'class'   => 'be-child-item be-child-last-item',// 			'class'   => 'be-flex-color',
			'type'    => 'color',
			'default' => '',
			'title'  => '统计数字颜色',
			'dependency' => array('page_qztj', '==', 'true'),
		),
		array(
			'id'       => 'page_lxmk',
			'type'     => 'switcher',
			'title'    => '左侧联系模块',
		),
		array(
			'id'       => 'page_lxmk_url',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'text',
			'title'    => '联系模块的链接',
			'dependency' => array('page_lxmk', '==', 'true'),
		),
		array(
			'id'       => 'page_dbkax',
			'type'     => 'switcher',
			'title'    => '页面底部可爱线',
		),
		array(
			'id'       => 'page_dbkax_img',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'upload',
			'title'    => '底部可爱线图片',
			'preview'  => true,
			'dependency' => array('page_dbkax', '==', 'true'),
		),
		array(
			'id'       => 'page_dbkax_text',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'text',
			'title'    => '底部可爱线文字',
			'default'  => '我是底线可爱胖！冲鸭~',
			'dependency' => array('page_dbkax', '==', 'true'),
		),
		array(
			'id'       => 'page_dbkax2',
			'type'     => 'switcher',
			'title'    => '页面底部可爱线-样式2',
		),
		array(
			'id'       => 'page_dbkax_img2',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'upload',
			'title'    => '底部可爱线图片-样式2',
			'preview'  => true,
			'dependency' => array('page_dbkax2', '==', 'true'),
		),

    )
));
CSF::createSection( $prefix, array(
	'parent'      => 'style_setting',
	'title'       => '首页',
	'icon'        => '',
	'description' => '首页模块美化设置',
	'description' => '动态搜索美化代码复制到头部HTML标签<link rel="stylesheet" type="text/css" href="//你的域名/wp-content/themes/b2child/Assets/Css/Modular/bwveur0fle.css" />
<script src="//你的域名/wp-content/themes/b2child/Assets/Js/Modular/bwveur0fle.js"></script>',
	'fields'      => array(
        array(
			'id'       => 'index_Search',
			'type'     => 'switcher',
			'title'    => '动态搜索模块',
		),
		array(
			'id'       => 'index_onecad_search_mp4',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'upload',
			'title'    => '视频',
			'preview'  => true,
			'default'  => '//wwttl.com/wp-content/themes/b2child/images/2023.mp4',
			'dependency' => array('index_Search', '==', 'true'),
		),
		array(
            'id' => 'index_onecad_search_rmtj',
            'class'    => 'be-child-item be-child-last-item',
            'type' => 'group',
            'title' => '热门关键词',
            'dependency' => array('index_Search', '==', 'true'),
            'fields' => array(
                array(
                    'id' => 'ad_id',
                    'type' => 'text',
                    'title' =>  '唯一标识',
                    'subtitle' =>  '仅用于识别模块，可以作为备注使用',
                ),
                array(
                    'id' => 'ad_text',
                    'type' => 'text',
                    'title' =>  '标题',
                ),
                array(
                    'id' => 'ad_url',
                    'type' => 'text',
                    'title' =>  '链接',
                ),
            ),
        ),
        array(
            'id' => 'index_onecad_search_ksljdh',
            'class'    => 'be-child-item be-child-last-item',
            'type' => 'group',
            'title' => '快速链接导航',
            'dependency' => array('index_Search', '==', 'true'),
            'max' => 5,
            'fields' => array(
                array(
                    'id' => 'ad_svg',
                    'type' => 'text',
                    'title' =>  '小图标',
                    'subtitle' =>  '如#hg-zixun1',
                ),
                array(
                    'id' => 'ad_text',
                    'type' => 'text',
                    'title' =>  '标题',
                ),
                array(
                    'id' => 'ad_url',
                    'type' => 'text',
                    'title' =>  '链接',
                ),
                array(
                    'id' => 'ad_text_gjc1',
                    'type' => 'text',
                    'title' =>  '关键词标题1',
                ),
                array(
                    'id' => 'ad_url_lj1',
                    'type' => 'text',
                    'title' =>  '关键词链接1',
                ),
                array(
                    'id' => 'ad_text_gjc2',
                    'type' => 'text',
                    'title' =>  '关键词标题2',
                ),
                array(
                    'id' => 'ad_url_lj2',
                    'type' => 'text',
                    'title' =>  '关键词链接2',
                ),
            ),
        ),
        array(
			'id'       => 'Fivebarslide',
			'type'     => 'switcher',
			'title'    => '五格幻灯片模块',
		),
		array(
			'id'       => 'Fivebarslide_mobile',
			'type'     => 'switcher',
			'title'    => '仅桌面设备显示',
			'dependency' => array('Fivebarslide', '==', 'true'),
		),
		array(
            'id' => 'Fivebarslide_add',
            'class'    => 'be-child-item be-child-last-item',
            'type' => 'group',
            'title' => '大幻灯片信息',
            'dependency' => array('Fivebarslide', '==', 'true'),
            'fields' => array(
                array(
                    'id' => 'ad_url',
                    'type' => 'text',
                    'title' =>  '链接',
                ),
                array(
                    'id' => 'ad_img',
                    'type'     => 'upload',
			        'title'    => '图片',
			        'preview'  => true,
                ),
            ),
        ),
        array(
            'id' => 'Fivebarslide_addx',
            'class'    => 'be-child-item be-child-last-item',
            'type' => 'group',
            'title' => '小幻灯片信息',
            'dependency' => array('Fivebarslide', '==', 'true'),
            'max' => 4,
            'fields' => array(
                array(
                    'id' => 'ad_url',
                    'type' => 'text',
                    'title' =>  '链接',
                ),
                array(
                    'id' => 'ad_img',
                    'type'     => 'upload',
			        'title'    => '图片',
			        'preview'  => true,
                ),
            ),
        ),
        array(
			'id'       => 'hotUser',
			'type'     => 'switcher',
			'title'    => '用户展示模块',
		),
		array(
			'id'       => 'one_index_user_title',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'text',
			'title'    => '标题',
			'default'  => '活跃用户',
			'dependency' => array('hotUser', '==', 'true'),
		),
		array(
			'id'       => 'page_b2child_vip_text',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'text',
			'title'    => '模块描述',
			'dependency' => array('hotUser', '==', 'true'),
		),
		array(
			'id'       => 'one_index_user_ys',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'switcher',
			'title'    => '精美样式',
			'dependency' => array('hotUser', '==', 'true'),
		),
		array(
			'id'       => 'one_index_user_id',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'text',
			'title'    => '用户id',
			'default'  => '1,2,3',
			'dependency' => array('hotUser', '==', 'true'),
		),
		array(
			'id'       => 'one_index_user_all',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'select',
			'title'    => '全部用户页面',
			'placeholder' => '选择页面',
			'options'     => 'pages',
			'query_args'  => array(
				'posts_per_page' => -1
			),
			'dependency' => array('hotUser', '==', 'true'),
		),
		array(
			'id'       => 'siteCount',
			'type'     => 'switcher',
			'title'    => '底部统计模块',
		),
		array(
			'id'       => 'siteCount_time',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'text',
			'title'    => '建站时间',
			'default'  => '2022/10/23',
			'dependency' => array('siteCount', '==', 'true'),
		),
		array(
			'id'       => 'siteCount_img',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'upload',
			'title'    => '底部统计模块图片',
			'preview'  => true,
			'dependency' => array('siteCount', '==', 'true'),
		),

    )
));

CSF::createSection( $prefix, array(
	'parent'      => 'style_setting',
	'title'       => '分类',
	'icon'        => '',
	'description' => '分类模块美化设置',
	'fields'      => array(




    )
));
CSF::createSection( $prefix, array(
	'parent'      => 'style_setting',
	'title'       => '文章',
	'icon'        => '',
	'description' => '文章界面美化设置',
	'fields'      => array(
		array(
			'id'       => 'page_wzmsbq',
			'type'     => 'switcher',
			'title'    => '文章末尾版权',
		),
		array(
			'id'       => 'page_wzmsbq_text',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'text',
			'title'    => '版权名称',
			'default'  => '不错吧',
			'dependency' => array('page_wzmsbq', '==', 'true'),
		),
		array(
			'id'       => 'page_wzmsbq_mail',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'text',
			'title'    => '联系邮箱',
			'default'  => '88984809@qq.com',
			'dependency' => array('page_wzmsbq', '==', 'true'),
		),



    )
));

CSF::createSection( $prefix, array(
	'parent'      => 'style_setting',
	'title'       => '用户模块',
	'icon'        => '',
	'description' => '用户界面美化设置',
	'fields'      => array(



    )
));


//手机美化模块
CSF::createSection( $prefix, array(
	'id'     => 'yd_style_setting',
	'title'  => '手机美化',
	'icon'  => 'dashicons dashicons-admin-appearance',
) );
CSF::createSection( $prefix, array(
	'parent'      => 'yd_style_setting',
	'title'       => '综合设置',
	'icon'        => '',
	'description' => '手机端综合设置',
	'fields'      => array(
        array(
			'id'       => 'footer_mh',
			'type'     => 'switcher',
			'title'    => '手机端底部美化',
		),
		array(
        'id' => 'footer_css',
        'class' => 'el footer_img be-child-item be-child-last-item',
        'type' => 'image_select',
        'title' => '手机端底部美化样式',
        'inline'  => true,
        'options' => array(
                'footer1' => $imagepath . 'footer1.png',
				'footer2' => $imagepath . 'footer2.png',
				'footer3' => $imagepath . 'footer3.png',
        ),
        'default' => 'footer1',
        'dependency' => array('footer_mh', '==', 'true'),
      ),



    )
));
//用户设置
CSF::createSection( $prefix, array(
	'id'     => 'users_setting',
	'title'  => '用户设置',
	'icon'  => 'dashicons dashicons-admin-users',
) );
CSF::createSection( $prefix, array(
	'parent'      => 'users_setting',
	'title'       => '评论设置',
	'icon'        => '',
	'description' => '用户评论设置',
	'fields'      => array(
	    array(
			'id'       => 'page_dm',
			'type'     => 'switcher',
			'title'    => '评论弹幕',
		),
		array(
			'id'       => 'page_ipgsd',
			'type'     => 'switcher',
			'title'    => '评论ip归属地（1.5）',
			'after'    => '开启后评论会显示ip归属地（请将子主题Modules文件夹覆盖进b2主题）<span class="after-perch">需上传IP数据库dat文件到网站根目录</span>',
		),
		array(
			'id'       => 'ip_dat_name',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'text',
			'title'    => '数据库文件名称',
			'default'  => 'qqwry',
			'after'    => '纯真IP库，不错吧的下载地址 <a href="https://www.wwttl.com/5801.html">纯真IP库</a>',
		),
		array(
			'id'       => 'page_yiyan',
			'type'     => 'switcher',
			'title'    => '评论自动获取一言',
		),
		array(
			'id'       => 'page_dk',
			'type'     => 'switcher',
			'title'    => '评论打卡',
		),
		array(
			'id'       => 'other_cszpl',
			'type'     => 'switcher',
			'title'    => '禁止纯数字、英文、日文评论',
		),
		array(
			'id'       => 'other_pljg',
			'type'     => 'switcher',
			'title'    => '评论间隔',
		),
		array(
			'id'       => 'other_pljg_sj',
			'class'    => 'be-child-item be-child-last-item',
			'type'     => 'number',
			'title'    => '间隔时间',
			'default'  => 30,
			'dependency' => array('other_pljg', '==', 'true'),
		),



    )
));
CSF::createSection( $prefix, array(
	'parent'      => 'users_setting',
	'title'       => '登录界面',
	'icon'        => '',
	'description' => '登录注册管理页面',
	'fields'      => array(
		array(
			'id'       => 'page_b2child_cat',
			'type'     => 'switcher',
			'title'    => '登录框猫耳朵（1.6）',
		),



    )
));
CSF::createSection( $prefix, array(
	'parent'      => 'users_setting',
	'title'       => '用户注销',
	'icon'        => '',
	'description' => '用户注销页面设置',
	'fields'      => array(
		array(
			'id'       => 'delaccount',
			'type'     => 'switcher',
			'title'    => '用户注销',
			'default'  => false,
			'after'    => '默认页面地址<a href="' . home_url() . '/delaccount" target="_blank">' . home_url() . '/delaccount</a> 无法访问请访问这个<a href="' . home_url() . '/index.php?b2_del=del" target="_blank">' . home_url() . '/index.php?b2_del=del</a>',
		),
    )
));
CSF::createSection( $prefix, array(
	'title'       => '备份选项',
	'icon'        => 'dashicons dashicons-update',
	'description' => '将主题设置数据导出为 backup + 日期.json 文件，用于备份恢复选项设置',
	'fields'      => array(

		array(
			'type' => 'backup',
		),

		array(
			'title'   => '警告',
			'type'    => 'content',
			'content' => '不要随意输入内容，并执行导入操作，否则所有设置将消失！',
		),
	)
) );