<?php

/*
Template Name: 在线视频
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
                    <h1><?php echo get_the_title(); ?></h1>
                </header>
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
                    <html lang="zh-cn">
                    <head>
                    <style type="text/css"> 
                    .wiui01{ width:100%; height:645px;border-radius:20px;}
                    .input-group-addon{padding: 6px 12px; font-size: 14px; font-weight: 400; line-height: 1; color: #555; text-align: center; background-color: #eee; border-radius: 20px;border: #ccc;margin-right: 20px}
                    @media screen and (max-width: 768px){.wiui01{width:100%; height:200px;}}}
                       </style>
                            <script type="text/javascript">
                    eval(function(p,a,c,k,e,d){e=function(c){return(c<a?"":e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)d[e(c)]=k[c]||e(c);k=[function(e){return d[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('b a(){0 6=1.2("9").4;0 5=1.2("3");0 3=1.2("3").c;0 8=5.e[3].4;0 7=1.2("f");7.d=8+6}',16,16,'var|document|getElementById|jk|value|jkurl|diz|cljurl|jkv|url|dihejk|function|selectedIndex|src|options|player'.split('|'),0,{}))
                            function dihejk2() {
                            var diz = document.getElementById("url").value;
                            var jkurl = document.getElementById("jk");
                            var jk = document.getElementById("jk").selectedIndex;
                            var jkv = jkurl.options[jk].value;
                            var cljurl = document.getElementById("player");
                            window.open(jkv + diz,"_blank");
                            }
                            </script>
                        </head>
                        <body>
                            <div class="col-md-14 column">
                                    <div id="kj" >
                                        <iframe class="wiui01" src="/wp-content/themes/b2child/oauth/video/videos.html" style="background-color: black;" id="player" allowtransparency="true" allowfullscreen="true" frameborder="0" scrolling="no" name="player"></iframe>
                                    </div>
                                <script type="text/javascript">
                    if(navigator.userAgent.match(/(iPhone|iPod|Android|ios)/i)){
                                document.getElementById("sdfdf").style.display = "block";
                                }
                                </script>
                            </div><br>
                            <div class="col-md-14 column">
                                <div class="input-group" style="width: 100%;">
                                    <span class="input-group-addon">选择接口</span> <select class="form-control" id="jk">
                                     <option value="https://okjx.cc/?url=" selected="">
                                     0️⃣网友推荐-推荐0 
                                     </option> 
                                     <option value="https://jx.aidouer.net/?url=" selected=""> 
                                     1️⃣ 网友推荐-推荐1
                                     </option>
                                     <option value="http://cdn-vip.xxphp.cn/jiexi/?url=" selected=""> 
                                     2️⃣ 网友推荐-推荐2
                                     </option> 
                                     <option value="https://jx.playerjy.com/?url=" selected=""> 
                                     3️⃣ 网友推荐-推荐3
                                     </option>
                                     <option value="https://dmjx.m3u8.tv/?url=" selected=""> 
                                     4️⃣ 网友推荐-推荐4
                                     </option>
                                     <option value="https://okjx.cc/?url=" selected=""> 
                                     5️⃣ 网友推荐-推荐5
                                     </option>                                        
                                     <option value="https://jx.bozrc.com:4433/player/?url=" selected=""> 
                                     6️⃣ 网友推荐-推荐6
                                     </option>  
                                     <option value="https://vip.parwix.com:4433/player/?url=" selected=""> 
                                     7️⃣ 网友推荐-推荐7
                                     </option>
                                     <option value="https://jx.bozrc.com:4433/player/?url=" selected=""> 
                                     8️⃣ 网友推荐-推荐8
                                     </option>  
                                     <option value="https://jx.bozrc.com:4433/player/?url=" selected=""> 
                                     🔗默认接口(若默认接口无法解析请选择其他接口播放) 
                                      </option>
                                    </select>
                                    </div><br>
                                <div class="input-group" style="width: 100%;">
                                    <span class="input-group-addon" >播放地址</span> <input class="form-control" type="search" placeholder="电脑使用Ctrl+V粘贴网址-手机直接长按粘贴网址" id="url">
                                </div><br>
                                <div>
                                     <button id="bf" type="button" class="btn btn-info btn-block" onclick="dihejk()">点击开始解析</button> 
                                    <button id="bf" type="button" class="btn btn-warning btn-block" onclick="dihejk2()">点击全屏播放解析</button>
                                </div>
                            </div>
                        </body>
                    </html>
                    <h2>🏳‍🌈解析教程：</h2>
                    <p>🧡第一步：进入【<strong><span class="has-inline-color has-vivid-red-color">影视的官网</span>】</strong>。</p><p>💛第二步：点击进入你需要看的影视页面，选择你要看的集数（例如：《<a rel="noreferrer noopener"href="/"target="_blank">瞄准</a>》--&gt;第一集）,复制链接（浏览器上的视频地址）。</p>
                    <p>💚第三步：将复制的链接粘贴到上面的【解析视频】，然后点击【立即播放】，就会跳转到播放页面，然后就可以免费看啦！</p>
                    <p>💌<span class="has-inline-color has-vivid-red-color"><strong>注：</strong>建议使用PC端观看，手机端解析路线可能有广告。如果不能正常解析，请更换解析路线，目前解析路线是[思古解析]，不会更换的请联系不错吧！</span></p>
                    <h2>🈸免责声明：</h2>
                    <p class="has-pale-cyan-blue-color has-text-color has-background has-medium-font-size" style="text-indent: 2em;"><strong><qc style="color:#fb2121;background:undefined">本站服务器仅展示第三方网站接口页面，并不存储任何视频资源。因此经由本站搜索所产生的任何结果皆不代表本站立场，本站不对其真实合法性以及版权负责，亦不承担任何法律责任。本站所有接口皆源于互联网，仅供学习交流。</qc></strong></p>
                    <!--主体结束-->
                </div> 
                </article>
                <!--是否开启评论-->
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
