/**
	姓名：冯宇
	site: http://fy.035k.com
	更新时间 ：2018-04-03
	QQ : 515184405
	github : https://github.com/515184405/barrage 还有一些其他的项目哦，欢迎star

**/

	$.fn.barrage = function(opt){

		var _self = $(this);

		var opts = {   //默认参数
			data : [], //数据列表
			row : 5,   //显示行数
			time : 1000, //间隔时间
			gap : 15,    //每一个的间隙
			direction : 'bottom right', //方向
			ismoseoverclose : true, //悬浮是否停止
		}

		var settings = $.extend({},opts,opt); //合并参数
		var M = {},Obj = {}; 
		Obj.data = settings.data;
		M.vertical = settings.direction.split(/\s+/)[0]; //纵向
		M.horizontal = settings.direction.split(/\s+/)[1]; //横向
		M.bgColors = ['#f79a3e','#e66760','#5382af',' #aea79f','#37b8af','#008b5d',' #f0b849','#499df3',' #5f6c72',' #8c88cd']; //随机背景色数组#f79a3e','#e66760','#5382af',' #aea79f','#37b8af','#008b5d',' #f0b849','#499df3',' #5f6c72',' #8c88cd
		Obj.arrEle = []; //预计存储dom集合数组
		M.barrageBox = $('<div id="dim_barrage"></div>'); //存所有弹幕的盒子
		M.timer = null; 
		var createView = function(){
			var randomIndex = Math.floor(Math.random() * M.bgColors.length);
			var ele = $('<li class="overflow-text" style="opacity:0;text-align:'+settings.direction.split(/\s+/)[1]+';float:'+settings.direction.split(/\s+/)[1]+';background-color:'+M.bgColors[randomIndex]+'"; ><a class="img" target="_blank" href="'+(Obj.data[0].link ? Obj.data[0].link : "javascript:;")+'"><img src="'+Obj.data[0].avatar+'"width="40" height="40"></a>'+Obj.data[0].answer+'</li>');
		var str = Obj.data.shift();
		ele.animate({
		'opacity' : 1,
		'margin-bottom' : settings.gap
		},1000)
		M.barrageBox.append(ele);
		Obj.data.push(str);

		if(M.barrageBox.children().length > settings.row){

		M.barrageBox.children().eq(0).animate({
		'opacity' : 0,
		},300,function(){
		$(this).css({
		'margin' : 0,
		}).remove();
		})
		}
		}
		M.mouseClose = function(){
		settings.ismoseoverclose && (function(){

		M.barrageBox.mouseover(function(){
		clearInterval(M.timer);
		M.timer = null;
		}).mouseout(function(){
		M.timer = setInterval(function(){ //循环
		createView();
		},settings.time)
		})

		})()
		}
		Obj.close = function(){
		M.barrageBox.remove();
		clearInterval(M.timer);
		M.timer = null;
		}
		Obj.start = function(){
		if(M.timer) return;
		_self.append(M.barrageBox); //把弹幕盒子放到页面中
		createView(); //创建试图并开始动画
		M.timer = setInterval(function(){ //循环
		createView();
		},settings.time)
		M.mouseClose();
		}
		return Obj;
		}
		$.ajax({
			type: "POST",
			url: b2_global.rest_url+'b2/v1/B2Danmu_allinfo_data',
			success: function(data) {
				var Obj = $('body').barrage({
					data: data,
					//数据
					row: 6,
					//显示行数
					time: 1000,
					//时间
					gap: 15,
					//间隙
					ismoseoverclose: true,
					//悬浮是否停止
				})
				if ($('#dim_barrage').length == 0) {
					Obj.start();
				}
			}
});