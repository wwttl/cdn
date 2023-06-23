<?php
$index_onecad_search_mp4 = lmy_get_option('index_onecad_search_mp4');
$index_onecad_search_text = lmy_get_option('index_onecad_search_text');
?>
<div class="" style="width:100%"><div class="home-row-left content-area "><div id="html-box-shou-suo" class="html-box"><style type="text/css">
.mg-b {
  margin-bottom: -0px;
}
</style>
<!--首页视频区块-->
<div id="page-wrapper">

<div class="home-banner por">
<section class="section">
<div class="video-wrapper">
<video autoplay playsinline="" loop muted="" src="<?php echo $index_onecad_search_mp4; ?>" __idm_id__="2285569"></video>
</div>
<div class="video-overlay"></div>
</section>
<div class="layout-center poa" style="width: 1200px;top: 0;left: 50%;margin-left: -600px;">
<div class="home-banner-content clearfix">
<div class="slogan-text por fl">
<p><?php echo get_bloginfo('name'); ?>-<?php echo get_bloginfo('description') ?></p>
<i class="iblock poa corner" style="background:url(/wp-content/themes/b2child/images/hot.svg) no-repeat;"></i>
<p class="promote-sub-title line-one"><p class="promote-sub-title line-one">已发布<span style="display: inline-block;overflow: hidden;line-height: 34px;vertical-align: -9px;">
<em id="goal-works" value="5351266">
<ps style="color: #26d6c8;"><?php $count_posts = wp_count_posts(); echo $published_posts =$count_posts->publish;?><ps>
</em>
</span>篇文章内容，运行了<ps style="color: #26d6c8;">2</ps>年<ps style="color: #26d6c8;">4</ps>个月</p>
</div>
</div>
<div class="home-banner-search por searchv2-top-m">
<div class="primary-menus" style=" width: 92%; position: unset;transform: translate(1px, 1px);">
<div class="search-types-cycles poa">
<ul class="selects">
<li data-target="search_1">百度 </li>
<li data-target="search_2">Bing </li>
<li data-target="search_3" class="current">站内搜索 </li>
<li data-target="search_4">360 </li>
<li data-target="search_5">哔哩哔哩 </li>
<li data-target="search_6">头条搜索 </li>
<li data-target="search_8">知乎 </li>
</ul>
</div>
<div class="cont">
<div class="left-cont">
<form class="search hidden" id="search_1" action="https://www.baidu.com/s?wd=" method="get" target="_blank">
<input type="text" name="wd" class="s" placeholder="请输入关键词">
<button type="submit" name="" class="btn">百度搜索</button>
</form>
<form class="search hidden" id="search_2" action="https://cn.bing.com/search?q=" method="get" target="_blank">
<input type="text" name="q" class="s" placeholder="请输入关键词">
<button type="submit" name="" class="btn">Bing搜索</button>
</form>
<form class="search" id="search_3" action="/?s=" method="get" target="_blank">
<input type="text" name="s" class="s" placeholder="请输入关键词">
<button type="submit" name="" class="btn">站内搜索</button>
</form>
<form class="search hidden" id="search_4" action="https://www.so.com/s?q=" method="get" target="_blank">
<input type="text" name="query" class="s" placeholder="请输入关键词">
<button type="submit" name="" class="btn">360搜索</button>
</form>
<form class="search hidden" id="search_5" action="https://search.bilibili.com/all?keyword=" method="get" target="_blank">
<input type="text" name="q" class="s" placeholder="请输入关键词">
<button type="submit" name="" class="btn">哔哩哔哩</button>
</form>
<form class="search hidden" id="search_6" action="https://so.toutiao.com/search?dvpf=pc&source=input&keyword=" method="get" target="_blank">
<input type="text" name="q" class="s" placeholder="请输入关键词">
<button type="submit" name="" class="btn">头条搜索</button>
</form>
<form class="search hidden" id="search_8" action="https://www.zhihu.com/search?q=" method="get" target="_blank">
<input type="text" name="q" class="s" placeholder="请输入关键词">
<button type="submit" name="" class="btn">知乎搜索</button>
</form>
</div>
</div>
</div>
<p class="home-banner-links line-one">热搜词：
<?php 
    if (!empty(lmy_get_option('index_onecad_search_rmtj'))) {
                                foreach (lmy_get_option('index_onecad_search_rmtj') as $group_item) {
                                        echo '<a href="' . $group_item['ad_url'] . '" target="_blank" class="iblock">' . $group_item['ad_text'] . '</a>';
                                }
                    }?>
</p>
<style>
p.home-banner-linkss.line-one1 { width: 80%; font-size: 14px; height: 20px; line-height: 20px; color: #fff; text-shadow: 0 2px 4px rgb(0 0 0 / 27%); margin-top: 40px; text-align: center; }
</style>
<p class="home-banner-linkss line-one1">
</p>
</div>
</div> <!-- 头部快速链接导航 -->
<div class="top-navs poa">
<div class="layout-center clearfix" style="width: 1200px;">
<div class="top-navs-l fl">
    <?php 
    if (!empty(lmy_get_option('index_onecad_search_ksljdh'))) {
                                foreach (lmy_get_option('index_onecad_search_ksljdh') as $group_item) {
                                        echo '<div class="top-navs-l-item fl">
<p class="top-navs-l-title">
<a href="' . $group_item['ad_url'] . '" target="_blank" class="block">
<svg class="icon-dhs" aria-hidden="true">
<use xlink:href="' . $group_item['ad_svg'] . '"></use>
</svg>' . $group_item['ad_text'] . '
</a>
</p>
<p class="top-navs-l-links">
<a href="' . $group_item['ad_url_lj1'] . '" class="fl" target="_blank">' . $group_item['ad_text_gjc1'] . '</a>
<a href="' . $group_item['ad_url_lj2'] . '" class="fl" target="_blank">' . $group_item['ad_text_gjc2'] . '</a>
</p>
</div>';
                                }
                    }?>
</div>
<div class="top-navs-m fl">
<a href="/gold-top" target="_blank" class="fl">财富排行</a>
<a href="/dark-room" target="_blank" class="fl">小黑屋</a>
<a href="/task" target="_blank" class="fl">任务大厅</a>
<a href="/mission/today" target="_blank" class="fl">签到管理</a>
</div>
<div class="top-navs-r fl clearfix">
<a class="fl" rel="nofollow" target="_blank" href="/verify">
<svg class="icon-dhs" aria-hidden="true">
<use xlink:href="#hg-yonghu"></use>
</svg>
<p>高级认证</p>
</a>
<a class="fl" target="_blank" href="/vips">
<svg class="icon-dhs" aria-hidden="true">
<use xlink:href="#hg-huiyuan"></use>
</svg>
<p>会员办卡</p>
</a>
</div>
</div>
</div>
</div>
</div>