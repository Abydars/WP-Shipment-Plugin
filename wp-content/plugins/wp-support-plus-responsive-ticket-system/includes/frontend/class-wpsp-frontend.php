<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSP_Frontend' ) ) :

    class WPSP_Frontend {

        /**
         * Constructor
         */
        public function __construct() {

            add_action( 'wp_enqueue_scripts', array($this,'load_scripts') );
						add_action( 'wp_footer', array($this,'print_inline_script'), 899999999999 );
            add_action( 'wp_footer', array($this,'support_button') );
            add_filter( 'template_include', array($this,'load_template'), 99 );
						add_shortcode( 'wp_support_plus', array( $this, 'integrated_template' ) );
						add_action( 'wpsp_footer_text',array($this, 'wpsp_footer_text_data'));
						add_action( 'init', array($this,'check_login') );
            add_action( 'init', array($this,'check_download_file') );
            add_action( 'wp_logout', array($this,'wpsp_logout') );
        }

        /**
         * Load script for all web-site excluding support page
         */
        public function load_scripts(){
            
					 global $post, $wpsupportplus;
					 $flag = false;
					 wp_enqueue_script( 'jquery' );
					 wp_enqueue_script( 'jquery-ui-core' );
					 if ((isset($post) && $wpsupportplus->functions->get_support_page_id()==$post->ID) || apply_filters('wpsp_jqueryui_files_include',$flag)) {
							wp_enqueue_script( 'jquery-ui-datepicker' );
							wp_enqueue_script( 'jquery-ui-autocomplete' );
							wp_enqueue_editor();
						}
						
        }
				
				function print_inline_script() {
					
					global $post, $wpsupportplus;
					$flag = false;
					?>
					
					<script type="text/javascript">
						var wpspjq=jQuery.noConflict();
					</script>
					
					<link href="<?php echo WPSP_PLUGIN_URL.'asset/css/support_btn.css?version='.WPSP_VERSION;?>" rel="stylesheet">
					<script src="<?php echo WPSP_PLUGIN_URL.'asset/js/support_btn.js?version='.WPSP_VERSION;?>" type="text/javascript"></script>
					
					<?php if ((isset($post) && $wpsupportplus->functions->get_support_page_id()==$post->ID) || apply_filters('wpsp_js_files_include',$flag)) {?>
						<link href="<?php echo WPSP_PLUGIN_URL.'asset/library/jquery-ui/jquery-ui.structure.min.css?version='.WPSP_VERSION;?>" rel="stylesheet">
				    <link href="<?php echo WPSP_PLUGIN_URL.'asset/library/jquery-ui/jquery-ui.theme.min.css?version='.WPSP_VERSION;?>" rel="stylesheet">
						<link href="<?php echo WPSP_PLUGIN_URL.'asset/library/font-awesome/css/font-awesome.min.css?version='.WPSP_VERSION;?>" rel="stylesheet">
				    <link href="<?php echo WPSP_PLUGIN_URL.'asset/library/ImageViewer/imageviewer.css?version='.WPSP_VERSION;?>" rel="stylesheet">
				    <link href="<?php echo WPSP_PLUGIN_URL.'asset/css/public.css?version='.WPSP_VERSION;?>" rel="stylesheet">
						<script src="<?php echo WPSP_PLUGIN_URL.'asset/library/ImageViewer/imageviewer.min.js?version='.WPSP_VERSION;?>" type="text/javascript"></script>
				    <script src="<?php echo WPSP_PLUGIN_URL.'asset/js/public.js?version='.WPSP_VERSION;?>"></script>
					<?php }?>
					
					<?php if ((isset($post) && $wpsupportplus->functions->get_support_page_id()==$post->ID && $wpsupportplus->functions->load_bootstrap()) || apply_filters('wpsp_bootstrap_files_include',$flag)) {?>
						<link href="<?php echo WPSP_PLUGIN_URL.'asset/library/bootstrap/css/bootstrap-iso.css?version='.WPSP_VERSION;?>" rel="stylesheet">
						<script src="<?php echo WPSP_PLUGIN_URL.'asset/library/bootstrap/js/bootstrap.min.js?version='.WPSP_VERSION;?>"></script>
					<?php }?>
					
					<?php
					do_action('wpsp_enqueue_scripts');
					
				}

        /**
         * Support Button template
         */
        public function support_button(){
            include WPSP_ABSPATH . 'includes/frontend/support_button.php';
        }

        /**
         * Load front end template
         */
        public function load_template($template){

            global $wpsupportplus, $post;
						$current_permelink = '';
						if(!empty($post)){
							$current_permelink = get_permalink( $post->ID );
						}
						if( $current_permelink == $wpsupportplus->functions->get_support_page_url() && !$wpsupportplus->functions->is_integrate_theme() ){
								$template = WPSP_ABSPATH . 'template/index.php';
						}
						return $template;
        }
				
				public function integrated_template(){
						
						global $wpsupportplus;
						ob_start();
						include WPSP_ABSPATH . 'template/integrated_template.php';
						return ob_get_clean();
				}

        /*
         * Footer  Text
         */
        public function wpsp_footer_text_data(){
            global $wpsupportplus;
            echo do_shortcode(wpautop(stripslashes($wpsupportplus->functions->get_footer_text())));
        }

				function check_login(){
				    
						global $wpdb, $wpsupportplus, $current_user;
						
						$wpsp_user_session = $wpsupportplus->functions->get_current_user_session();
				    
						$redirect_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
						$redirect_link = preg_match('/\?/', $redirect_link) ? $redirect_link.'&dc='.time() : $redirect_link.'?dc='.time();
						
				    if (isset($_REQUEST['redirect_to'])) {
				        $redirect_link = urldecode($_REQUEST['redirect_to']);
								$redirect_link = preg_match('/\?/', $redirect_link) ? $redirect_link.'&dc='.time() : $redirect_link.'?dc='.time();
				    }
				    
				    $post_id = url_to_postid( $redirect_link );
				    
				    if( $post_id == $wpsupportplus->functions->get_support_page_id() ){

				        $login_req_pages = apply_filters( 'wpsp_login_req_pages', array('tickets') );
				        $current_page    = isset($_REQUEST['page']) ? sanitize_text_field($_REQUEST['page']) : 'tickets';
				        $sign_in_url     = $wpsupportplus->functions->get_support_page_url(array('page'=>'sign-in','redirect_to'=>urlencode($redirect_link),'dc'=>time()));
								
								if( in_array($current_page,$login_req_pages) && !$wpsp_user_session ){
				            wp_redirect( $sign_in_url );
				            exit;
				        }
								
								if( ($current_page == 'sign-out' || !$wpsp_user_session) && $current_page != 'sign-in' && $current_page != 'knowledgebase' && $current_page != 'faq'){

                    unset($_COOKIE["wpsp_user_session"]);
                    setcookie("wpsp_user_session", null, strtotime('-1 day'), COOKIEPATH);

                    if(isset($_COOKIE["wpsp_ticket_filters"])){
                        unset($_COOKIE["wpsp_ticket_filters"]);
                        setcookie("wpsp_ticket_filters", null, strtotime('-1 day'), COOKIEPATH);
                    }

                    if( is_user_logged_in() ){
                        wp_logout();
                    }

                    wp_redirect( site_url() );
                    exit;

                }
				        
				        if ( $current_page == 'sign-in' && isset($_REQUEST['action']) && $_REQUEST['action'] == 'wpsp_signin' ) {
				            include_once( WPSP_ABSPATH . 'includes/ajax/user-login/signin.php' );
				        }
								
								if ( $current_page == 'sign-in' && isset($_REQUEST['action']) && $_REQUEST['action'] == 'wpsp_guest_signin' ) {
				            include_once( WPSP_ABSPATH . 'includes/ajax/user-login/guest_signin.php' );
				        }
								
								if ( $current_page == 'sign-in' && !isset($_REQUEST['action']) && $wpsp_user_session ) {
										$support_url = $wpsupportplus->functions->get_support_page_url(array('dc'=>time()));
										wp_redirect( $support_url );
										exit;
				        }
								
						}
				    
				}

        /**
         * Download file attachment
         */
        public function check_download_file(){

            global $wpdb;

            if( isset($_REQUEST['wpsp_attachment']) ){

                $attach_id = intval(sanitize_text_field($_REQUEST['wpsp_attachment']));

                if( $attach_id ){

                    $attachment = $wpdb->get_row( "select * from {$wpdb->prefix}wpsp_attachments where id=".$attach_id );

                    $upload_dir = wp_upload_dir();

                    $filepath     = $attachment->filepath;
                    $filepath     = explode('/', $filepath);
                    $file_name    = $filepath[count($filepath)-1];
                    $filepath     = $upload_dir['basedir'] . '/wpsp/'. $file_name;
                    $content_type = $attachment->filetype;

										header('Content-Description: File Transfer');
								    header('Cache-Control: public');
								    header('Content-Type: '.$content_type);
								    header("Content-Transfer-Encoding: binary");
								    header('Content-Disposition: attachment; filename='. $attachment->filename);
								    header('Content-Length: '.filesize($filepath));
								   	flush();
								    readfile($filepath);
										exit(0);
                }
            }
        }
				
	      public function wpsp_logout(){
            if(isset($_COOKIE["wpsp_user_session"])){
                unset($_COOKIE["wpsp_user_session"]);
                setcookie("wpsp_user_session", null, strtotime('-1 day'), COOKIEPATH);
            }
        }
				
				public function wpsp_guest_login_redirect(){
		
					if ( isset($_REQUEST['wpsp_guest_signin_response']) ) {
						?>
						<script type="text/javascript">
							
							<?php if($_REQUEST['wpsp_guest_signin_response']['success']):?>
									window.location.href = '<?php echo urldecode($_REQUEST['redirect_to'])?>';
							<?php endif;?>
							
							<?php if(!$_REQUEST['wpsp_guest_signin_response']['success']):?>
									window.location.href = '<?php echo urldecode($_REQUEST['redirect_to'])?>';
							<?php endif;?>
							
						</script>
						<?php
					}
				}
				
    }

endif;

if( get_option( 'wpsp_installation' ) == 5 ){

    new WPSP_Frontend();

}
