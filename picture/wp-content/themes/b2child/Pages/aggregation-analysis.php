<?php

/*
Template Name: 无水印解析
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
                     <html>
                        <head>
                        <meta charset="utf-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1">
                        <style>
                        .inputUrl{width: 100%; display: flex; justify-content: flex-end; align-items: center; flex-direction: column;} .textUrl{width: 90%; height: 45px; outline: none; padding: 4px 8px; border-radius: 20px; border: 1.5px solid #c0c0c0; outline: none; background: #00000000; font-weight: 700; box-shadow: 0px 0px 2px #eeeeee;} .sendBtn{width: 90%; height: 40px; margin: 15px; box-shadow: 0px 0px 2px #555; font-weight: 700;} .sendBtn:hover{background-color: #FFF;} .down{text-align: center;} .down a{display: inline-block; padding: 0 1em; margin: 0.5em; height: 2.75em; border: solid 1px #c8cccf; border-radius: 20px; background-color: transparent; color: #414f57!important; text-align: center; text-decoration: none; white-space: nowrap; line-height: 2.75em; font-weight: 700;} #myDiv{color: #0089fff0; overflow-y: hidden; background-color: #f0f1f1fa; margin: 10px; border-radius: 8px; word-wrap: break-word;} #myDiv1{text-align: center;font-weight: 700;padding: 10px;}.myJson{margin: 20px}
                        </style>
                        </head>
                        <!--<div id="wrapper">-->
                            <!--<form method="post">-->
                              <div id="myDiv1">
                                <p>
                                  目前支持:抖音/皮皮虾/火山/微视/微博/绿洲/最右/轻视频/instagram/哔哩哔哩/快手/全民小视频/皮皮搞笑
                                  <br>
                                  温馨提示:粘贴视频地址时无需删除文案 但如果视频链接正确但解析失败请删掉文案后重试
                                </p>
                              </div>
                              <div class="inputUrl">
                              <input class="textUrl" type="text" placeholder="请粘贴视频链接" id="input1" class="longurl">
                              <input class="btn sendBtn" type="button" onclick="setValue()" value="解析">
                              </div>
                            </form>
                            <div class="down"></div>
                              <div id="myDiv"></div>
                        </div>
                        <script>
                        function  setValue(){
                        var v =  document.getElementById("input1").value;
                            var xmlhttp;
                            if(window.XMLHttpRequest){
                              xmlhttp=new XMLHttpRequest();
                              }
                            else{
                            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
                            xmlhttp.open("GET","https://tenapi.cn/video?url="+v,false);
                            xmlhttp.send();
                            var jsonObj = JSON.parse(xmlhttp.responseText); 
                            document.getElementById("myDiv").innerHTML="<div class='myJson'>返回代码："+jsonObj.code+"<br/>标题："+jsonObj.title+"<br/>封面地址："+jsonObj.cover+"<br/>下载地址："+jsonObj.url+"<br/><br/>JSON："+xmlhttp.responseText+"</div>";
                            $(".down").html('<h4 id="form-title" style="font-weight: 700;">'+jsonObj.title+'</h4><a id="form-cover" href="'+jsonObj.cover+'" target="_blank" download="video">下载封面</a><a id="form-video" href="'+jsonObj.url+'" target="_blank" download="video">下载视频</a><a id="form-music" href="'+jsonObj.music+'" target="_blank" download="video">下载音乐</a>');
                        }
                        </script>
                    </html>
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
