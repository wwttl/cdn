<?php
//同步微博
if( lmy_get_option('weibo') ) {
add_action('publish_post', 'new_post_weibo', 1);
function new_post_weibo($post_ID, $debug = true) {
  global $post;
  if (!wp_is_post_revision($post_ID) && $post->post_status != "publish" || $debug == true){
    if (isset($post) && $post->post_type != "post") return;
    $access_token = lmy_get_option('access_token');
    $headers = array();
    $headers[] = "Authorization: OAuth2 ".$access_token;
    $url = 'https://api.weibo.com/2/statuses/share.json';
        $blog_title = get_bloginfo('name');
        $WEB_DOMAIN=get_option('home');
        $status =  $blog_title."更新《".strip_tags( $_POST['post_title'] ) .'》'.mb_strimwidth(strip_tags(apply_filters('the_content',$_POST['post_content'])),0,85,".")."(文章原文来自".$WEB_DOMAIN.")→".get_permalink($post_ID);
    if (has_post_thumbnail()) {
      $post_thumbnail_id = wp_get_attachment_scr(get_post_thumbnail_id($post->ID));
      $img_src = $post_thumbnail_id;
    }else{
             $img_src= '';
      $content = get_post( $post_ID )->post_content;
            preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $strResult);
      $img_src = $strResult [1] [0];
    }
    if(!empty($img_src)) {
      $picfile = str_replace(home_url(),$_SERVER["DOCUMENT_ROOT"],$img_src);
      if(!empty($picfile)){
        $filecontent = file_get_contents($picfile);
      }else{
        $filecontent = file_get_contents($img_src);
      }
      $array = explode('?', basename($img_src));
      $filename = $array[0];
      $boundary = uniqid('------------------');
      $MPboundary = '--'.$boundary;
      $endMPboundary = $MPboundary. '--';
      $multipartbody = '';
      $multipartbody .= $MPboundary . "\r\n";
      $multipartbody .= 'Content-Disposition: form-data; name="pic"; filename="' . $filename . '"' . "\r\n";
      $multipartbody .= "Content-Type: image/unknown\r\n\r\n";
      $multipartbody .= $filecontent. "\r\n";
      $multipartbody .= $MPboundary . "\r\n";
      $multipartbody .= 'content-disposition: form-data; name="status' . "\"\r\n\r\n";
      $multipartbody .= urlencode($status)."\r\n";
      $multipartbody .= $endMPboundary;
      $headers[] = "Content-Type: multipart/form-data; boundary=" . $boundary;
      $data = $multipartbody;
    }else{
      $data = "status=" . urlencode($status);
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $curlResponse = curl_exec($ch);
    curl_close($ch);
    $output = json_decode($curlResponse);
    if($debug){
      var_dump($output);
      echo '<hr />';
      var_dump($data);
    }
  }
}
}