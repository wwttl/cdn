//文章页面高亮代码复制粘贴
for (var i = 0; i < $(".prettyprint").length; i++) {
    $(".prettyprint").eq(i).append('<span class="copy" data-clipboard-target="#copy'+ i +'">一键复制</span>');
    $(".prettyprint").eq(i).attr('id','copy'+ i);
}
var clipboard = new ClipboardJS('.copy');
clipboard.on('success', function(e) {
    //console.info(e.text); 提示
    e.clearSelection();
    e.trigger.innerHTML = "一键复制成功";
     e.trigger.disabled = true;
        setTimeout(function() {
            e.trigger.innerHTML = "一键复制";
            e.trigger.disabled = false;
        },
        2000);/**时长**/
});