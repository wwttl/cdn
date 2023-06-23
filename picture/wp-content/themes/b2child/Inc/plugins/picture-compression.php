<?php
// 图片压缩
if ( ! defined( 'ABSPATH' ) ) exit;
add_option( 'be_resizeupload_width', 				'1200', '', 'yes' );
add_option( 'be_resizeupload_height',				'1200', '', 'yes' );
add_option( 'be_resizeupload_quality',				'90', '', 'yes' );
add_option( 'be_resizeupload_resize_yesno', 		'yes', '','yes' );
add_option( 'be_resizeupload_recompress_yesno', 	'yes', '','yes' );

add_action( 'admin_menu', 'be_uploadresize_options_page' );

// 挂钩到上传
add_action( 'wp_handle_upload', 'be_uploadresize_resize' );

function be_uploadresize_options_page() {
	global $be_settings_page;
	if( function_exists( 'add_options_page' ) ) {
		$be_settings_page = add_options_page(
			'图片压缩',
			'<span class="bem"></span>图片压缩',
			'manage_options',
			'resize-after-upload',
			'be_uploadresize_options'
		);
	}
}

// 定义选项页面
function be_uploadresize_options() {
	if( isset( $_POST['be_options_update'] ) ) {
		if ( ! ( current_user_can( 'manage_options' ) && wp_verify_nonce( $_POST['_wpnonce'], 'be-options-update' ) ) ) {
			wp_die( "无权限" );
		}

		$resizing_enabled = ( $_POST['yesno'] == 'yes' ? 'yes' : 'no' );
		$force_jpeg_recompression   = ( $_POST['recompress_yesno'] == 'yes' ? 'yes' : 'no' );

		$max_width   = intval( $_POST['maxwidth'] );
		$max_height  = intval( $_POST['maxheight'] );
		$compression_level    = intval( $_POST['quality'] );

		// 如果输入不是整数，使用默认设置
		$max_width = ( $max_width == '' ) ? 0 : $max_width;
		$max_width = ( ctype_digit( strval( $max_width ) ) == false) ? get_option( 'be_resizeupload_width' ) : $max_width;
		update_option( 'be_resizeupload_width', $max_width );

		$max_height = ( $max_height == '' ) ? 0 : $max_height;
		$max_height = (ctype_digit(strval($max_height)) == false) ? get_option('be_resizeupload_height') : $max_height;
		update_option( 'be_resizeupload_height',$max_height );

		$compression_level = ( $compression_level == '' ) ? 1 : $compression_level;
		$compression_level = ( ctype_digit( strval( $compression_level ) ) == false ) ? get_option( 'be_resizeupload_quality' ) : $compression_level;

		if( $compression_level < 1 ) {
			$compression_level = 1;
		} else if( $compression_level > 100 ) {
			$compression_level = 100;
		}

		update_option( 'be_resizeupload_quality', $compression_level );

		if ( $resizing_enabled == 'yes' ) {
			update_option( 'be_resizeupload_resize_yesno','yes' );
		} else {
			update_option( 'be_resizeupload_resize_yesno','no' );
		}

		if ( $force_jpeg_recompression == 'yes' ) {
			update_option( 'be_resizeupload_recompress_yesno','yes' );
		} else {
			update_option( 'be_resizeupload_recompress_yesno','no' );
		}
	    echo('<div id="message" class="updated fade"><p><strong>设置已保存。</strong></p></div>');
	}

	// 设置表单
	$resizing_enabled = get_option( 'be_resizeupload_resize_yesno' );
	$force_jpeg_recompression = get_option( 'be_resizeupload_recompress_yesno' );
	$compression_level  = intval( get_option('be_resizeupload_quality' ) );

	$max_width = get_option( 'be_resizeupload_width' );
	$max_height = get_option( 'be_resizeupload_height' );
	?>

	<div class="wrap">
		<form method="post" accept-charset="utf-8">
			<h1>图片压缩设置</h1>
			<h3>大小设置</h3>
			<table class="form-table">
				<tr>
					<th scope="row">调整大小</th>
					<td valign="top">
						<select name="yesno" id="yesno">
							<option value="no" label="否" <?php echo ( $resizing_enabled == 'no' ) ? 'selected="selected"' : ''; ?>></option>
							<option value="yes" label="是" <?php echo ( $resizing_enabled == 'yes' ) ? 'selected="selected"' : ''; ?>></option>
						</select>
					</td>
				</tr>

				<tr>
					<th scope="row">最大图片尺寸</th>
					<td>
						<label for="maxwidth">最大宽度</label>
						<input name="maxwidth" step="1" min="0" id="maxwidth" class="small-text" type="number" value="<?php echo $max_width; ?>">
						&nbsp;&nbsp;&nbsp;<label for="maxheight">最大高度</label>
						<input name="maxheight" step="1" min="0" id="maxheight" class="small-text" type="number" value="<?php echo $max_height; ?>">
						<p class="description">设置为零即不调整大小</p>
					</td>
				</tr>
			</table>

			<h3>JPEG 压缩设置</h3>
			<table class="form-table">
				<tr>
					<th scope="row">压缩级别</th>
					<td valign="top">
						<select id="quality" name="quality">
						<?php for( $i=1; $i<=100; $i++ ) : ?>
							<option value="<?php echo $i; ?>" <?php if( $compression_level == $i ) : ?>selected<?php endif; ?>><?php echo $i; ?></option>
						<?php endfor; ?>
						</select>
					</td>
				</tr>

				<tr>
					<th scope="row">重新压缩</th>
					<td>
						<select name="recompress_yesno" id="yesno">
							<option value="no" label="否" <?php echo ($force_jpeg_recompression == 'no') ? 'selected="selected"' : ''; ?>></option>
							<option value="yes" label="是" <?php echo ($force_jpeg_recompression == 'yes') ? 'selected="selected"' : ''; ?>></option>
						</select>
					</td>
				</tr>
			</table>

			<p class="submit">
				<input type="hidden" id="convert-bmp" name="convertbmp" value="no" />
				<input type="hidden" name="action" value="update" />
				<?php wp_nonce_field('be-options-update'); ?>
				<input id="submit" name="be_options_update" class="button button-primary" type="submit" value="保存更改">
			</p>
		</form>
	</div>
	<?php
}

