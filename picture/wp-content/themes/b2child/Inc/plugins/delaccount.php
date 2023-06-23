<?php
if (!class_exists('B2_DC')) {
    class B2_DC{
        public function __construct(){
            add_action('generate_rewrite_rules',array($this,'rewrite_rules') );
            add_action('query_vars',array($this,'add_query_vars') );
            add_action('template_redirect',array($this,'template_redirect') );
            add_filter('redirect_canonical',array($this,'pagecanonical') );
            add_action('wp_enqueue_scripts',array($this ,'support_script_css'),99);
        }
        public function add_query_vars($public_query_vars){     
            $public_query_vars[] = 'b2_del';
            return $public_query_vars;     
        } 
        public function rewrite_rules( $wp_rewrite ){   
            $new_rules = array(    
                'delaccount/?$' => 'index.php?b2_del=del',   
            );
            $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;   
        }    
        public function template_redirect(){   
            global $wp;   
            global $wp_query, $wp_rewrite;   
            $reditect_page =  isset($wp_query->query_vars['b2_del']) ? $wp_query->query_vars['b2_del'] : '';
            if ($reditect_page == "del"){   
                include get_theme_file_path('Pages/delaccount.php');
                die();   
            }   
        }
        public function pagecanonical($redirect_url){
            if( get_query_var('b2_del')) return false;
            return $redirect_url;
        }
        public function support_script_css(){
            global $wp_query;
            $page = $wp_query->get('b2_del');
            if($page=='del'){
            	wp_enqueue_script( 'b2_del-ele', 'https://cdn.bootcss.com/element-ui/2.13.1/index.js' ,array(), '1.0' ,true );
            	wp_enqueue_style( 'b2_del-ele-css', 'https://cdn.bootcss.com/element-ui/2.13.1/theme-chalk/index.css' ,array(), '1.0' ,'all' );
                wp_enqueue_script( 'b2_del', B2_CHILD_URI .'/Assets/Js/del.js' ,array(), '1.0' ,true );
                
            }
        }
        
    }
    new B2_DC();
}

add_action( 'rest_api_init', function () {
  register_rest_route( 'b2delc/v1', '/delc/', array(
    'methods' => 'POST',
    'callback' => function($request){
		$user_id = b2_get_current_user_id();
		if(!$user_id){
		   return new \WP_Error('b2_login',__('登录已过期，请重新登录','b2'),array('status'=>401)) ;
        }
        require_once(ABSPATH.'wp-admin/includes/user.php');
        wp_delete_user($user_id);
		return true;
    },
  ) );
});