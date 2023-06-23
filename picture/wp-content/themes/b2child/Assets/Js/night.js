/*夜间模式*/
(function() {
    if (document.cookie.replace(/(?:(?:^|.*;\s*)night\s*\=\s*([^;]*).*$)|^.*$/, "$1") === '') {
        if (new Date().getHours() >= 22 || new Date().getHours() < 7) {
            document.body.classList.add('night');
            document.cookie = "night=1;path=/";
            console.log('夜间模式开启');
        } else {
            document.body.classList.remove('night');
            document.cookie = "night=0;path=/";
            console.log('夜间模式关闭');
        }
    } else {
        var night = document.cookie.replace(/(?:(?:^|.*;\s*)night\s*\=\s*([^;]*).*$)|^.*$/, "$1") || '0';
        if (night == '0') {
            document.body.classList.remove('night');
            
        } else if (night == '1') {
            document.body.classList.add('night');
            
        }
    }
})();
function switchnightMode() {
    var night = document.cookie.replace(/(?:(?:^|.*;\s*)night\s*\=\s*([^;]*).*$)|^.*$/, "$1") || '0';
    if (night == '0') {
        document.body.classList.add('night');
        document.cookie = "night=1;path=/"
    } else {
        document.body.classList.remove('night');
        document.cookie = "night=0;path=/"
        
    }
}
