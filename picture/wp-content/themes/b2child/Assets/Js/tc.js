/* 禁用调试以及右键并提醒 */
var url = "https://pv.sohu.com/cityjson?ie=utf-8"
document.onkeydown=function(){if(event.ctrlKey&&window.event.keyCode==85){new Vue({data:function(){this.$notify({title:"嘿！别瞎按",message:"老弟，在干嘛呢？已记录你的IP",position:'bottom-right',offset:50,showClose:true,type:"error"});return{visible:false}}})
return false;}
if(window.event&&window.event.keyCode==123){event.keyCode=0;event.returnValue=false;new Vue({data:function(){this.$notify({title:"嘿！Bingo~",message:"老弟，试试 Alt+Shift+Fn+F4",position:'bottom-right',offset:50,showClose:true,type:"error"});return{visible:false}}})
return false;}
if(event.ctrlKey&&window.event.keyCode==83){new Vue({data:function(){this.$notify({title:"嘿！你瞧瞧你",message:"网页得换方法保存哦~",position:'bottom-right',offset:50,showClose:true,type:"error"});return{visible:false}}})
return false;}
if((event.ctrlKey)&&(event.shiftKey)&&(event.keyCode==73)){new Vue({data:function(){this.$notify({title:"嘿！哈哈哈",message:"老弟，调试方法也得换换哟~",position:'bottom-right',offset:50,showClose:true,type:"error"});return{visible:false}}})
return false;}
if(window.event&&window.event.keyCode==117){event.keyCode=0;event.returnValue=false;new Vue({data:function(){this.$notify({title:"嘿！喂喂喂",message:"浏览器自带刷新按钮不香吗？",position:'bottom-right',offset:50,showClose:true,type:"warning"});return{visible:false}}})
return false;}}
document.oncontextmenu = function (){new Vue({data:function(){this.$notify({title:"嘿！没有右键菜单",message:"复制请用键盘快捷键 Ctrl+C",position:'bottom-right',offset:50,showClose:true,type:"warning"});return{visible:false}}})
return false;}

/*<!--VUE复制提醒-->*/
document.addEventListener("copy",function(e){
    new Vue({
        data:function(){
            this.$notify({
                title:"嘿！复制成功",
                message:"若要转载请务必保留原文链接哦！",
                position: 'bottom-right',
                offset: 50,
                showClose: false,
                type:"success"
            });
            return{visible:false}
        }
    })
    return false;
});
/*消息通知*/
$(function(){
    if(window.localStorage.getItem("isClose") == 'yes'){
    		console.log('Notification通知状态：已关闭');
        return false;
    }else{
        setTimeout(function(){
            new Vue({data:function(){
                this.$notify({
                    title:"签到任务已开放",
                    dangerouslyUseHTMLString:true,
                    message:"本站已经开启签到任务！更多详情可首页侧栏<span style='color: #f00;'> 消息通知 </span>查看",
                    dangerouslyUseHTMLString:true,
                    position:'bottom-right',
                    offset:50,
                    duration:0,
                    type:"success",
                    onClose() {
                        window.localStorage.setItem("isClose", "yes");
                        console.log('Notification通知状态：关闭成功');
                    }
                });
                return{visible:false}
            }});
        },5000);
    }
});