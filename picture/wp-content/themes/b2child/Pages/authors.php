<?php
/**
 *Template Name: 用户展示
 * Description:  A waterfall page
*/
$key = get_search_query();

$count = 20;

$paged = get_query_var('paged') ? get_query_var('paged') : 1;
$offset = ($paged -1)*$count;

    $users = new \WP_User_Query( array(
        'search'         => '*'.$key.'*',
        'search_columns' => array(
            'display_name',
            'user_id'
        ),
        'number'=>$count,
        'offset'=>$offset
    ) );

$users_found = $users->get_results();
$total = $users->get_total();
$pages = ceil($total/$count);
get_header();
?>
<link rel='stylesheet' href='<?php echo B2_CHILD_URI ?>/Assets/Css/users.css' type='text/css' media='all' />
<div class="collection-top mg-t- mg-b" style="background-image:url(<?php echo B2_CHILD_URI ?>/Assets/images/banner3_bg.jpg">
    <h1><?php echo get_the_title(); ?></h1>
    <p>网站所有用户展示页面</p>
</div>
<div class="htmleaf-container">
<div class="ah-tab-content-wrapper">
	<div class="ah-tab-content" data-ah-tab-active="true">
		<div id="user-list" class="">
       <?php $ids = array(); if($users_found){
            echo '<ul>';
            
            foreach ($users_found as $user) {
                $user_data = B2\Modules\Common\User::get_user_public_data($user->ID);
                $user_lv = B2\Modules\Common\User::get_user_lv($user->ID);
                $user_vip = isset($user_lv['vip']['icon']) ? $user_lv['vip']['icon'] : '';
                $user_lv = isset($user_lv['lv']['icon']) ? $user_lv['lv']['icon'] : '';

                $tips = B2\Modules\Common\Comment::get_tips();
                $tips = $tips['title'] ? $tips['title'] : __('这家伙很懒，什么都没留下','b2');

                $following = get_user_meta($user->ID,'zrz_follow',true);
                $following = is_array($following) ? count($following) : 0;
                
                $followers = get_user_meta($user->ID,'zrz_followed',true);
                $followers = is_array($followers) ? count($followers) : 0;
                $ids[] = $user->ID;

                echo '	<li>
							<article class="user_main">
								<div class="left">
									<div class="img">
										<img class="avatar" src="'.$user_data['avatar'].'" style="display: inline-block;">
									</div>
									<div class="author_btn" > 
										 <button @click="dmsg('.$user->ID.')">'.__('私信','b2').'</button>
									</div>
								</div>
								<div class="right">
									<a href="'.$user_data['link'].'">
										<h4>'.$user_data['name'].''.$user_vip.$user_lv.'</h4>
									
									</a>
									<div class="post">
										<span>文章数<b>'.count_user_posts($user->ID,'post').'</b></span>
										<span>评论<b>'.B2\Modules\Common\Comment::get_user_comment_count($user->ID).'</b></span>
										<span>关注<b>'.$following.'</b></span>
										<span>粉丝<b>'.$followers.'</b></span>
									</div>
									<p class="desc"> '.$user_data['desc'].'</p>
								</div>
							</article>
						</li>';
            }
            echo '</ul>';  
        ?>

        <?php }else{
            echo B2_EMPTY;
        } 
        unset($users_found);
        unset($users);
        wp_localize_script( 'b2-js-main', 'b2_search_data', array(
            'users'=>$ids
        ))
        ?>  </div>
    </div>
    <?php do_action('b2_normal_archive_after'); ?>
<?php if($pages > 1){ ?>
    <div class="b2-pagenav post-nav">
        <?php echo b2_pagenav(array('pages'=>$pages,'paged'=>$paged)); ?>
    </div>
<?php } ?>
	       <div class="ah-tab-content">
  <div id="gold-top" class="content-area gold-page wrapper" style="width:950px">
		<main id="main" class="site-main box b2-radius" ref="goldTop" data-user="<?php echo $user_id; ?>" data-paged="<?php echo $paged; ?>" data-type="<?php echo $type; ?>" data-url="<?php echo b2_get_custom_page_url('gold'); ?>">
            <div class="custom-page-content">
                <div class="button empty b2-loading empty-page text" v-if="data === ''"></div>
                <div class="gold-top-list" v-else-if="data != '' && Object.keys(data).length > 0" v-cloak>
                    <ul>
                        <li v-for="(item,index) in data" style="width:100%">
                            <div class="gold-top-avatar">
                                <a :href="item.link" target="_blank"><img class="avatar b2-radius" :src="item.avatar" /></a>
                                <span v-if="item.user_title" v-html="item.verify_icon"></span>
                            </div>
                            <div class="gold-top-info">
                                <div class="gold-top-info-left" style="margin-left: 15px;">
                                    <h2><span v-text="'No.'+(index+1)"></span><a :href="item.link" target="_blank" v-text="item.name"></a><b v-if="item.user_title"><?php echo __('认证会员','b2'); ?></b></h2>
                                    <div class="gold-top-desc">
                                        <p v-if="item.desc" v-text="item.desc"></p>
                                        <p v-else="item.desc" v-text="item.desc"></p>
                                    </div>
                                </div>
                                <div class="gold-top-credit"><span class="user-credit"><?php echo b2_get_icon('b2-jifen'); ?>{{item.credit}}</span></div>
                            </div>
                        </li>
                    </ul>
                    <div class="gold-top-num"><?php echo __('前20名','b2'); ?></div>
                </div>
                <div v-else v-cloak>
                    <?php echo B2_EMPTY; ?>
                </div>
                <!-- <page-nav ref="goldNav" paged="<?php echo $paged; ?>" navtype="json" :pages="pages" type="p" :box="selecter" :opt="opt" :api="api" :url="url" title="<?php echo __('财富排行','b2'); ?>" @return="get"></page-nav> -->
            </div>
		</main>
    </div>
	         </div>
	         <div class="ah-tab-content">
	         	<div id="verifys" ref="verify">
                        <div class="" v-if="users === ''">
                            <div class="button empty b2-loading empty-page text"></div>
                        </div>
                        <div class="verify-none" v-else-if="users.length == 0" v-cloak>
                            <span v-cloak><?php echo __('暂无认证用户','b2'); ?></span>
                        </div>
                        <ul v-else-if="users.length > 0" v-cloak>
                            <li v-for="user in users">
                                <article class="user_main">
                                    <div class="left">
									    <div class="img">
                                        <a :href="user.link" target="_blank"><img :src="user.avatar" class="b2-radius avatar" /><span v-html="user.verify_icon"></span></a>
                                        </div>
                                    </div>
                                    <div class="right">
                                    <div class="post">
                                    <span style="color: #333;font-size: 15px;padding: 10px;font-family: cursive;font-weight: 700;">昵称：<a :href="user.link" target="_blank">{{user.name}}</a></span></br>
                                    <span style="color: #333;font-size: 15px;padding: 10px;font-family: cursive;font-weight: 700;">称号：<a :href="user.link" target="_blank">{{user.user_title ? user.user_title : (user.desc ? user.desc : '<?php echo __('没有称号','b2'); ?>')}}</a></span></div></div></div>
                                </article>
                            </li>
                        </ul>
                </div>
	        </div>
</div>
</div>

<?php get_footer(); ?>