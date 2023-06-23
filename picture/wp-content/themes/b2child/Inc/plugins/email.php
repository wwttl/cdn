<?php
$style_center = true;
 
 
//定义界面顶部区域内容,请注意修改您的主题目录
$email_headertop_center = '
    <div class="emailpaged" style="background-color: #f2f5f8;">
        <div class="emailcontent" style="width:96%;max-width:720px;text-align: left;margin: 0 auto;padding-top: 20px;padding-bottom: 20px">
            <div class="emailtitle">
                <div style="position: relative;margin:0;">
                    <div style="text-align: center;margin-bottom: -20px;"> <img src="https://www.wtdxz.com/wp-content/uploads/2022/09/2022090113275712.png"  title="' . get_option("blogname") . '" style="display:inline;margin:0;max-height:100px;width: auto;" border="0"> </div>
                    
                    <div style="line-height:40px;font-size:12px;text-align: center;">
                        <a href="' . get_bloginfo('url') . '" title="' . get_option("blogname") . '" style="color:#222222;text-decoration:none;padding:0 6px;">首页</a>
                        <a href="' . get_bloginfo('url') . '/所有文章" title="所有文章" style="color:#222222;text-decoration:none;padding:0 6px;">所有文章</a>
                        <a href="' . get_bloginfo('url') . '/个人介绍" title="关于博主" style="color:#222222;text-decoration:none;padding:0 6px;">关于博主</a>
                    </div>
                    <div class="clear" style="clear: both;display: block;"></div>
                </div>
                <div style="margin: 0;color: #2f2f2f; background: #fff;font-size: 20px;padding: 20px 0;text-align: center;border-bottom: 1px solid #eeeeee;">
';
$email_headertop = '
	<style>
		@media only screen and (max-width: 500px){.ititle{padding: 15px 0px 0 80px!important;}.imenu{display: none;}}
    </style>
    <div class="emailpaged" style="background-color: #f2f5f8;">
        <div class="emailcontent" style="width:96%;max-width:720px;text-align: left;margin: 0 auto;padding-top: 80px;padding-bottom: 20px">
            <div class="emailtitle">
                <div style="background: #fff;position: relative;margin:0;border-bottom: 1px solid #eeeeee;">
                    <div style="float: left;"><div style="height:60px;padding: 15px 0 0 20px;"><img src="https://www.wtdxz.com/wp-content/uploads/2022/09/2022090113275712.png"  title="' . get_option("blogname") . '" style="display:inline;margin:0;max-height:50px;width: auto;" border="0"></div></div>     
                    <div class="ititle" style="float: left;color: #2f2f2f;position: absolute;font-size: 17px;padding: 15px 160px 0 80px;line-height: 50px;height: 60px;">
';
 
/*---------------
**----标题空间----
**-------------*/
 
$email_headerbot_center = '
                </div>
                <div class="emailtext" style="background:#fff;padding:20px 32px 40px;">
';
$email_headerbot = '
                    </div>
                    <div class="imenu" style="float: right;position: absolute;right: 0;"><div style="height:60px;line-height:60px;padding: 10px 20px 0 0;font-size:12px;">
                        <a href="' . get_bloginfo('url') . '" title="' . get_option("blogname") . '" style="color:#222222;text-decoration:none;padding:0 6px;">首页</a>
                        <a href="' . get_bloginfo('url') . '/所有文章" title="所有文章" style="color:#222222;text-decoration:none;padding:0 6px;">所有文章</a> 
                        <a href="' . get_bloginfo('url') . '/个人介绍" title="关于博主" style="color:#222222;text-decoration:none;padding:0 6px;">关于博主</a>
                    </div></div>
                    <div class="clear" style="clear: both;display: block;"></div>
                </div>
                <div class="emailtext" style="background:#fff;padding:20px 32px 40px;">
';
if($style_center){
    define ('emailheadertop',  $email_headertop_center );
    define ('emailheaderbot', $email_headerbot_center );
}
else{
    define ('emailheadertop',  $email_headertop );
    define ('emailheaderbot', $email_headerbot );
}
 
//定义界面底部区域内容，---[请注意修改下面广告图片地址,不需要请删除 <div class="emailad" ......</div> 这 4 行]，下面标红处为广告图
$email_footer = '                
                <div class="emailad" style="margin-top: 18px;text-align:center;">
                    <a href="' . get_bloginfo('url') . '">
                        <img src="" alt="" style="margin: auto;width:100%;max-width:720px;height: auto;">
                    </a>
                </div>
                
                <div class="copyright" style="font-size:13px;line-height: 1.5;color: #777777;padding: 5px 0;text-align:center;">
                    <p style="margin:10px 0 0;">(此为系统自动发送邮件, 请勿回复！)</p>
                    <p style="margin:10px 0 0;"> '. date("Y") . '  邮件来自  <a href="' . get_bloginfo('url') . '" style="color:#51a0e3;text-decoration:none">' . get_option("blogname") . '</a></p>
                </div>
            </div>
        </div>
    </div>
';
define ('emailfooter', $email_footer );
 
