<?php
/**
 * 存档页面
 */
get_header();

$term = get_queried_object();

$show_sidebar = isset($term->term_id) ? get_term_meta($term->term_id,'b2_show_sidebar',true) : false;

$tax = isset($term->taxonomy) ? $term->taxonomy : 'normal';

?>

<?php do_action('b2_archive_'.$tax.'_top'); ?>

<div class="b2-single-content wrapper <?php echo $tax; echo $show_sidebar ? ' single-sidebar-show' : ' single-sidebar-hidden'; ?> ">

    <?php do_action('b2_archive_'.$tax.'_before'); ?>

    <div id="primary-home" class="content-area">

            <?php

                do_action('b2_archive_'.$tax.'_content_before');
                
                get_template_part( 'TempParts/Archive/content',$tax);

                do_action('b2_archive_'.$tax.'_content_after');

            ?>

    </div>

    <?php do_action('b2_archive_'.$tax.'_after'); ?>

    <?php 
        if($show_sidebar)
        get_sidebar(); 
    ?>

</div>
<?php
get_footer();