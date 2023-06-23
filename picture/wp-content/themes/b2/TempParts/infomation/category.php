<?php
use B2\Modules\Common\Infomation;
/**
 * 供求
 */
get_header();

$term = get_queried_object();

if(!isset($term->term_id)){
    wp_safe_redirect(B2_HOME_URI.'/404');
    exit;
}

$term_id = $term->term_id;

$name = b2_get_option('normal_custom','custom_infomation_name');
$for = b2_get_option('normal_custom','custom_infomation_for');
$get = b2_get_option('normal_custom','custom_infomation_get');
$paged = get_query_var('paged');
$paged = $paged ? $paged : 1;

$count = b2_get_option('infomation_main','infomation_per_count');
$offset = ($paged -1)*$count;

?>
<div class="b2-single-content wrapper mg-b">

    <div id="primary-home" class="content-area b2-infomation" ref="b2infomation" data-paged="<?php echo $paged; ?>" data-term="<?php echo $term_id; ?>" data-count="<?php echo $count; ?>">
        <div class="infomation-list box b2-radius">
        <div class="infomation-breadcrumb b2-hover mg-b">
            <?php echo Infomation::link_breadcrumb(); ?>
        </div>
            <div class="infomation-top-box">
                <div class="info-h1">
                    <h1><?php echo $term->name; ?></h1>
                    <a href="<?php echo b2_get_custom_page_url('po-infomation');?>" class="button" target="_blank">发布</a>
                </div>
                <?php echo $term->description ? '<div class="infomation-desc">'.$term->description.'</div>' : ''; ?>
            </div>
            <?php get_template_part( 'TempParts/infomation/archive'); ?>
        </div>

        <div class="circle-list">
            <ul>
                <?php 
                    $_pages = 0;
                    $args = [
                        'offset'=>$offset,
                        'post_status'=>'publish',
                        'include_children' => true,
                        'posts_per_page'=>$count,
                        'post_type'=>'infomation',
                        'tax_query' => array(
                            array (
                                'taxonomy' => 'infomation_cat',
                                'field' => 'term_id',
                                'terms' => $term_id
                            )
                        ),
                        'meta_query'=>array(
                            'relation' => 'AND',
                            array(
                                'key'     => 'b2_infomation_sticky',
                                'type' => 'NUMERIC'
                            ),
                        ),
                        'orderby'=>['meta_value_num' => 'DESC','date' => 'DESC']
                    ];

                    $topic_query = new \WP_Query( $args );

                    if ( $topic_query->have_posts()) {
                        $_pages = $topic_query->max_num_pages;
                        while ( $topic_query->have_posts() ) {
                            $topic_query->the_post();

                            get_template_part( 'TempParts/infomation/infomation','item');

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
    </div>

    <?php 
        get_sidebar(); 
    ?>

</div>
<?php
get_footer();