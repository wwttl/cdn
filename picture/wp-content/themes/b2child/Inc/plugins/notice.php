<?php
if ( ! defined( 'ABSPATH' ) ) exit;
//文章待审通知
if (lmy_get_option("new_pending")) {
function posts_pending($post) {
global $wpdb;
$admin_email = get_bloginfo ('admin_email');
$to = $admin_email;
$user_id = $post->post_author;
$udata   = get_userdata($user_id);
$site_name = get_bloginfo('name');
$link     = home_url().'/wp-admin/edit.php?post_status=pending&post_type=post';
$subject = '['.$site_name.']'.__('有新的待审文章！','b2');
$message = '<div style="width:700px;background-color:#fff;margin:0 auto;border: 1px solid #ccc;">
            <div style="height:64px;margin:0;padding:0;width:100%;">
                <a href="'.home_url().'" style="display:block;padding: 12px 30px;text-decoration: none;font-size: 24px;letter-spacing: 3px;border-bottom: 1px solid #ccc;" rel="noopener" target="_blank">
                    '.$site_name.'
                </a>
            </div>
            <div style="padding: 30px;margin:0;">
                <p style="font-size:14px;color:#333;">
                    '.__('你好！(' . get_option("blogname") . ')有新的待审文章：','b2').'
                </p>
                <div style="font-size:16px;color: green;"><pre>您可以打开链接以审核投稿文章：<a href="' . $link. ' " rel="noopener" target="_blank">' . $link.'</a></pre><p>投稿用户：' . $udata->display_name . '<p></div>
                <p style="font-size:12px;color:#999;border-top:1px dotted #E3E3E3;margin-top:30px;padding-top:30px;">
                    '.__('本邮件为系统邮件不能回复，请勿回复。','b2').'
                </p>
            </div>
        </div>';
        wp_mail( $to, $subject, $message);
     }
add_action('new_posts_pending', 'posts_pending', 99);
}
//帖子待审通知
if (lmy_get_option("circle_pending")) {
function circle_pending($post) {
global $wpdb;
$admin_email = get_bloginfo ('admin_email');
$to = $admin_email;
$user_id = $post->post_author;
$udata   = get_userdata($user_id);
$site_name = get_bloginfo('name');
$link     = home_url().'/wp-admin/edit.php?post_status=pending&post_type=circle';
$subject = '['.$site_name.']'.__('有新的待审帖子！','b2');
$message = '<div style="width:700px;background-color:#fff;margin:0 auto;border: 1px solid #ccc;">
            <div style="height:64px;margin:0;padding:0;width:100%;">
                <a href="'.home_url().'" style="display:block;padding: 12px 30px;text-decoration: none;font-size: 24px;letter-spacing: 3px;border-bottom: 1px solid #ccc;" rel="noopener" target="_blank">
                    '.$site_name.'
                </a>
            </div>
            <div style="padding: 30px;margin:0;">
                <p style="font-size:14px;color:#333;">
                    '.__('你好！(' . get_option("blogname") . ')有新的待审帖子：','b2').'
                </p>
                <div style="font-size:16px;color: green;"><pre>您可以打开链接以审核投稿帖子：<a href="' . $link. ' " rel="noopener" target="_blank">' . $link.'</a></pre><p>投稿用户：' . $udata->display_name . '<p></div>
                <p style="font-size:12px;color:#999;border-top:1px dotted #E3E3E3;margin-top:30px;padding-top:30px;">
                    '.__('本邮件为系统邮件不能回复，请勿回复。','b2').'
                </p>
            </div>
        </div>';
        wp_mail( $to, $subject, $message);
     }
add_action('new_circle_pending', 'circle_pending', 99);
}
//友情链接待审通知
if (lmy_get_option("links_pending")) {
function links_submit_email_to_admin($data) {
$admin_email = get_bloginfo ('admin_email');
$to = $admin_email;
$linkdata = array(
        'link_name'        => esc_attr($data['b2child_name']),
        'link_url'         => esc_url($data['b2child_url']),
        'link_description' => !empty($data['b2child_description']) ? esc_attr($data['b2child_description']) : '无',
        'link_notes'       => !empty($data['link_notes']) ? esc_attr($data['link_notes']) : '空',
    );
$link     = home_url().'/wp-admin/link-manager.php?orderby=visible&order=asc';
$site_name = get_bloginfo('name');
$subject = '['.$site_name.']'.__('新的链接待审核！','b2');
$message = '<div style="width:700px;background-color:#fff;margin:0 auto;border: 1px solid #ccc;">
            <div style="height:64px;margin:0;padding:0;width:100%;">
                <a href="'.home_url().'" style="display:block;padding: 12px 30px;text-decoration: none;font-size: 24px;letter-spacing: 3px;border-bottom: 1px solid #ccc;" rel="noopener" target="_blank">
                    '.$site_name.'
                </a>
            </div>
            <div style="padding: 30px;margin:0;">
                <p style="font-size:14px;color:#333;">
                    '.__('你好！(' . get_option("blogname") . ')新的链接待审核：','b2').'
                </p>
                <div style="font-size:16px;color: green;"><pre>网站有新的链接提交：<br />链接名称：' . $linkdata['link_name'] . '<br>链接地址：' . $linkdata['link_url'] . '<br>链接简介：' . $linkdata['link_description'] . '<br>站长Qq：' . $linkdata['link_notes'] . '<br></pre><p>您可以打开链接以审核该链接：<a target="_blank" style="margin-top: 20px" href="'.$link . '">' . $link . '</a><p></div>
                <p style="font-size:12px;color:#999;border-top:1px dotted #E3E3E3;margin-top:30px;padding-top:30px;">
                    '.__('本邮件为系统邮件不能回复，请勿回复。','b2').'
                </p>
            </div>
        </div>';
        wp_mail( $to, $subject, $message);
     }
add_action('ajax_frontend_links_submit_success', 'links_submit_email_to_admin', 99);
}