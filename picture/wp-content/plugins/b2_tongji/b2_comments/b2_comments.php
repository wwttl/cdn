<?php
add_action( 'restrict_manage_comments', function() {
    $user = isset( $_GET[ 'tj_user' ]) ? $_GET[ 'tj_user' ] : '';
    echo '<input type="search" placeholder="用户ID" name="tj_user" value="'.$user.'" style="float:none;margin-right:6px">';
} );

add_action('pre_get_comments',function($comments){
    global $pagenow;
    $user = isset( $_GET[ 'tj_user' ]) ? $_GET[ 'tj_user' ] : 0;
    if($pagenow == 'edit-comments.php' && is_super_admin() && $user){
        $comments->query_vars['user_id'] = $user;
    }
    return $comments;
});



//https://rudrastyh.com/wordpress/comments-table-columns.html
add_filter( 'manage_edit-comments_columns', 'rudr_add_comments_columns' );
function rudr_add_comments_columns( $my_cols ){
	// $my_cols is the array of all column IDs and labels
	// if you know arrays, you can add, remove or change column order with no problems
	// like this:
	/*
	$my_cols = array(
		'cb' => '', // do not forget about the CheckBox
		'author' => 'Author',
		'comment' => 'Comment',
		'm_comment_id' => 'ID', // added 
		'm_parent_id' => 'Parent ID', // added
		'response' => 'In reply to',
		'date' => 'Date'
	);
	*/
	// but the above way is not so good - there could be problems when plugins would like to hook the comment columns
	// so, better like this:
	$misha_columns = array(
	    'm_comment_user_id' => 'User_ID',
		'm_comment_id' => 'Commment_ID',
		'm_parent_id' => 'Parent ID'
	);
	$my_cols = array_slice( $my_cols, 0, 3, true ) + $misha_columns + array_slice( $my_cols, 3, NULL, true );
 
	// if you want to remove a column, you can just use:
	// unset( $my_cols['response'] );
 
	// return the result
	return $my_cols;
}
add_action( 'manage_comments_custom_column', 'rudr_add_comment_columns_content', 10, 2 );
function rudr_add_comment_columns_content( $column, $comment_ID ) {
	global $comment;
	//var_dump($comment);
	switch ( $column ) :
	    case 'm_comment_user_id' : {
			echo $comment->user_id; // or echo $comment->comment_ID;
			break;
		}
		case 'm_comment_id' : {
			echo $comment_ID; // or echo $comment->comment_ID;
			break;
		}
		case 'm_parent_id' : {
			// try to print_r( $comment ); to see more comment information
			echo $comment->comment_parent; // this will be printed inside the column
			break;
		}
	endswitch;
}
