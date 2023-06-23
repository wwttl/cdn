<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if (lmy_get_option('add_link')) {
	if( isset($_POST['b2child_form']) && $_POST['b2child_form'] == 'send'){
		global $wpdb;

		$link_name = isset( $_POST['b2child_name'] ) ? trim(htmlspecialchars($_POST['b2child_name'], ENT_QUOTES)) : '';
		$link_url = isset( $_POST['b2child_url'] ) ? trim(htmlspecialchars($_POST['b2child_url'], ENT_QUOTES)) : '';
		$link_description = isset( $_POST['b2child_description'] ) ? trim(htmlspecialchars($_POST['b2child_description'], ENT_QUOTES)) : '';
		$link_notes = isset( $_POST['link_notes'] ) ? trim(htmlspecialchars($_POST['link_notes'], ENT_QUOTES)) : '';
		$link_target = "_blank";
		$link_visible = "N";

		if ( empty($link_name) || mb_strlen($link_name) > 20 ){
			wp_die('网站名称必须填写，且长度不得超过30字<a href="'.get_permalink( lmy_get_option('link_url') ).' "><p class="link-return">重写</p></a>');
		}

		if ( empty($link_description) || mb_strlen($link_description) > 100 ){
			wp_die('网站描述必须填写，且长度不得超过100字<a href="'.get_permalink( lmy_get_option('link_url') ).' "><p class="link-return">重写</p></a>');
		}

		if ( empty($link_notes)){
			wp_die('QQ必须填写<a href="'.get_permalink( lmy_get_option('link_url') ).' "><p class="link-return">重写</p></a>');
		}

		if ( empty($link_url) || strlen($link_url) > 60 || !preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $link_url)){
			wp_die('网站链接必须填写<a href="'.get_permalink( lmy_get_option('link_url') ).' "><p class="link-return">重写</p></a>');
		}

		$lkname = $link_name.' — 待审核';
		$lk_name = $wpdb->get_row("select * from $wpdb->links  where link_name ='$lkname'");
		if ($lk_name){
			wp_die('网站名称已经存在请勿重复申请！！！<a href="'.get_permalink( lmy_get_option('link_url') ).' "><p class="link-return">重写</p></a>');
		}

		$lk_url =  $wpdb->get_row("select * from $wpdb->links  where link_url ='$link_url'");
		if ($lk_url){
			wp_die('网站链接已经存在请勿重复申请！！！<a href="'.get_permalink( lmy_get_option('link_url') ).' "><p class="link-return">重写</p></a>');
		}

		$sql_link = $wpdb->insert(
		$wpdb->links,
			array(
				'link_name' => $link_name.' — 待审核',
				'link_url' => $link_url,
				'link_target' => $link_target,
				'link_description' => $link_description,
				'link_notes' => $link_notes,
				'link_visible' => $link_visible
			)
		);

		$result = $wpdb->get_results($sql_link);
		/**添加执行挂钩 */
        do_action('ajax_frontend_links_submit_success', $_POST);
		wp_die('提交成功，等待站长审核中！<a href="'.get_permalink( lmy_get_option('link_url') ).' "><p class="link-return">返回</p></a>');
	}
}
?>
<?php
/*
Template Name: 友情链接页面
*作者：不错吧
*作者网站：www.wwttl.com
*
*/
get_header();
$faviconApi = "https://favicon.cccyun.cc/";
$default_b2img = B2_THEME_URI . '/Assets/fontend/images/default-avatar.png';
?>
<style type="text/css">h3{
    font-size:1.1rem;
    margin-top:20px
}
strong{
    font-weight:bolder
}
.contextual-callout p{
    font-size:13px
}
.link-header h1{
    font-size:1.6rem;
    line-height:30px;
    text-align:center;
    margin:0 0 15px 0
}
.link-page{
    margin:30px 0
}
.mb-3,.my-3{
    margin-bottom:1rem!important
}
.row{
    display:-ms-flexbox;
    display:flex;
    -ms-flex-wrap:wrap;
    flex-wrap:wrap;
    margin-right:-15px;
    margin-left:-15px
}
.col{
    position:relative;
    width:100%;
    padding-right:15px;
    padding-left:15px
}
.col-6{
    -ms-flex:0 0 50%;
    flex:0 0 50%;
    max-width:50%
}
.url-card .url-body{
    transform:translateY(0px);
    -webkit-transform:translateY(0px);
    -moz-transform:translateY(0px);
    -webkit-transition:all .3s ease;
    -moz-transition:all .3s ease;
    -o-transition:all .3s ease;
    transition:all .3s ease
}
.url-card .url-body:hover{
    transform:translateY(-6px);
    -webkit-transform:translateY(-6px);
    -moz-transform:translateY(-6px);
    box-shadow:0 26px 40px -24px rgba(0,36,100,0.30);
    -webkit-box-shadow:0 26px 40px -24px rgba(0,36,100,0.30);
    -moz-box-shadow:0 26px 40px -24px rgba(0,36,100,.3)
}
.card,.block{
    background:#fff;
    border-width:0;
    margin-bottom:1rem;
    box-shadow:0 3px 5px rgb(32 160 255 / 15%);
    transition:background-color .3s
}
.card{
    position:relative;
    display:-ms-flexbox;
    display:flex;
    -ms-flex-direction:column;
    flex-direction:column;
    min-width:0;
    word-wrap:break-word;
    background-color:#fff;
    background-clip:border-box;
    border-radius:.25rem
}
.url-card .card-body{
    padding:.938rem;
    z-index: 1
}
.card-body{
    -ms-flex:1 1 auto;
    flex:1 1 auto;
    min-height:1px;
    padding:1.25rem
}
.d-flex{
    display:-ms-flexbox!important;
    display:flex!important
}
.align-items-center{
    -ms-flex-align:center!important;
    align-items:center!important
}
.url-card .url-img{
    width:40px;
    height:40px;
    -webkit-box-flex:0;
    -ms-flex:none;
    flex:none;
    background:rgba(128,128,128,.1);
    overflow:hidden
}
.mr-2,.mx-2{
    margin-right:.5rem!important
}
.align-items-center{
    -ms-flex-align:center!important;
    align-items:center!important
}
.justify-content-center{
    -ms-flex-pack:center!important;
    justify-content:center!important
}
.rounded-circle{
    border-radius:50%!important
}
.url-card .url-img>img{
    max-height:100%;
    vertical-align:unset
}
.url-card .url-info{
    overflow:hidden;
    padding-right:5px
}
.flex-fill{
    -ms-flex:1 1 auto!important;
    flex:1 1 auto!important
}
.overflowClip_1{
    overflow:hidden;
    -o-text-overflow:ellipsis;
    text-overflow:ellipsis;
    word-break:break-all;
    display:-webkit-box!important;
    -webkit-line-clamp:1;
    -webkit-box-orient:vertical
}
.overflowClip_1{
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
    display:block!important
}
.text-sm{
    font-size:.875rem!important
}
.text-xs{
    font-size:.75rem!important
}
.overflowClip_1 a{
    color:#282a2d;
    outline:0!important;
    text-decoration:none
}
@media(min-width:768px){
    .col-md-3{
        -ms-flex:0 0 25%;
        flex:0 0 25%;
        max-width:25%
    }
}
@media(max-width:767.98px){
    .row [class*="col-"]{
        padding-right:.5rem;
        padding-left:.5rem
    }
}
.button-yl{
    float: right;
}
.link-yl-jj{
    background-color: #f1404b1f;
    padding: 15px;
    margin: 10px 0 20px;
    border-radius: 3px;
    border: 1px solid rgba(241,64,75,0.07);
}
.link-yl-ps{
    background-color: #f1404b1f;
    padding: 15px;
    margin: 10px 0 20px;
    border-radius: 3px;
    border: 1px solid rgba(241,64,75,0.07);
}
.show-modalyl {
    opacity: 1;
    visibility: visible;
    transform: perspective(1px) scale(1.0);
    transition: visibility 0s linear 0s, opacity 0.15s 0s, transform 0.15s;
    backdrop-filter: saturate(97%) blur(5px);
    }
    .yltc{
        display: none;
    }
