<?php
// 面包屑导航
if (lmy_get_option("crumbs")) {
function cmp_breadcrumbs() {
echo '<div class="post-breadcrumb b2-hover mg-b">当前位置：';
if (!is_home()) { //如果不是首页
echo '<a href="'.get_option("home_url").'">首页</a><span>&gt;</span>';
if (is_category() || is_single()){ 
    $cat_id = get_the_category()[0]->term_id;
                                    $if_parent = TRUE;
                                    $breadcrumb = "";
                                    while ($if_parent == TRUE) {
                                        $cat_object = get_category($cat_id);
                                        $cat = $cat_object->term_id;
                                        $categoryURL = get_category_link($cat);
                                        $name = $cat_object->name;
                                        $cat_id = $cat_object->parent;
                                        $add_link = '<a href="' . $categoryURL . '">' . $name . '</a>';
                                        $breadcrumb = substr_replace($breadcrumb, $add_link, 0, 0);
                                        if ($cat_id == 0) {
                                            $if_parent = FALSE;
                                        }
                                    }
                                    echo $breadcrumb;
if (is_single()) { //如果是文章页
echo '<span>&gt;</span><a href="'.get_permalink().'"> '.get_the_title().'</a>';
}
}
}
echo '</div>';
}
}