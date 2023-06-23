<style>.siteCount {
    background: url('<?php echo lmy_get_option('siteCount_img'); ?>') center center / cover no-repeat fixed;
}</style>
<div class="siteCount">
    <div class="cover"></div>
    <div class="wrapper">
	    <ul>
	        <li>
	            <span><?php $users = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users"); echo $users; ?></span>
	            <b>用户总数</b>
	        </li>
	        <li>
	            <span><?php $count_posts = wp_count_posts(); echo $published_posts =$count_posts->publish;?></span>
	            <b>文章总数</b>
	        </li>
	        <li>
	            <span><?php echo nd_get_all_view(); ?></span>
	            <b>浏览总数</b>
	        </li>
	        <li>
	            <span><?php echo nd_get_24h_post_count(); ?></span>
	            <span><?php // echo nd_get_week_post_count(); ?></span>
	            <b>今日发布</b>
	        </li>
	        <li>
	            <span><?php $siteCount_time=lmy_get_option('siteCount_time'); echo floor((time()-strtotime($siteCount_time))/86400); ?></span>
	            <b>稳定运行</b>
	        </li>
	    </ul>
	    <div class="join-vip">
	        <a class="b2-radius" href="/vips" target="_blank">立即加入</a>
	        <p>加入<?php echo get_bloginfo('name'); ?>VIP，快速免费获取优质资源！</p>
	    </div>
    </div>
</div>