</style>
    <div class="b2-single-content wrapper">
    <div id="primary-home" class="content-area" style="max-width:100%">
        <article class="single-article b2-radius box">
            <main class="site-main">
                <?php while (have_posts()) : the_post(); ?>
                <div class="button-yl">
                            <button onclick="document.getElementById('popDiv').style.display='block'" style="width:auto;">申请友链</button>
                        </div>
                    <article id="post-<?php the_ID(); ?>" class="type-post post">
                        <h1 class="h2 mb-4"><?php echo get_the_title() ?></h1>
                        <div class="content">
                            <div class="single-content">
                                <?php if (lmy_get_option('add_link_content')) { ?>
                                    <h3>一、申请友链可以直接在本页面留言，内容包括网站名称、链接以及相关说明，为了节约你我的时间，可先做好本站链接并此处留言，我将尽快答复</h3>
                                    <h3>二、欢迎申请友情链接，只要是正规站常更新即可，申请首页链接需符合以下几点要求：</h3>
                                    <ul>
                                        <li>本站优先招同类原创、内容相近的博客或网站；</li>
                                        <li>Baidu和Google有正常收录，百度近期快照，不含有违法国家法律内容的合法网站，TB客，垃圾站不做。</li>
                                        <li>如果您的站原创内容少之又少，且长期不更新，申请连接不予受理！</li>
                                        <li>友情链接的目的是常来常往，凡是加了友链的朋友，我都会经常访问的，也欢迎你来我的网站参观、留言等。</li>
                                    </ul>
                                    <p>长期不更新的会视情节把友链转移至内页。</p>
                                    <p><b>本站信息示例：</b></p>
<ul>
<li>名称：<?php echo get_bloginfo('name') ?></li>
<li>简介：<?php echo get_bloginfo('description') ?></li>
<li>链接：<a href="<?php echo esc_url(home_url()) ?>"><?php echo esc_url(home_url()) ?></a></li>
<li>logo：<a href="<?php echo esc_url(home_url()) ?>"><?php echo esc_url(home_url()) ?>/favicon.ico</a></li>
                                    
                                    <p>PS:链接由于无法访问或您的博客没有发现本站链接等其他原因，将会暂时撤销超链接，恢复请留言通知我，望请谅解，谢谢！</p>

                                <?php } ?>
                                <?php the_content(); ?>
                            </div> <!-- .single-content -->
                            <article class="link-page">

                                <?php $default_ico = get_template_directory_uri() . '/Assets/fontend/images/default-avatar.png';
                                $linkcats = get_terms('link_category');
                                if (!empty($linkcats)) {
                                    foreach ($linkcats as $linkcat) {
                                        echo '<div class="link-title mb-3"><h3 class="link-cat"><i class="b2font b2-hashtag"></i>' . $linkcat->name . '</h3></div>';
                                        $bookmarks = get_bookmarks(array(
                                            'orderby' => 'rating',
                                            'order' => 'asc',
                                            'category' => $linkcat->term_id,
                                        ));
                                        echo '<div class="row">';
                                        foreach ($bookmarks as $bookmark) {
                                            $ico = $faviconApi . $bookmark->link_url;
                                            ?>
                                            <div class="url-card col col-6 col-md-3">
                                                <div class="card url-body default">
                                                    <span class="insert-post-bg">
                                                        <picture class="picture"><source type="image/webp" srcset="<?php echo $ico; ?>"><img
                                                                    alt="<?php echo $bookmark->link_name; ?>" class="b2-radius lazy" data-src="<?php echo $ico; ?>"
                                                                    src="<?php echo $default_b2img; ?>" data-was-processed="false">
                                                        </picture>
                                                    </span>
                                                    <div class="card-body">
                                                        <div class="url-content d-flex align-items-center">
                                                            <div class="url-img rounded-circle mr-2 d-flex align-items-center justify-content-center">
                                                                <img class="lazy" src="<?php echo $default_b2img; ?>" data-src="<?php echo $ico; ?>"
                                                                     onerror="javascript:this.src='<?php echo $default_ico; ?>'" alt="<?php echo $bookmark->link_name; ?>">
                                                            </div>
                                                            <div class="url-info flex-fill">
                                                                <div class="text-sm overflowClip_1">
                                                                    <a href="<?php echo $bookmark->link_url; ?>"
                                                                       title="<?php echo $bookmark->link_name; ?>"
                                                                       target="_blank"><strong><?php echo $bookmark->link_name; ?></strong></a>
                                                                </div>
                                                                <p class="overflowClip_1 m-0 text-xs"><?php echo $bookmark->link_description ?></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php }
                                        echo '</div>';
                                    }
                                } else {
                                    echo '<div class="row">';
                                    $bookmarks = get_bookmarks(array(
                                        'orderby' => 'rating',
                                        'order' => 'asc'
                                    ));
                                    foreach ($bookmarks as $bookmark) {
                                        $ico = $faviconApi . $bookmark->link_url;
                                        ?>
                                        <div class="url-card col col-6 col-md-3">
                                            <div class="card url-body default">
                                                <div class="card-body">
                                                    <div class="url-content d-flex align-items-center">
                                                        <div class="url-img rounded-circle mr-2 d-flex align-items-center justify-content-center">

                                                            <img class="lazy" src="<?php echo $default_b2img; ?>" data-src="<?php echo $ico; ?>"
                                                                 onerror="javascript:this.src='<?php echo $default_ico; ?>'" alt="">
                                                        </div>
                                                        <div class="url-info flex-fill">
                                                            <div class="text-sm overflowClip_1">
                                                                <a href="<?php echo $bookmark->link_url; ?>"
                                                                   title="<?php echo $bookmark->link_name; ?>"
                                                                   target="_blank"><strong><?php echo $bookmark->link_name; ?></strong></a>
                                                            </div>
                                                            <p class="overflowClip_1 m-0 text-xs"><?php echo $bookmark->link_description ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php }
                                    echo '</div>';
                                } ?>
                                <div class="clear"></div>
                            </article>
                        </div><!-- .content -->
                    </article><!-- #page -->
                <?php endwhile; ?>
                <?php while (have_posts()) : the_post(); ?>
                    <?php if (comments_open() || get_comments_number()) : ?>
                        <?php comments_template('', true); ?>
                    <?php endif; ?>
                <?php endwhile; ?>
            </main>
        </article>
    </div>