//评论通过通知评论者
add_action('comment_unapproved_to_approved', 'iwill_comment_approved');
function iwill_comment_approved($comment) {
  if(is_email($comment->comment_author_email)) {
    $post_link = get_permalink($comment->comment_post_ID);
    // 邮件标题，可自行更改
    $title = '您在 [' . get_option('blogname') . '] 的评论已通过审核';
    // 邮件内容，按需更改。如果不懂改，可以给我留言
    $body = emailheadertop.'留言审核通过通知'.emailheaderbot.'
        <p style="color: #6e6e6e;font-size:13px;line-height:24px;">您在' . get_option('blogname') . '《<a href="'.$post_link.'">'.get_the_title($comment->comment_post_ID).'</a>》发表的评论：</p>
        <p style="color: #6e6e6e;font-size:13px;line-height:24px;padding: 15px 20px;background:#f8f8f8;margin:0px;border: 1px solid #eee;">'.$comment->comment_content.'</p>
        <p style="color: #6e6e6e;font-size:13px;line-height:24px;">已通过管理员审核并显示。<br />
        您可在此查看您的评论：<a href="'.get_comment_link( $comment->comment_ID ).'">前往查看</a></p>'.emailfooter;
    @wp_mail($comment->comment_author_email, $title, $body, "Content-Type: text/html; charset=UTF-8");        
  }
}
 
 
//WordPress 评论回复邮件通知代码
function comment_email_notify($comment_id) {
	$admin_email = get_bloginfo ('admin_email');
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
	$comment = get_comment($comment_id);
	$parent_id = $comment->comment_parent ? $comment->comment_parent : '';
	$spam_confirmed = $comment->comment_approved;
	global $wpdb;
	$comments_waiting = $wpdb->get_var("SELECT count(comment_ID) FROM $wpdb->comments WHERE comment_approved = '0'");
	if (($parent_id != '') && ($spam_confirmed != 'spam') && ($to != $admin_email)) {
		$wp_email = 'no-reply@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME']));
		$to = trim(get_comment($parent_id)->comment_author_email);
		$subject = '您在 [' . $blogname . ']' . ' 中的留言有了新回复！';
		$message = '<div style="background-color:white;border-left: 2px solid #555555;box-shadow:0 1px 3px #AAAAAA;line-height:180%;padding:0 15px 12px;width:500px;margin:50px auto;color:#555555;font-family:"Source Sans Pro","Hiragino Sans GB","Microsoft Yahei",SimSun,Helvetica,Arial,Sans-serif,monospace;font-size:14px;"> 
            <h2 style="border-bottom:1px solid #DDD;font-size:14px;font-weight:normal;padding:13px 0 10px 8px;"><span style="color: #f59200;font-weight: bold;">&gt; </span>您在 <a style="text-decoration:none; color:#f59200;font-weight:600;" href="' . home_url() . '">' . $blogname . '</a> 的留言有回复啦！</h2><div style="font-size: 14px; color: #777; padding: 0 10px; margin-top: 18px;">
			<p><b>' . trim(get_comment($parent_id)->comment_author)  . '</b> ：您曾在文章<b>《' . get_the_title($comment->comment_post_ID) . '》</b>上发表评论:</p>
			<p style="background: #F5F5F5; padding: 10px 15px; margin: 18px 0;">' . nl2br(strip_tags(get_comment($parent_id)->comment_content)) . '</p>
			<p>' . '<b>' . trim($comment->comment_author) . '</b>'. ' 给您的回复如下:</p>
			<p style="background: #F5F5F5; padding: 10px 15px; margin: 18px 0;">' . nl2br(strip_tags($comment->comment_content)) . '</p>
			<p>您可以点击 <a style="text-decoration:none; color:#f59200" href="' . htmlspecialchars(get_comment_link($parent_id)) . '">查看完整的回复內容</a>，也欢迎再次光临 <a style="text-decoration:none; color:#f59200"
			href="' . home_url() . '">' . $blogname . '</a>。祝您生活愉快！</p>
			<p style="padding-bottom: 15px;">(此邮件由系统自动发出,请勿直接回复!)</p></div></div></td></tr></tbody></table></div>';
		$from = "From: \"" . get_option('blogname') . "\" <$wp_email>";
		$headers = "$from\nContent-Type: text/html; charset=" . get_option('blog_charset') . "\n";
		wp_mail( $to, $subject, $message, $headers );
	}
 
	//文章有新评论时通知管理员
    if ($parent_id == '' && (trim($comment->comment_author_email) != trim($admin_email)) && ($spam_confirmed != 'spam') && ($comment->comment_approved != 0)){
        $wp_email = '';
        $subject = '在「' . $blogname .'」的文章《'. get_the_title($comment->comment_post_ID) .'》一文有新的评论';
        $message = '<div style="background-color:white;border-left: 2px solid #555555;box-shadow:0 1px 3px #AAAAAA;line-height:180%;padding:0 15px 12px;width:500px;margin:50px auto;color:#555555;font-family:"Source Sans Pro","Hiragino Sans GB","Microsoft Yahei",SimSun,Helvetica,Arial,Sans-serif,monospace;font-size:14px;"> 
            <h2 style="border-bottom:1px solid #DDD;font-size:14px;font-weight:normal;padding:13px 0 10px 8px;"><span style="color: #f59200;font-weight: bold;">&gt; </span><a style="text-decoration:none;color: #f59200;" href="' . home_url() . '">' . $blogname . '</a> 博客有新的评论啦！ </h2> 
            <div style="padding:0 12px 0 12px;margin-top:18px;"> 
            <p><b>'. $comment->comment_author . '</b> ：您在文章<b>《' . get_the_title($comment->comment_post_ID) . '》</b>上发表评论:</p> 
            <p style="background-color: #f5f5f5;border: 0px solid #DDD;padding: 10px 15px;margin:18px 0;">' . $comment->comment_content . '</p> 
            <p>您可以点击 <a style="text-decoration:none; color:#f59200" href="' . htmlspecialchars(get_comment_link($parent_id)) . '">查看完整的回复內容</a>，也欢迎再次光临 <a style="text-decoration:none; color:#f59200" href="' . home_url() . '">' . $blogname . '</a>。祝您生活愉快！</p></div></div>';
        $headers = "Content-Type: text/html; charset=" . get_option('blog_charset') . "\n";
        wp_mail( $admin_email, $subject, $message, $headers );
    }
 
	//评论需要审核时通知
    if ($parent_id == '' && (trim($comment->comment_author_email) != trim($admin_email)) && ($spam_confirmed != 'spam') && ($spam_confirmed != 'trash')  && ($comment->comment_approved == 0)){
        $wp_email = '';
        $subject = '在「' . $blogname .'」的文章《' . get_the_title($comment->comment_post_ID) . '》中有新的评论需要审核';
        $message = '<div style="background-color:white;border-left: 2px solid #555555;box-shadow:0 1px 3px #AAAAAA;line-height:180%;padding:0 15px 12px;width:500px;margin:50px auto;color:#555555;font-family:"Source Sans Pro","Hiragino Sans GB","Microsoft Yahei",SimSun,Helvetica,Arial,Sans-serif,monospace;font-size:14px;"> 
            <h2 style="border-bottom:1px solid #DDD;font-size:14px;font-weight:normal;padding:13px 0 10px 8px;"><span style="color: #f59200;font-weight: bold;">&gt; 「 </span><a style="text-decoration:none;color: #f59200;" href="' . home_url() . '">' . $blogname . '」</a> 中有一条评论等待您的审核 </h2> 
            <div style="padding:0 12px 0 12px;margin-top:18px;"> 
            <p><b>'. $comment->comment_author . '</b> ：您在文章<b><a style="text-decoration:none;color: #f59200;" href="' . get_permalink($comment->comment_post_ID) . '">《' . get_the_title($comment->comment_post_ID) . '》</a></b>上发表评论:</p> 
            <p style="background-color: #f5f5f5;border: 0px solid #DDD;padding: 10px 15px;margin:18px 0;">' . $comment->comment_content . '</p> 
            <p><a style="text-decoration:none;color: #12ADDB;" href="'. admin_url( "comment.php?action=approve&c={$comment_id}#wpbody-content" ) . '">[批准评论]</a> | <a style="text-decoration:none;color: #12ADDB;" href="'. admin_url( "comment.php?action=trash&c={$comment_id}#wpbody-content" ) . '">[移至回收站]</a>。您还可以：<a style="text-decoration:none; color:#12ADDB" href="' . admin_url( "comment.php?action=delete&c={$comment_id}#wpbody-content" ) . '">永久删除评论</a> | <a style="text-decoration:none;color: #12ADDB;" href="'. admin_url( "comment.php?action=spam&c={$comment_id}#wpbody-content" ) . '">标记为垃圾评论</a>
			<p>当前有 ' . $comments_waiting . ' 条评论等待审核。请移步<a style="text-decoration:none;color: #f59200;" href="' . admin_url('edit-comments.php?comment_status=moderated#wpbody-content') . '">审核页面</a>来查看。</p>也欢迎再次光临 <a style="text-decoration:none; color:#f59200" href="' . home_url() . '">' . $blogname . '</a>。祝您生活愉快！</p></div></div>';
        $headers = "Content-Type: text/html; charset=" . get_option('blog_charset') . "\n";
        wp_mail( $admin_email, $subject, $message, $headers );
    }
}
add_action('comment_post', 'comment_email_notify');
 
 
//用户更新账户通知用户
function users_profile_update( $user_id ) {
        $site_url = get_bloginfo('wpurl');
        $site_name = get_bloginfo('wpname');
        $user_info = get_userdata( $user_id );
        $to = $user_info->user_email;
        $subject = "".$site_name."账户更新";
        $message = emailheadertop.'您在' .$site_name. '账户资料修改成功！'.emailheaderbot.'<p style="color: #6e6e6e;font-size:13px;line-height:24px;">亲爱的 ' .$user_info->display_name . '<br/>您的资料修改成功!<br/>谢谢您的光临</p>'.emailfooter;
 
        wp_mail( $to, $subject, $message, "Content-Type: text/html; charset=UTF-8");
}
add_action( 'profile_update', 'users_profile_update', 10, 2);

?>