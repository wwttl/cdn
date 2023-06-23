<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Invalid request.' );
}
/**
 * 首页
 */
// if(!current_user_can('administrator')) wp_die('维护中...');
get_header();

?>

<?php do_action('b2_index_before'); ?>

<div class="b2-content">

    <div id="primary-home" class="content-area">

        <?php 
        do_action('my_index_top');
        do_action('b2_index');
        do_action('my_index_bottom'); ?>
    </div>

</div>

<?php do_action('b2_index_after'); ?>

<?php
get_footer();
