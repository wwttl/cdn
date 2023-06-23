$(function(){
	$('.jc_hd');
	var _0x2944cf=$('.jc_hd_menu_list');
	$('.hd_menu_dropdown');
	$('.jc_hd_ct_bg');
	var _0x248317=$('.jc_hd_menu');
	var _0x1095d1=0;
	var _0x1170da=null;
	_0x2944cf.children('li').on({'mouseenter':function(){
		var _0x2d541b=$(this);
		_0x1170da=setTimeout(function(){
			_0x2d541b.hasClass('active')||$('.hd_menu_dropdown').stop().fadeOut(300),_0x2d541b.addClass('active').siblings().removeClass('active'),_0x2d541b.hasClass('hasChildren')&&_0x2d541b.find('.hd_menu_dropdown').stop().fadeIn(300);
		},300),_0x528677();
	},'mouseleave':function(){
		_0x1170da&&clearTimeout(_0x1170da);
	}}),$('.jc_hd_menu').on('mouseleave',function(){
		$('.jc_hd_menu_list > li').removeClass('active'),$('.hd_menu_dropdown').stop().fadeOut(300);
	});
	var _0x12e3fc=$('.dropdown-product-nav li');
	$('.dropdown-product-body .dropdown-product-ct'),$('.dropdown-search-clear'),$('.dropdown-product-wrap');
	var _0x3f912e=null;
	_0x12e3fc.on({'mouseenter':function(){
		var _0x2ead6b=$(this);
		_0x3f912e=setTimeout(function(){
			var _0x2944cf=_0x2ead6b.index(),_0x4ef65a=_0x2ead6b.parents('.dropdown-product-wrap').find('.dropdown-product-ct');
			_0x2ead6b.hasClass('active')||(_0x12e3fc.removeClass('active'),_0x2ead6b.addClass('active'),_0x4ef65a.removeClass('dropdown-show'),_0x4ef65a.eq(_0x2944cf).addClass('dropdown-show'));
		},80);
	},'mouseleave':function(){
		clearTimeout(_0x3f912e);
	}}),$('.jc_hd').on('keyup','.dropdown-search-ipt',function(){
		var _0x374741=$(this),_0x2944cf=_0x374741.val(),_0x218f97=_0x374741.parents('.dropdown-product-wrap'),_0x12678c=_0x374741.siblings('.dropdown-search-clear');
		(''==_0x2944cf)||(null==_0x2944cf)?(_0x12678c.hide(),_0x218f97.removeClass('product-searching')):(_0x12678c.show(),_0x218f97.addClass('product-searching'));
	}),$('.dropdown-search-clear,.ds-clear').click(function(){
		var _0x46e14b=$(this),_0x2944cf=_0x46e14b.parents('.dropdown-product-wrap'),_0xdbef5d=_0x2944cf.find('.dropdown-search-ipt'),_0xb43b26=_0x2944cf.find('.dropdown-search-clear');
		_0xdbef5d.val(''),_0xb43b26.hide(),_0x2944cf.removeClass('product-searching');
	});
	function _0x528677(){
		$('.jc_hd_search').removeClass('search-active'),$('.jdc-search-dropdown').hide(),$('.jdc-search-ipt').val(''),$('.jdc-search-ipt').blur();
	}
	var _0x4a5c8b=$('.jdc-rwd');
	var _0x3ea4a1=$('.nav-m-user');
	$('.nav-m-user-list');
	var _0x238825=$('.nav-m-menu-wrap');
	var _0x5f01d3=_0x238825.find('.m-menu-list');
	var _0x40bf23=$('.jdc-nav-btn');
	var _0x136363=0;
	var _0x395f14=$('.m-sub-nav');
	var _0x429b07=$('.m-sub-nav-bd');
	_0x26a9f1();
	function _0x26a9f1(){
		_0x776f2b(),$('.nav-m-menu-wrap').hide(),$('.nav-m-user-list').hide(),_0x5f01d3.hide(),_0x40bf23.click(function(){
			_0x32add2(!0);
		}),_0x3ea4a1.click(function(){
			$('.nav-m-user-list').fadeToggle('fast'),_0x32add2(!1);
		}),$('.jc_hd').on('click','.m-menu-parent',function(){
			$(this).toggleClass('active').next('.m-menu-list').slideToggle('fast');
		}),$(window).on('resize.headerResize',function(){
			_0x4f2105(function(){
				_0x776f2b();
			},80,!1)();
		});
	}
	function _0x776f2b(){
		if(0==_0x4a5c8b.length)return!1;
		var _0x572eee=$('.jc_wrap');
		var _0x5779a1=_0x572eee.width();
		if(_0x136363==_0x5779a1)return!1;
		if(_0x136363=_0x5779a1,_0x5779a1>748){
			var _0x3c7293=$('.jc_hd_operation');
			var _0x1170da=$('.jdc-menu-more');
			var _0x12e3fc=$('.jc_hd_menu');
			_0x1170da.find('.dropdown_title_ul');
			var _0x1cc4f5=_0x12e3fc.offset().left;
			var _0x3f912e=(_0x3c7293.offset().left-0);
			_0x1095d1=parseInt(_0x3f912e-_0x1cc4f5-16),_0x1095d1<_0x2944cf.width()?(_0x12e3fc.width(_0x1095d1),_0x12e3fc.addClass('scroll-show'),$('.scroll-btn-right').show().css('left',parseInt(_0x3f912e-66)),$('.scroll-btn-left').hide(),_0x2944cf.css('margin-left',0)):(_0x12e3fc.css('width','auto'),$('.scroll-btn').hide(),_0x12e3fc.removeClass('scroll-show'),_0x2944cf.css('margin-left',0));
		}
	}
	$('.jc_hd').on('click','.scroll-btn-right',function(){
		_0x248317.hasClass('scroll-show')&&(_0x2944cf.css('margin-left',_0x1095d1-_0x2944cf.width()),$('.scroll-btn-right').hide(),$('.scroll-btn-left').show());
	}),$('.jc_hd').on('click','.scroll-btn-left',function(){
		_0x248317.hasClass('scroll-show')&&(_0x2944cf.css('margin-left',0),$('.scroll-btn-right').show(),$('.scroll-btn-left').hide());
	});
	function _0x32add2(_0x34a7e2){
		_0x40bf23.toggleClass('jdc-nav-btn-actived'),_0x40bf23.hasClass('jdc-nav-btn-actived')?(_0x34a7e2&&$('.nav-m-menu-wrap').delay(50).fadeIn('fast'),_0x3ea4a1.fadeOut('fast'),_0x3212ff()):($('.nav-m-menu-wrap').fadeOut('fast'),_0x3ea4a1.delay(50).fadeIn('fast'),$('#js-nav-m-mask').hide(),$('.nav-m-user-list').fadeOut('fast'),_0x395f14.removeClass('open'),_0x429b07.delay(50).fadeOut('fast'));
	}
	function _0x3212ff(){
		var _0x46527b=$('#js-nav-m-mask');
		var _0x2944cf=_0x46527b.length;
		var _0x34b841='<div class="nav-m-mask" id="js-nav-m-mask"></div>';
		_0x2944cf||$('body').append(_0x34b841),_0x46527b.show();
	}
	function _0x4f2105(_0x3e087f,_0x2944cf,_0x4ab096){
		var _0x2417ee;
		return function(){
			var _0x248317=this,_0x1095d1=arguments;
			function _0x1170da(){
				_0x4ab096||_0x3e087f.apply(_0x248317,_0x1095d1),_0x2417ee=null;
			}
			_0x2417ee?clearTimeout(_0x2417ee):_0x4ab096&&_0x3e087f.apply(_0x248317,_0x1095d1),_0x2417ee=setTimeout(_0x1170da,_0x2944cf||100);
		};
	}
	var _0x44dd75=$('.jdc-lang-tips');
	_0x44dd75.on('click','.lang-tips-close',function(){
		_0x44dd75.hide();
	}),_0x44dd75.on('click','.lang-tips-check-box',function(){
		$(this).find('.lang-tips-check').toggleClass('is-checked');
	}),$('.jc_hd_lang,.jc_hd_log_in').mouseenter(function(){
		_0x528677();
	}),$(document).click(function(){
		_0x528677();
	}),$('.jdc-search-ipt,.jdc-search-mask').click(function(_0x202bf4){
		_0x202bf4.stopPropagation(),$('.jc_hd_search').addClass('search-active'),$('.jdc-search-dropdown').show(),$('.jdc-search-ipt').focus();
	}),$('.jdc-search-clear').click(function(_0x371fd5){
		_0x371fd5.stopPropagation(),_0x528677();
	}),$('.m-search-ipt').keyup(function(){
		var _0x31e90f=$(this),_0x2944cf=_0x31e90f.val();
		(''==_0x2944cf)||(null==_0x2944cf)?$('.m-search-clear').hide():$('.m-search-clear').show();
	}),$('.m-search-clear').click(function(){
		$('.m-search-ipt').val(''),$(this).hide();
	}),$.fn.scrollEnd=function(_0x511ada,_0x2944cf){
		$(this).scroll(function(){
			var _0x710697=$(this);
			_0x710697.data('scrollTimeout')&&clearTimeout(_0x710697.data('scrollTimeout')),_0x710697.data('scrollTimeout',setTimeout(_0x511ada,_0x2944cf));
		});
	};
	var _0x298267=$('.m-consultation');
	$(window).scrollEnd(function(){
		_0x298267.removeClass('scrolling');
	},100),$(window).scroll(function(){
		_0x298267.hasClass('scrolling')||_0x298267.addClass('scrolling'),$('.m-consultation .cu-sub').hide(),$('.m-consultation-mask').hide();
	}),$('.jc_hd').on('click','.m-consultation-btn',function(){
		$('.m-consultation .cu-sub').show(),$('.m-consultation-mask').show();
	}),$('.jc_hd').on('click','.m-consultation-mask',function(){
		$('.m-consultation .cu-sub').hide(),$('.m-consultation-mask').hide();
	}),$('.m-sub-nav-tit').click(function(){
		_0x395f14.hasClass('open')?(_0x395f14.removeClass('open'),$('#js-nav-m-mask').hide(),_0x429b07.delay(50).fadeOut('fast')):(_0x395f14.addClass('open'),_0x3212ff(),_0x429b07.delay(50).fadeIn('fast'));
	}),$('.nav-m-user-list').on('click','.m-tips-wx',function(){
		$(this).parents('.nav-m-user-list').addClass('open');
	}),$('.nav-m-user-list').on('click','.m-qrcode-pop',function(){
		$(this).parents('.nav-m-user-list').removeClass('open');
	});

});