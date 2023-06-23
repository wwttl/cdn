<?php 
    $pizza =lmy_get_option('one_index_user_id');
    $hotuser_title=lmy_get_option('one_index_user_title');
    $hotuser_desc=lmy_get_option('one_index_user_desc');  
    $one_index_user_all=lmy_get_option('one_index_user_all');
    $hotuser_all=get_permalink($one_index_user_all);
    $user_ID=array(
    'id' => explode(",", $pizza),
	); //调用用户ID
?>
<div id="Onecad_hotuser" class="home_row module-posts ">
    <div id="user-list" ref="searchUser">
        <div class="wrapper" >
            <div class="post-modules-top ">
                <div class="modules-title-box">
                    <div class="Onecad_title post-list">
                        <h2 class="module-title"><?php echo $hotuser_title ?></h2>
                        <div><?php echo $hotuser_desc ?></div>
                    </div>
                </div>
                <div class="post-list-cats post-list-cats-has-title">
                    <div class="post-carts-list-row">
                        <a href="<?php echo $hotuser_all ?>" class="cat-list post-load-button post-load-button-more">
                            <span data-type="cat">
                                全部<i class="b2font b2-arrow-right-s-line "></i>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="hotuser-container">
                <div class="demo">
                        <?php 
                        $hotuser_a ='';
                        echo '<div class="home-section-designs" id="h_designer"> 
                             <div class="items"> ';
                        if (lmy_get_option('one_index_user_ys')){
                            foreach ($user_ID['id'] as $user) {
                                $user_data = B2\Modules\Common\User::get_user_public_data($user);
                                $user_lv = B2\Modules\Common\User::get_user_lv($user);
                                $user_vip = isset($user_lv['vip']['icon']) ? $user_lv['vip']['icon'] : '';
                                $user_lv = isset($user_lv['lv']['icon']) ? $user_lv['lv']['icon'] : '';
                                $tips = __('这个人很懒，什么都没有留下！','b2');
                                $followers = get_user_meta($user,'zrz_followed',true);
                                $followers = is_array($followers) ? count($followers) : 0;
                                $following = get_user_meta($user,'zrz_follow',true);
                                $following = is_array($following) ? count($following) : 0;
                                $title = get_user_meta($user,'b2_title',true);
                                $desc = get_the_author_meta( 'description', $user );
                                $ids[] = $user;
                    			$hotuser_a .= '<div class="item-wrap">
                    							<div class="our-team b2-radius">
                    								<div class="pic">
                                                        
                                                        <img class="avataruser b2-radius lazy" data-src="'.$user_data['avatar'].'" alt="'.$user_data['name'].'" src="'.$user_data['avatar'].'">
                    								</div>
                    								<div class="i-content">
                    									<div class="user-s-info-name">
                    										<h3 class="title">'.$user_data['name'].'
                    										'.($user_data['user_title'] ? $user_data['verify_icon'] : '').'
                    										</h3>
                    										
                    										<p>
                    											<span class="lv-icon user-lv b2-lv0">
                    												<b>'.$user_vip.'</b>
                    												'.$user_lv.'
                    											</span>
                    											
                    										</p>
                    									</div>
                    									<div class="user-s-data">
                    										<div>
                                                            <span>'.__('文章','b2').'</span>
                                                            <p>'.count_user_posts($user,'post').'</p>
                    										</div>
                    										<div>
                    											<span>'.__('评论','b2').'</span>
                    											<p>'.B2\Modules\Common\Comment::get_user_comment_count($user).'</p>
                    										</div>
                    										<div>
                    											<span>粉丝</span>
                    											 <p>'.$followers.'</p>
                    										</div>
                    										<div>
                    											<span>关注</span>
                    											<p>'.$following.'</p>
                    										</div>
                    									</div>
                    									<div class="user-s-info-desc">
                                                        '.($user_data['user_title'] ? $user_data['user_title'] : ($user_data['desc'] ? $user_data['desc'] : $tips)).'
                    									</div>
                    								</div>
                    								<ul class="social">
                    									<div class="user-s-follow">
                                                <a href="'.$user_data['link'].'" class="link-block">个人主页</a>
                    									</div>
                    								</ul>
                    							</div>
                    						</div>';
                        }
                        echo $hotuser_a;
                        }else {
                                foreach ($user_ID['id'] as $user) {
                                        $user_data = B2\Modules\Common\User::get_user_public_data($user);
                                        $user_lv = B2\Modules\Common\User::get_user_lv($user);
                                        $user_vip = isset($user_lv['vip']['icon']) ? $user_lv['vip']['icon'] : '';
                                        $user_lv = isset($user_lv['lv']['icon']) ? $user_lv['lv']['icon'] : '';
                                        $tips = __('这个人很懒，什么都没有留下！','b2');
                                        $followers = get_user_meta($user,'zrz_followed',true);
                                        $followers = is_array($followers) ? count($followers) : 0;
                                        $following = get_user_meta($user,'zrz_follow',true);
                                        $following = is_array($following) ? count($following) : 0;
                                        $title = get_user_meta($user,'b2_title',true);
                                        $desc = get_the_author_meta( 'description', $user );
                                        $ids[] = $user;
                                        $hotuser_a .= '
                                              <div class="item-wrap"> 
                                               <div class="item b2-radius"> 
                                                <a href="'.$user_data['link'].'" target="_blank"> 
                                                 <div class="item-thumb "> 
                                                  <i class="thumb " style="background-image:url('.$user_data['avatar'].')"></i> 
                                                 </div> 
                                                 <div class="item-main"> 
                                                  <h2>'.$user_data['name'].''.$user_vip.''.$user_lv.'</h2> 
                                                  <div class="one_list_a" >'.($user_data['user_title'] ? $user_data['user_title'] : ($user_data['desc'] ? $user_data['desc'] : $tips)).'</div> 
                                                 </div></a> 
                                               </div> 
                                              </div> ';
                                }
                            echo $hotuser_a;
                        }
                        echo '</div></div>';
                        wp_localize_script( 'b2-js-main', 'b2_search_data', array('users'=>$ids));
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

