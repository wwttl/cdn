<?php
    /**
     *Template Name: 带框页面
    *
    *
    * 
    */
get_header();
?>
<?php
    $excerpt = get_post_field('post_excerpt');
?>
<style>
    body{margin:0; padding:0;}
</style>
<div class="b2-single-content">

        <div id="primary-home" class="wrapper">
            <article class="single-article b2-radius box">

                <?php do_action('b2_single_article_before'); ?>
    
                <header class="entry-header">
                <div class="entry-content">
                   <?php do_action('b2_single_post_content_before'); ?>
                    <?php if($excerpt){ ?>
                        <div class="content-excerpt">
                            <?php echo get_the_excerpt(); ?>
                        </div>
                    <?php } ?>
                    <?php the_content(); ?>
                    <?php
            			$post_links = wp_link_pages( array(
            				'before' => '<div class="post-links">',
            				'after'  => '</div>',
            				'link_before'=>'<button class="empty">',
            				'link_after'=>'</button>',
            				'echo'=>false
            			) );
            			if($post_links){
            				echo $post_links;
            			}
            		?>
                    <?php do_action('b2_single_post_content_after'); ?>
                 <!--主体开始-->
                <?php  while ( have_posts() ) : the_post();
                    the_content();
                endwhile; ?>
                 <!--主体结束-->
                <!--是否开启评论-->
                </article>
                 <?php  while ( have_posts() ) : the_post();
                if ( (comments_open() || get_comments_number()) && (int)b2_get_option('template_comment','comment_close') === 1) :
                    comments_template();
                endif;
                endwhile; ?>
            </div>
            
        </div>
    <!--</div>-->
 

<?php
get_footer();