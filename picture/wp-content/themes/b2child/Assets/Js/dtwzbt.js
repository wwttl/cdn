var OriginTitile = document.title,
    titleTime;
document.addEventListener("visibilitychange",
function () {
    if (document.hidden) {
        document.title = "你别走吖 o(╥﹏╥)o";
        clearTimeout(titleTime)
    } else {
        document.title = "哇，你回来啦ヾ(o´∀｀o)ﾉ  ";
        titleTime = setTimeout(function () {
                document.title = OriginTitile
            },
            2000)
    }
});