/*首页动态大图搜索开始*/
(function($){
  var m=$('.primary-menus');
  if(m.length<1) return;
  var ul=m.find('.selects');
  if(ul.length<1) return;
  var lis=ul.children('li');
  if(lis.length<1) return;
  var s=m.find('.search');
  var sVal=s.find('.s').val();
  lis.on('click',function () {
    var d=$(this).attr('data-target');
    if (d) {
      lis.removeClass('current');
      $(this).addClass('current');
      s.addClass('hidden');
      s.filter('#'+d).removeClass('hidden');
      //s.filter('#'+d).find('.s').val('');
      s.filter('#'+d).find('.s').trigger('focusin');
    }
  });
  s.find('.s').on('focusin',function () {
    if ($(this).val()==sVal) {
      $(this).val('');
    }
  })
  s.find('.s').on('focusout',function () {
    var v=$(this).val();
    if (orz.isEmpty(v)) {
      v=sVal;
    }
    s.find('.s').val(v);
  })
})(jQuery);
/*首页动态大图搜索结束*/