
if (navigator.userAgent.match(/mobile/i)) {
	$('#jxm').css('display', 'none');
	$('#two').css('display', 'none');
	$('#three').css('display', 'none');
	$('#four').css('display', 'none');
	$('#five').css('display', 'none');
	$('#six').css('display', 'none');
	$('#swiper-1').css('display', 'none');
	$('#btplw1').css('display', 'none');
	$('#btplw2').css('display', 'none');
	$('#btplw3').css('display', 'none');
	$('#btplw4').css('display', 'none');
	$('#btplw5').css('display', 'none');
	$(function () {
		new Swiper('.redcard-swiper', { 'spaceBetween': 0, 'allowTouchMove': !1, 'autoplay': { 'delay': 6000, 'stopOnLastSlide': !1, 'disableOnInteraction': !0 }, 'loop': !0, 'pagination': { 'el': '.jdc-swiper-pagination', 'clickable': !0 }, 'breakpoints': { 768: { 'slidesPerView': 'auto', 'slidesPerGroup': 1, 'spaceBetween': 0, 'allowTouchMove': !0, 'centeredSlides': !0, 'loop': !0 } } });
	});
}
$(function () {
	new Swiper('.resist-swiper', { 'spaceBetween': 0, 'allowTouchMove': !1, 'autoplay': { 'delay': 6000, 'stopOnLastSlide': !1, 'disableOnInteraction': !0 }, 'loop': !0, 'pagination': { 'el': '.jdc-swiper-pagination', 'clickable': !0 }, 'breakpoints': { 768: { 'slidesPerView': 'auto', 'slidesPerGroup': 1, 'spaceBetween': 0, 'allowTouchMove': !0, 'centeredSlides': !0, 'loop': !0 } } });
	new Swiper('.templet-swiper', { 'observer': !0, 'observeParents': !0, 'slidesPerView': '3', 'slidesPerGroup': '1', 'spaceBetween': 0, 'allowTouchMove': !1, 'breakpoints': { 768: { 'slidesPerView': 'auto', 'slidesPerGroup': 1, 'spaceBetween': 0, 'allowTouchMove': !0, 'centeredSlides': !0, 'loop': !0 } } });
});
jQuery(document).ready(function (_0x40ae33) {
	$body = window.opera ? (document.compatMode == 'CSS1Compat') ? _0x40ae33('html') : _0x40ae33('body') : _0x40ae33('html,body');
	_0x40ae33('#csbt1').click(function () {
		$body.animate({ 'scrollTop': _0x40ae33('#cs1').offset().top }, 1000);
		return false;
	});
	_0x40ae33('#csbt2').click(function () {
		$body.animate({ 'scrollTop': _0x40ae33('#cs2').offset().top }, 1000);
		return false;
	});
	_0x40ae33('#csbt3').click(function () {
		$body.animate({ 'scrollTop': _0x40ae33('#cs3').offset().top }, 1000);
		return false;
	});
});
function _0x28b562() {
	$('.jdc-search-ipt').attr('placeholder', '云主机');
}
function _0x132252() {
	$('.jdc-search-ipt').attr('placeholder', '云数据库 MySQL');
}
function _0x8621b9() {
	$('.jdc-search-ipt').attr('placeholder', '对象储存');
}
function _0x194b8f() {
	$('.jdc-search-ipt').attr('placeholder', 'LolipaStatck 专有云');
}
var _0x4c646e = window.setInterval(_0x28b562, 5000);
var _0x17308b = window.setInterval(_0x132252, 10000);
var _0x202522 = window.setInterval(_0x8621b9, 15000);
var _0x2bbdc6 = window.setInterval(_0x194b8f, 20000);
$('#btplw1').click(function () {
	$('#plw1').css('display', 'block');
	$('#plw2').css('display', 'none');
	$('#plw3').css('display', 'none');
	$('#plw4').css('display', 'none');
	$('#plw5').css('display', 'none');
});
$('#btplw2').click(function () {
	$('#plw1').css('display', 'none');
	$('#plw2').css('display', 'block');
	$('#plw3').css('display', 'none');
	$('#plw4').css('display', 'none');
	$('#plw5').css('display', 'none');
});
$('#btplw3').click(function () {
	$('#plw1').css('display', 'none');
	$('#plw2').css('display', 'none');
	$('#plw3').css('display', 'block');
	$('#plw4').css('display', 'none');
	$('#plw5').css('display', 'none');
});
$('#btplw4').click(function () {
	$('#plw1').css('display', 'none');
	$('#plw2').css('display', 'none');
	$('#plw3').css('display', 'none');
	$('#plw4').css('display', 'block');
	$('#plw5').css('display', 'none');
});
$('#btplw5').click(function () {
	$('#plw1').css('display', 'none');
	$('#plw2').css('display', 'none');
	$('#plw3').css('display', 'none');
	$('#plw4').css('display', 'none');
	$('#plw5').css('display', 'block');
});
$('#btplw11').click(function () {
	$('#plw1').css('display', 'block');
	$('#plw2').css('display', 'none');
	$('#plw3').css('display', 'none');
	$('#plw4').css('display', 'none');
	$('#plw5').css('display', 'none');
});
$('#btplw22').click(function () {
	$('#plw1').css('display', 'none');
	$('#plw2').css('display', 'block');
	$('#plw3').css('display', 'none');
	$('#plw4').css('display', 'none');
	$('#plw5').css('display', 'none');
});
$('#btplw33').click(function () {
	$('#plw1').css('display', 'none');
	$('#plw2').css('display', 'none');
	$('#plw3').css('display', 'block');
	$('#plw4').css('display', 'none');
	$('#plw5').css('display', 'none');
});
$('#btplw44').click(function () {
	$('#plw1').css('display', 'none');
	$('#plw2').css('display', 'none');
	$('#plw3').css('display', 'none');
	$('#plw4').css('display', 'block');
	$('#plw5').css('display', 'none');
});
$('#btplw55').click(function () {
	$('#plw1').css('display', 'none');
	$('#plw2').css('display', 'none');
	$('#plw3').css('display', 'none');
	$('#plw4').css('display', 'none');
	$('#plw5').css('display', 'block');
});
$('span[class=\'el-checkbox__input\']').addClass('is-checked');
$('.pro-nav-ul li').click(function () {
	$(this).siblings('li').removeClass('on');
	$(this).addClass('on');
});
$('.nav-item').click(function () {
	$(this).siblings('div').removeClass('on');
	$(this).addClass('on');
});
$('#1-1').click(function () {
	$('#tab1').attr('class', 'pro-tab-bd active');
	$('#tab2').attr('class', 'pro-tab-bd');
	$('#1-1').attr('class', 'nav-item on nav-animation');
	$('#1-2').attr('class', 'nav-item nav-animation');
	$('#1-3').attr('class', 'nav-item nav-animation');
	$('#1-4').attr('class', 'nav-item nav-animation');
	$('#1-5').attr('class', 'nav-item nav-animation');
	$('#1-6').attr('class', 'nav-item nav-animation');
	$('#one').css('display', 'block');
	$('#two').css('display', 'none');
	$('#three').css('display', 'none');
	$('#four').css('display', 'none');
	$('#five').css('display', 'none');
	$('#six').css('display', 'none');
	$('#2-1').attr('class', 'nav-item nav-animation');
	$('#2-2').attr('class', 'nav-item nav-animation');
	$('#2-3').attr('class', 'nav-item nav-animation');
	$('#2-4').attr('class', 'nav-item nav-animation');
	$('#2-5').attr('class', 'nav-item nav-animation');
	$('#2-6').attr('class', 'nav-item nav-animation');
});
$('#1-2').click(function () {
	$('#tab1').attr('class', 'pro-tab-bd active');
	$('#tab2').attr('class', 'pro-tab-bd');
	$('#1-1').attr('class', 'nav-item nav-animation');
	$('#1-2').attr('class', 'nav-item on nav-animation');
	$('#1-3').attr('class', 'nav-item nav-animation');
	$('#1-4').attr('class', 'nav-item nav-animation');
	$('#1-5').attr('class', 'nav-item nav-animation');
	$('#1-6').attr('class', 'nav-item nav-animation');
	$('#one').css('display', 'none');
	$('#two').css('display', 'block');
	$('#three').css('display', 'none');
	$('#four').css('display', 'none');
	$('#five').css('display', 'none');
	$('#six').css('display', 'none');
	$('#2-1').attr('class', 'nav-item nav-animation');
	$('#2-2').attr('class', 'nav-item nav-animation');
	$('#2-3').attr('class', 'nav-item nav-animation');
	$('#2-4').attr('class', 'nav-item nav-animation');
	$('#2-5').attr('class', 'nav-item nav-animation');
	$('#2-6').attr('class', 'nav-item nav-animation');
});
$('#1-3').click(function () {
	$('#tab1').attr('class', 'pro-tab-bd active');
	$('#tab2').attr('class', 'pro-tab-bd');
	$('#1-1').attr('class', 'nav-item nav-animation');
	$('#1-2').attr('class', 'nav-item nav-animation');
	$('#1-3').attr('class', 'nav-item on nav-animation');
	$('#1-4').attr('class', 'nav-item nav-animation');
	$('#1-5').attr('class', 'nav-item nav-animation');
	$('#1-6').attr('class', 'nav-item nav-animation');
	$('#one').css('display', 'none');
	$('#two').css('display', 'none');
	$('#three').css('display', 'block');
	$('#four').css('display', 'none');
	$('#five').css('display', 'none');
	$('#six').css('display', 'none');
	$('#2-1').attr('class', 'nav-item nav-animation');
	$('#2-2').attr('class', 'nav-item nav-animation');
	$('#2-3').attr('class', 'nav-item nav-animation');
	$('#2-4').attr('class', 'nav-item nav-animation');
	$('#2-5').attr('class', 'nav-item nav-animation');
	$('#2-6').attr('class', 'nav-item nav-animation');
});
$('#1-4').click(function () {
	$('#tab1').attr('class', 'pro-tab-bd active');
	$('#tab2').attr('class', 'pro-tab-bd');
	$('#1-1').attr('class', 'nav-item nav-animation');
	$('#1-2').attr('class', 'nav-item nav-animation');
	$('#1-3').attr('class', 'nav-item nav-animation');
	$('#1-4').attr('class', 'nav-item on nav-animation');
	$('#1-5').attr('class', 'nav-item nav-animation');
	$('#1-6').attr('class', 'nav-item nav-animation');
	$('#one').css('display', 'none');
	$('#two').css('display', 'none');
	$('#three').css('display', 'none');
	$('#four').css('display', 'block');
	$('#five').css('display', 'none');
	$('#six').css('display', 'none');
	$('#2-1').attr('class', 'nav-item nav-animation');
	$('#2-2').attr('class', 'nav-item nav-animation');
	$('#2-3').attr('class', 'nav-item nav-animation');
	$('#2-4').attr('class', 'nav-item nav-animation');
	$('#2-5').attr('class', 'nav-item nav-animation');
	$('#2-6').attr('class', 'nav-item nav-animation');
});
$('#1-5').click(function () {
	$('#tab1').attr('class', 'pro-tab-bd active');
	$('#tab2').attr('class', 'pro-tab-bd');
	$('#1-1').attr('class', 'nav-item nav-animation');
	$('#1-2').attr('class', 'nav-item nav-animation');
	$('#1-3').attr('class', 'nav-item nav-animation');
	$('#1-4').attr('class', 'nav-item nav-animation');
	$('#1-5').attr('class', 'nav-item on nav-animation');
	$('#1-6').attr('class', 'nav-item nav-animation');
	$('#one').css('display', 'none');
	$('#two').css('display', 'none');
	$('#three').css('display', 'none');
	$('#four').css('display', 'none');
	$('#five').css('display', 'block');
	$('#six').css('display', 'none');
	$('#2-1').attr('class', 'nav-item nav-animation');
	$('#2-2').attr('class', 'nav-item nav-animation');
	$('#2-3').attr('class', 'nav-item nav-animation');
	$('#2-4').attr('class', 'nav-item nav-animation');
	$('#2-5').attr('class', 'nav-item nav-animation');
	$('#2-6').attr('class', 'nav-item nav-animation');
});
$('#1-6').click(function () {
	$('#tab1').attr('class', 'pro-tab-bd active');
	$('#tab2').attr('class', 'pro-tab-bd');
	$('#1-1').attr('class', 'nav-item nav-animation');
	$('#1-2').attr('class', 'nav-item nav-animation');
	$('#1-3').attr('class', 'nav-item nav-animation');
	$('#1-4').attr('class', 'nav-item nav-animation');
	$('#1-5').attr('class', 'nav-item nav-animation');
	$('#1-6').attr('class', 'nav-item on nav-animation');
	$('#one').css('display', 'none');
	$('#two').css('display', 'none');
	$('#three').css('display', 'none');
	$('#four').css('display', 'none');
	$('#five').css('display', 'none');
	$('#six').css('display', 'block');
	$('#2-1').attr('class', 'nav-item nav-animation');
	$('#2-2').attr('class', 'nav-item nav-animation');
	$('#2-3').attr('class', 'nav-item nav-animation');
	$('#2-4').attr('class', 'nav-item nav-animation');
	$('#2-5').attr('class', 'nav-item nav-animation');
	$('#2-6').attr('class', 'nav-item nav-animation');
});
var _0x4c646e = this;
new Swiper('.fs-slide', {
	'effect': 'fade', 'autoplay': { 'delay': 5000, 'stopOnLastSlide': !1, 'disableOnInteraction': !1 }, 'slidesPerView': 1, 'slidesPerGroup': 1, 'spaceBetween': 0, 'allowTouchMove': !1, 'pagination': { 'el': '.fs-slide .jdc-swiper-pagination', 'clickable': !0 }, 'on': {
		'slideChangeTransitionStart': function () {
			if (!_0x4c646e.isMobile) {
				var _0xfd7db5 = document.getElementById('bannerVideo' + this.activeIndex);
				_0xfd7db5 && (_0xfd7db5.pause(), _0xfd7db5.currentTime = 0, _0xfd7db5.play());
			}
		}
	}, 'breakpoints': { 768: { 'allowTouchMove': !0 } }
});
$('.nav-animation').each(function () {
	!function (_0x5f23dc, _0x298028) {
		function _0x342b44() {
			_0x5286eb.css({ 'background-position': ('-' + _0x1e1923 * _0x3e9f18 + 'px 0') });
		}
		function _0x4c646e() {
			(_0x269e11 > ++_0x3e9f18) ? (_0x342b44(), _0x4adb5f = requestAnimationFrame(_0x4c646e)) : cancelAnimationFrame(_0x4adb5f);
		}
		function _0x23a2c4() {
			(--_0x3e9f18 >= 0) ? (_0x342b44(), _0x4adb5f = requestAnimationFrame(_0x23a2c4)) : cancelAnimationFrame(_0x4adb5f);
		}
		var _0x4adb5f, _0x5286eb = $(_0x5f23dc).find('.nav-item-icon'), _0x3e9f18 = 0, _0x269e11 = 24, _0x1e1923 = 56;
		$(_0x5f23dc).hover(function () {
			cancelAnimationFrame(_0x4adb5f), _0x4adb5f = requestAnimationFrame(_0x4c646e);
		}, function () {
			cancelAnimationFrame(_0x4adb5f), _0x4adb5f = requestAnimationFrame(_0x23a2c4);
		});
	}(this);
});
(function () {
	var _0x4c646e = Object(r.a)(regeneratorRuntime.mark(function _0x4c646e(_0x22fb6e, _0x125f2b, _0xa646cf) {
		var _0x279656, _0x115e8b, _0x3d0ff5;
		return regeneratorRuntime.wrap(function (_0x4c646e) {
			for (; ;)switch (_0x4c646e.prev = _0x4c646e.next) {
				case 0:
					if (this.defaultFirstIndex = _0x125f2b, this.defaulrSecondIndex = _0xa646cf, !this.detailMap[_0x22fb6e]) {
						_0x4c646e.next = 5;
						break;
					}
					return this.id = _0x22fb6e, _0x4c646e.abrupt('return');
				case 5:
					return _0x279656 = { 'lang': 'cn', 'floorId': this.floorId, 'currentFloorId': _0x22fb6e }, _0x4c646e.next = 8, Object(z.u)(_0x279656);
				case 8:
					_0x115e8b = _0x4c646e.sent, _0x3d0ff5 = _0x115e8b.result && _0x115e8b.result.childList || [[], []], this.$set(this.detailMap, _0x22fb6e, _0x3d0ff5), this.id = _0x22fb6e;
				case 12:
				case 'end':
					return _0x4c646e.stop();
			}
		}, _0x4c646e, this);
	}));
	return function (_0x1904c8, _0x4c90cf, _0x23567f) {
		return _0x4c646e.apply(this, arguments);
	};
});