// 处理图片函数
function be_uploadresize_resize( $image_data ) {
	$resizing_enabled = get_option( 'be_resizeupload_resize_yesno' );
	$resizing_enabled = ( $resizing_enabled =='yes' ) ? true : false;

	$force_jpeg_recompression = get_option( 'be_resizeupload_recompress_yesno' );
	$force_jpeg_recompression = ( $force_jpeg_recompression=='yes' ) ? true : false;

	$compression_level = get_option( 'be_resizeupload_quality' );

	$max_width  = get_option( 'be_resizeupload_width' )==0 ? false : get_option( 'be_resizeupload_width' );
	$max_height = get_option( 'be_resizeupload_height' )==0 ? false : get_option( 'be_resizeupload_height' );

	if ( $resizing_enabled || $force_jpeg_recompression ) {
		$fatal_error_reported = false;
		$valid_types = array( 'image/gif','image/png','image/jpeg','image/jpg' );

		if ( empty( $image_data['file'] ) || empty( $image_data['type'] ) ) {
			$fatal_error_reported = true;
		} else if( !in_array( $image_data['type'], $valid_types ) ) {
			$fatal_error_reported = true;
		}

		$image_editor = wp_get_image_editor( $image_data['file'] );
		$image_type = $image_data['type'];

		if ( $fatal_error_reported || is_wp_error( $image_editor ) ) {
		} else {
			$to_save = false;
			$resized = false;


			// 如果需要，执行调整大小
			if ( $resizing_enabled ) {
				$sizes = $image_editor->get_size();

				if ( ( isset( $sizes['width'] ) && $sizes['width'] > $max_width ) || ( isset( $sizes['height']) && $sizes['height'] > $max_height ) ) {
					$image_editor->resize($max_width, $max_height, false);
					$resized = true;
					$to_save = true;
					$sizes = $image_editor->get_size();
				}
			}

			// 无论调整大小，重新压缩时都必须保存图像
			if ( $force_jpeg_recompression && ( $image_type=='image/jpg' || $image_type=='image/jpeg' ) ) {
				$to_save = true;
			}

			// 仅在调整大小或需要重新压缩时才保存图像
			if ( $to_save ) {
				$image_editor->set_quality( $compression_level );
				$saved_image = $image_editor->save( $image_data['file'] );
			}
		}
	}
	return $image_data;
}

function be_uploadresize_convert_image( $params, $compression_level ) {
	$transparent = 0;
	$image = $params['file'];

	$contents = file_get_contents( $image );
	if ( ord ( file_get_contents( $image, false, null, 25, 1 ) ) & 4 ) $transparent = 1;
	if ( stripos( $contents, 'PLTE' ) !== false && stripos( $contents, 'tRNS' ) !== false ) $transparent = 1;

	$transparent_pixel = $img = $bg = false;
	if ( $transparent ) {
		$img = imagecreatefrompng( $params['file'] );
		$w = imagesx( $img ); // Get the width of the image
		$h = imagesy( $img ); // Get the height of the image
	    //run through pixels until transparent pixel is found:
		for( $i = 0; $i<$w; $i++ ) {
			for( $j = 0; $j < $h; $j++ ) {
				$rgba = imagecolorat( $img, $i, $j );
				if( ( $rgba & 0x7F000000) >> 24 ) {
					$transparent_pixel = true;
					break;
				}
			}
		}
	}

	if ( !$transparent || !$transparent_pixel ) {
		if (!$img) $img = imagecreatefrompng( $params['file'] );
		$bg = imagecreatetruecolor(imagesx( $img ), imagesy( $img ) );
		imagefill( $bg, 0, 0, imagecolorallocate( $bg, 255, 255, 255 ) );
		imagealphablending( $bg, 1 );
		imagecopy( $bg, $img, 0, 0, 0, 0, imagesx( $img ), imagesy( $img ) );
		$newPath = preg_replace("/\.png$/", ".jpg", $params['file'] );
		$newUrl = preg_replace( "/\.png$/", ".jpg", $params['url'] );
		for($i = 1; file_exists( $newPath ); $i++) {
			$newPath = preg_replace( "/\.png$/", "-".$i.".jpg", $params['file'] );
		}
		if ( imagejpeg( $bg, $newPath, $compression_level ) ){
			unlink( $params['file'] );
			$params['file'] = $newPath;
			$params['url'] = $newUrl;
			$params['type'] = 'image/jpeg';
		}
	}
	return $params;
}