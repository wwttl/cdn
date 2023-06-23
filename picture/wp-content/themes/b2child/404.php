<?php
//404死链获取
if (lmy_get_option("silian_silian")) {
    require_once get_theme_file_path('/Inc/plugins/silian.php');
}
get_header(); ?>
<div id="primary" class="content-area">
<main id="main" class="site-main wrapper">
<div class="error-404 page-404">
    <div class="page-content"><h2>您访问的页面不存在</h2><p>暂时没有搜寻到您需要的内容，我们会在今后努力补充完整。 <br> 感谢对 乱码天地 的关注支持</p> 
    <a class="go-home b2-radius" href="/">
        <i class="b2font b2-feiji">
        </i>返回首页</a><h3>这些热门频道或许您感兴趣</h3>
        <ul>
        <li>1. <a href="/win/application">电脑驱动</a></li>
        <li>2. <a href="/win/winjc">实用教程</a></li>
        <li>3. <a href="/win/win">win系统下载</a></li>
        </ul>
        </div>
    <div class="page-img"><img src=/wp-content/themes/b2child/img/icon/389b81fd39d3a.png>
  </div>
</main><!-- #main -->
</div><!-- #primary -->

<?php
get_footer();
