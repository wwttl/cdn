$.get("https://v1.hitokoto.cn/",function(data,status){
  if (status == 'success'){
    $('#textarea').text(data.hitokoto);
  }else{
    $('#textarea').text('获取出错！');
  }
});