//复制成功弹窗
document.body.oncopy = function() {
    Qmsg['success']('复制成功', {
        html: true
    });
};
//首页是否购买VIP判断开始
var qukuai = new Vue({
	el:'.sort-mine-wrap',
	data:{
	
	},
	computed:{
        userData(){
            return this.$store.state.userData;
        }
    }
})
console.log('\n' + ' %c 网站定制微信：ATMJGY %c https://www.wwttl.com ' + '\n', 'color: #fadfa3; background: #030307; padding:5px 0; font-size:12px;', 'background: #fadfa3; padding:5px 0; font-size:12px;');