<?php
/**
 * 
 * description: 原始数据接口转化，终究还是变成了你要我成为的模样，我终究还是丢了自己!
 * 
**/
add_action( 'rest_api_init', 'rest_B2Danmu_allinfo_rate');
function rest_B2Danmu_allinfo_rate()
{
	register_rest_route('b2/v1','/B2Danmu_allinfo_data',array(
		'methods'=>'POST',
		'callback'=>'B2Danmu_allinfo_data',
	));
}