</div>
    
<?php if ( lmy_get_option( 'add_link' ) ) { ?>
<!--提交友情链接申请表单-->
<div id="popDiv" class="yltc">
    <!-- 表单div -->
    <div id="main" meta="referer-policy:never">
        <div class="container">
            <div class="content content-link-application">
                <div class="wb-form contact-form nice-validator n-default">

                    <div class="modal show-modalyl">
                        <div class="modal-content login-box-content b2-radius">
                            <div class="box login-box-top">
                                <span onclick="document.getElementById('popDiv').style.display='none'"
                                    class="close-button">&times;</span>
                                <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
                                    <div class="login-box-in" style="">
                                        <div class="login-title">
                                            <span>申请友链</span>
                                        </div>
                                        <label class="login-form-item" style=""><input type="text" name="b2child_name"
                                                placeholder="请输入网站名称" tabindex="1" spellcheck="false" autocomplete="off"
                                                class="" /> <span><b>网站名称</b></span></label>
                                        <label class="login-form-item"><input type="text" name="b2child_url"
                                                placeholder="请输入链接地址" tabindex="2" spellcheck="false" autocomplete="off"
                                                class="active" /> <span><b>网站链接</b></span></label>
                                        <label class="login-form-item" style="display: none;">
                                            <div class="check-code-luo">
                                                <div data-site-key="" data-width="100%" data-callback="getResponse"
                                                    class="l-captcha"></div>
                                            </div>
                                        </label>
                                        <label class="login-form-item"><input name="link_notes" placeholder="请输入站长QQ"
                                                tabindex="3" autocomplete="off" spellcheck="false" type="text"
                                                class="active" /> <span><b>QQ</b></span> </label>
                                        <label class="login-form-item"><input name="b2child_description"
                                                placeholder="请输入网站简介" tabindex="4" autocomplete="off" spellcheck="false"
                                                type="text" class="active" /> <span><b>网站简介</b></span> </label>
                                        <!---->
                                        <div class="login-bottom">
                                            <!---->
                                            <input type="hidden" value="send" name="blink_form" />
                                            <button type="submit" class="add-link-btn da bk">提交申请</button>
                                            <!---->
                                            <!---->
                                        </div>
                                    </div>
                                    <div class="site-terms">
                                        <span>提交申请后，站长会在1-3天内审核，可先做好本站链接。</span>
                                    </div>
                                </form>
                            </div>
   </div>
  </div>
  </div>
</div></div></div></div>
                    <?php } ?>

<script>
    // 获取模型
    var modal = document.getElementById('popDiv');

    // 鼠标点击模型外区域关闭登录框
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
<?php get_footer();
