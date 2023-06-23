<?php
/**
 * 问答首页
 */
get_header();
$paged = get_query_var('paged');
$paged = $paged ? $paged : 1;
$count = b2_get_option('ask_main','ask_page_count');
$offset = ($paged -1)*$count;
$name = b2_get_option('ask_main','ask_title');
?>

<?php do_action('b2_ask_home_top'); ?>

<div class="b2-single-content wrapper mg-b">

    <?php do_action('b2_ask_home_before'); ?>

    <div id="primary-home" class="content-area wrapper box ask-archive b2-radius" ref="askarchive" data-paged="<?php echo $paged; ?>" data-count="<?php echo $count; ?>" data-term="0">

        <?php
            do_action('b2_ask_archive_content_before');
        ?>
         <div class="ask-top">
            <div class="ask-search-box">
                <div>
                    <h1 class="ask-h1"><?php echo $name;?></h1>
                </div>
                <div class="ask-search-input"><input type="text" v-on:keyup.enter="search" v-model="opt.s" placeholder="<?php echo __('搜索','b2'); ?>"/><button class="text" @click="search"><?php echo __('搜索','b2'); ?></button></div>
            </div>
            <div class="ask-bar b2flex">
                <div>
                    <span :class="{'picked':opt.type == 'hot'}" @click="fliter('hot')"><?php echo __('热门','b2'); ?></span>
                    <span :class="{'picked':opt.type == 'last'}" @click="fliter('last')"><?php echo __('最新','b2'); ?></span>
                    <span :class="{'picked':opt.type == 'waiting'}" @click="fliter('waiting')"><?php echo __('等待回答','b2'); ?></span>
                </div>
                <div>
                    <a href="<?php echo b2_get_custom_page_url('po-ask'); ?>" target="_blank" class="b2-color button empty b2flex"><?php echo b2_get_icon('b2-user-location-line').__('提问','b2').b2_get_icon('b2-arrow-right-s-line'); ?></a>
                </div>
            </div>
        </div>
        <?php
            get_template_part( 'TempParts/Ask/archive');

            do_action('b2_ask_archive_content_after');
        ?>
        <pagenav-new class="ask-list-nav" ref="infonav" navtype="post" :pages="opt['pages']" type="p" box=".ask-list-box" :opt="opt" :api="api" :rote="true" url="<?php echo get_post_type_archive_link('ask'); ?>" title="<?php echo $name; ?>" @return="get"></pagenav-new>
    </div>
    
    <?php 
        get_sidebar(); 
    ?>

</div>
<div class="ask-list-b">
    <ul>
        <?php 
            $_pages = 0;
            $args = [
                'offset'=>$offset,
                'post_status'=>'publish',
                'include_children' => true,
                'posts_per_page'=>$count,
                'post_type'=>'ask'
            ];

            $topic_query = new \WP_Query( $args );

            if ( $topic_query->have_posts()) {
                $_pages = $topic_query->max_num_pages;
                while ( $topic_query->have_posts() ) {
                    $topic_query->the_post();

                    get_template_part( 'TempParts/Ask/ask','item');

                }
                
            }
            wp_reset_postdata();
        ?>
    </ul>
    <?php
        $pagenav = b2_pagenav(array('pages'=>$_pages,'paged'=>$paged)); 
        if($pagenav){
            echo '<div class="b2-pagenav collection-nav post-nav box">'.$pagenav.'</div>';
        }
    ?>
</div>
<?php
get_footer();