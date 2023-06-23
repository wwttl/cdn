<?php
use B2\Modules\Common\Post;
/**
 * 文章内容页
 */
$post_id = get_the_id();
$post_meta = Post::post_meta($post_id);
$excerpt = get_post_field('post_excerpt');
$from_url = get_post_meta($post_id,'b2_post_from_url',true);
if(strpos($from_url,'http://') === false && strpos($from_url,'https://') === false){
    $from_url = 'https://'.$from_url;
}
$from_name = get_post_meta($post_id,'b2_post_from_name',true);
$down_open = get_post_meta($post_id,'b2_open_download',true);

?>
<article class="single-article b2-radius box">

    <?php do_action('b2_single_article_before'); ?>
    <?php if (lmy_get_option("crumbs")) {cmp_breadcrumbs();} ?>

    <header class="entry-header">
        <h1><?php echo get_the_title(); ?></h1>
        <div id="post-meta">
            <div class="post-meta-row">
                <ul class="post-meta">
                    <li>
                        <?php echo B2\Modules\Templates\Modules\Posts::get_post_cats('target="__blank"',$post_meta,array('cats'),'post_3'); ?>
                    </li>
                    <?php 
                        if($from_url && $from_name){
                    ?>
                        <li class="single-from"><span><?php echo __('来源：','b2'); ?><a href="<?php echo $from_url; ?>" target="_blank" rel="nofollow"><?php echo $from_name; ?></a></span></li>
                    <?php
                        }
                    ?>
                    <li class="single-date">
                        <span><?php echo $post_meta['date']; ?></span>
                    </li>
                    <li class="single-like">
                        <span><?php echo b2_get_icon('b2-heart-fill'); ?><b v-text="postData.up"></b></span>
                    </li>
                    <li class="single-eye">
                        <span><?php echo b2_get_icon('b2-eye-fill'); ?><b v-text="postData.views"></b></span>
                    </li>
                    <?php if (lmy_get_option('word_count')) {$text = '';echo count_words ($text); } ?>
                    <?php if (lmy_get_option('reading_time')) { reading_time(); }?>
                    <?php if (lmy_get_option('baidu_record')) { baidu_record_t(); }?>
                    <li class="single-edit" v-cloak v-if="userData.is_admin">
                        <a href="<?php echo get_edit_post_link($post_id); ?>" target="_blank"><?php echo __('编辑','b2'); ?></a>
                    </li>
                </ul>
                <?php if($down_open){ ?>
                    <div class="single-button-download"><button class="empty b2-radius" @click="scroll"><?php echo b2_get_icon('b2-download-cloud-line').__('前往下载','b2'); ?></button></div>
                <?php } ?>
            </div>
            <?php if(get_post_type() !== 'announcement'){ ?>
                <?php if(!is_audit_mode()){ ?>
                    <div class="post-user-info">
                        <div class="post-meta-left">
                            <a class="link-block" href="<?php echo $post_meta['user_link']; ?>"></a>
                            <div class="avatar-parent"><img class="avatar b2-radius" src="<?php echo $post_meta['user_avatar']; ?>" /><?php echo $post_meta['user_title'] ? $post_meta['verify_icon'] : ''; ?></div>
                            <div class="post-user-name"><b><?php echo $post_meta['user_name']; ?></b><span class="user-title"><?php echo $post_meta['user_title']; ?></span></div>
                        </div>
                        <div class="post-meta-right">
                            <div class="" v-if="self == false" v-cloak>
                                <button @click="followingAc" class="author-has-follow" v-if="following"><?php echo __('取消关注','b2'); ?></button>
                                <button @click="followingAc" v-else><?php echo b2_get_icon('b2-add-line').__('关注','b2'); ?></button>
                                <button class="empty" @click="dmsg()"><?php echo __('私信','b2'); ?></button>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
            
        </div>
    </header>
    <div class="entry-content">
        <?php do_action('b2_single_post_content_before'); ?>
        <?php if($excerpt){ ?>
            <div class="content-excerpt">
                <?php echo get_the_excerpt(); ?>
            </div>
        <?php } ?>
        <?php if ( lmy_get_option('all_more')&&( word_num() > 250 )) { ?>
        <input type="checkbox" id="contTab" checked="checked" class="tabbed">
        <div id="cont">
        <?php the_content(); ?>
        </div>
        <div class="content-more"><div class="gradient"></div> <label for="contTab" class="readmore">点击展开全文</label></div>
        <?php } else { ?>
        <?php the_content(); ?>
        <?php } ?>
        <?php if ( lmy_get_option('begin_today') ) { ?><?php echo begin_today(); ?><?php } ?>
        <?php
            global $page, $numpages, $multipage, $more;
            echo b2_pagenav(array('pages'=>$numpages,'paged'=>$page),true);
		?>
        <?php do_action('b2_single_post_content_after'); ?>
    </div>

    <?php do_action('b2_single_article_after'); ?>
</article>
<?php if ( lmy_get_option('authorCard') ) { ?>
<div class="postFooterInfo u-marginTop30 u-backgroundColorWhite b2-radius">
    <div class="u-flex">
        <div class="u-flex0"><a href="<?php echo $post_meta['user_link']; ?>" target="_blank"><img
                    src="<?php echo $post_meta['user_avatar']; ?>" alt=""
                    height="75" width="75" class="avatar"></a></div>
        <div class="u-flex1 u-paddingLeft15 u-overflowHidden">
            <div class="authorCard--content"><span class="authorCard--title u-flex"><a
                        href="<?php echo $post_meta['user_link']; ?>" target="_blank"><?php echo $post_meta['user_name']; ?></a> 
                        <?php if (get_the_author_meta('b2_title')!=""){ ?>
                                <i class="b2-vrenzhengguanli b2font b2-color" style="position: inherit;display: inherit;" title="<?php echo get_the_author_meta('b2_title'); ?>"></i>
                        <?php } ?>
                <div class="vxname">
                   <?php if (get_the_author_meta('vx')!=""){ ?>
                    <button class="button button--follow">关注公众号</button>
                    <?php echo "<img src='https://open.weixin.qq.com/qr/code?username=".get_the_author_meta('vx')."'> "; ?>
                  <?php } ?>
                  <?php if (get_the_author_meta('vx')==""){ ?>
                    <a href="<?php echo $post_meta['user_link']; ?>" class="button">个人主页</a>
                  <?php } ?>  
                  </div>
                  </span></div>
            <div class="authorCard--description">
                        <?php if (get_the_author_meta('description')!=""){ ?>
                            <?php  echo the_author_meta( 'description' ); ?>
                        <?php } ?>
                        <?php if (get_the_author_meta('description')==""){ ?>
                            这家伙很懒什么也没写！
                        <?php } ?>
                        
                       </div>
            <div class="authorCard--meta"><span class="authorInfo-item"><?php echo count_user_posts(get_post($id)->post_author, 'post' ); ?>篇作品</span></div>
        </div>
    </div>
</div>
<?php } ?>