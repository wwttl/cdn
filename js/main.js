$(function(){
	$('.jdc-footer-channel-item').on('click', 'dt', function () {
		$(this).parent('.jdc-footer-channel-item').toggleClass('active');
	}), $('.jdc-footer-select').click(function () {
		$(this).toggleClass('active');
	}), $('.m-qrcode').on('click', '.m-qrcode-action', function () {
		$(this).parents('li').addClass('open');
	}), $('.m-qrcode').on('click', '.m-qrcode-pop-mark', function () {
		$(this).parents('li').removeClass('open');
	}), $('.m-app-tips').on('click', '.mat-download', function () {
		$(this).parents('.m-app-tips-ct').addClass('open');
	}), $('.m-app-tips').on('click', '.m-qrcode-pop-mark', function () {
		$(this).parents('.m-app-tips-ct').removeClass('open');
	});
});