<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSP_Admin' ) ) :

    class WPSP_Admin {

        /**
         * Constructor
         */
        public function __construct() {

            add_action( 'admin_menu', array($this,'custom_menu_page') );
						add_action( 'admin_init', array($this,'wpsp_redirect') );
						add_action( 'admin_menu', array($this,'custom_menu_page') );
            add_action( 'admin_enqueue_scripts', array( $this, 'loadScripts') );
            add_action( 'wpsp_setting_update', array($this,'setting_update') );
            add_action( 'wpsp_setting_update', array($this,'setting_update_alert'), 99 );
            add_filter( 'admin_footer_text', array($this,'footer_text') );
            add_action( 'delete_user', array($this,'delete_user') );
            add_action( 'admin_notices', array($this,'check_installation') );
            add_action( 'wp_logout', array($this,'wpsp_logout') );
						add_action( 'profile_update',array($this, 'my_profile_update'),10, 2 );
						add_action( 'admin_footer', array($this,'print_inline_script') );
      	}
				
				function print_inline_script() {
					if ( wp_script_is( 'jquery', 'done' ) ) {
						?>
						<script type="text/javascript">
							var wpspjq=jQuery.noConflict();
						</script>
						<?php
					}
				}

        /**
         * Check installation
         */
        public function check_installation(){

            $flag = true;
            if( isset($_REQUEST['action']) && $_REQUEST['action'] == 'wpsp_installation' ){

                $flag = false;

            } else if( isset($_REQUEST['action']) && $_REQUEST['action'] == 'wpsp_upgrade' ){

                $flag = false;

            }

            if( $flag && get_option( 'wpsp_installation' ) < 5 ){

                $installation_url = 'admin.php?page=wp-support-plus&action=wpsp_installation'
                ?>
                <div class="update-nag notice" style="width: 100%; box-sizing: border-box;">
                    <p>Please <a href="<?php echo htmlentities($installation_url)?>">click here</a> to complete installation of WP Support Plus.</p>
                </div>
                <?php
            }
        }

        /**
         * Add dashboard menu(s)
         */
        public function custom_menu_page(){

            add_menu_page(
                'WP Support Plus',
                __('Support Plus','wp-support-plus-responsive-ticket-system'),
                'manage_options',
                'wp-support-plus',
                array($this, 'settings'),
                'dashicons-admin-settings',
                '81.66'
            );
        }

				function wpsp_redirect(){

					global $wpdb;
					if( isset($_REQUEST['action']) && $_REQUEST['action'] == 'wpsp_installation' ){
						$coloums = $wpdb->get_results("SHOW COLUMNS FROM {$wpdb->prefix}wpsp_ticket like 'status'");
						if( count($coloums) && !(get_option( 'wpsp_upgrade' ) && get_option( 'wpsp_upgrade' ) == 10 )) {
								wp_redirect( 'admin.php?page=wp-support-plus&action=wpsp_upgrade' );
						    exit;
						}
					}
					if( isset($_REQUEST['update_setting']) ) :
					    
					    $setting = sanitize_text_field( $_REQUEST['update_setting'] );
					    
					    switch ( $setting ) {
								
								case 'custom_menu_add':
									include_once WPSP_ABSPATH.'includes/admin/general/custom-menu/add_custom_menu.php';
									break;
						
								case 'custom_menu_update':
									include_once WPSP_ABSPATH.'includes/admin/general/custom-menu/update_custom_menu.php';
									break;
									
				        case 'settings_support_btn':
									include_once WPSP_ABSPATH.'includes/admin/general/custom-menu/update_custom_menu_order.php';
									break;
									
								case 'support_page_menu_add':
									include_once WPSP_ABSPATH.'includes/admin/general/support-page-menu/add_sp_menu.php';
									break;
									
								case 'support_page_menu_update':
									include_once WPSP_ABSPATH.'includes/admin/general/support-page-menu/update_sp_menu.php';
									break;
									
								case 'support_page_menu':
									include_once WPSP_ABSPATH.'includes/admin/general/support-page-menu/sp_menu_order.php';
									break;
							}
					endif;
					
				}

        /**
         * Load admin style and script
         */
        public function loadScripts(){

            if( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'wp-support-plus' ) :

                wp_enqueue_script( 'jquery' );
								wp_enqueue_media();
                wp_enqueue_script( 'jquery-ui-core' );
                wp_enqueue_script( 'wpspjq-ui-sortable');
                wp_enqueue_script( 'wp-color-picker' );
								wp_enqueue_style( 'wp-color-picker' );
                wp_enqueue_script( 'wpsp-admin', WPSP_PLUGIN_URL.'asset/js/admin.js?version='.WPSP_VERSION );
                wp_enqueue_style('wpsp-admin-css', WPSP_PLUGIN_URL . 'asset/css/admin.css?version='.WPSP_VERSION );
                wp_enqueue_style('wpspjq-ui-css', WPSP_PLUGIN_URL . 'asset/css/wpspjq-ui.min.css?version='.WPSP_VERSION);
                wp_enqueue_style('wpspjq-ui-structure-css', WPSP_PLUGIN_URL . 'asset/css/wpspjq-ui.structure.min.css?version='.WPSP_VERSION);
                wp_enqueue_style('wpspjq-ui-theme-css', WPSP_PLUGIN_URL . 'asset/css/wpspjq-ui.theme.min.css?version='.WPSP_VERSION);

                $loading_html = '<div class="wpsp_filter_loading_icon"><img src="'.WPSP_PLUGIN_URL.'asset/images/ajax-loader@2x.gif"></div>';

                $localize_script_data = apply_filters( 'wpsp_admin_localize_script', array(
                    'ajax_url'              => admin_url( 'admin-ajax.php' ),
                    'filter_loading_html'   => $loading_html,
										'required'							=> __('Please enter required field.', 'wp-support-plus-responsive-ticket-system'),
										'confirm'								=> __('Are you sure?', 'wp-support-plus-responsive-ticket-system'),
                ));

                wp_localize_script( 'wpsp-admin', 'wpsp_admin', $localize_script_data );

            endif;
        }

        /**
         * Settings for WP Support Plus
         */
        public function settings(){
						global $wpdb;
						$coloums = $wpdb->get_results("SHOW COLUMNS FROM {$wpdb->prefix}wpsp_ticket like 'status'");
						if( count($coloums) && !(get_option( 'wpsp_upgrade' ) && get_option( 'wpsp_upgrade' ) == 10 )) {
							
								include_once( WPSP_ABSPATH . 'includes/admin/installation/wpsp_upgrade.php' );
								
						}else if( (isset($_REQUEST['action']) && $_REQUEST['action'] == 'wpsp_installation') || get_option( 'wpsp_installation' ) < 5 ){

                include_once( WPSP_ABSPATH . 'includes/admin/installation/wpsp_install.php' );

            } else if( isset($_REQUEST['action']) && $_REQUEST['action'] == 'wpsp_upgrade' ){

                include_once( WPSP_ABSPATH . 'includes/admin/installation/wpsp_upgrade.php' );

            } else {
								
								$settings = $this->get_settings();
                $setting_page = isset( $_REQUEST['setting'] ) ? sanitize_text_field( $_REQUEST['setting'] ) : 'general';
                ?>

                <div class="wrap wrap-general">
                    <h2><?php _e('WP Support Plus Settings','wp-support-plus-responsive-ticket-system');?></h2>

                    <?php isset( $_REQUEST['action'] ) && $_REQUEST['action']=='update' ? do_action('wpsp_setting_update') : ''?>

                    <h2 class="nav-tab-wrapper">
                        <?php foreach ( $settings as $key => $tab ):
                                $tab_class=($key==$setting_page)?'nav-tab nav-tab-active':'nav-tab';
                                $tab_href='admin.php?page=wp-support-plus&setting='.$key;?>

                        <a href="<?php echo htmlentities($tab_href)?>" class="<?php echo $tab_class?>"><?php echo $tab['label']?></a>

                        <?php endforeach;?>
                    </h2>

                    <?php
                    if(isset($settings[$setting_page])){
                        include( $settings[$setting_page]['file'] );
                    }
                    ?>
                </div>
                <?php
            }
        }

        /**
         * Return primary settings array
         */
        private function get_settings(){

            $settings = array (

                'general' => array(
                    'label' => __('General','wp-support-plus-responsive-ticket-system'),
                    'file'  => WPSP_ABSPATH . 'includes/admin/general/general.php'
                ),
                'ticket-form' => array(
                    'label' => __('Ticket Form','wp-support-plus-responsive-ticket-system'),
                    'file'  => WPSP_ABSPATH . 'includes/admin/ticket-form/ticket-form.php'
                ),
                'ticket-list' => array(
                    'label' => __('Ticket List','wp-support-plus-responsive-ticket-system'),
                    'file'  => WPSP_ABSPATH . 'includes/admin/ticket-list/ticket-list.php'
                ),
                'agents' => array(
                    'label' => __('Agents','wp-support-plus-responsive-ticket-system'),
                    'file'  => WPSP_ABSPATH . 'includes/admin/agents/agents.php'
                ),
                'dashboard' => array(
                    'label' => __('Dashboard','wp-support-plus-responsive-ticket-system'),
                    'file'  => WPSP_ABSPATH . 'includes/admin/dashbord/dashbord.php'
                ),
								'customize' => array(
                    'label' => __('Customize','wp-support-plus-responsive-ticket-system'),
                    'file'  => WPSP_ABSPATH . 'includes/admin/customize/customize.php'
                ),
                'addons' => array(
                    'label' => __('Add-Ons','wp-support-plus-responsive-ticket-system'),
                    'file'  => WPSP_ABSPATH . 'includes/admin/addons/addons.php'
                )

            );

            return apply_filters( 'wpsp_primary_settings', $settings );
        }

        /**
         * Settings update action
         */
        public function setting_update(){

            include WPSP_ABSPATH . 'includes/admin/setting_update.php';
        }

        /**
         * Settings update alert. Performed after setting update done.
         */
        public function setting_update_alert(){

            ?>
            <div id="setting-error-" class="updated settings-error notice is-dismissible">
                <p><strong><?php _e('Settings updated','wp-support-plus-responsive-ticket-system')?>.</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e('Dismiss this notice','wp-support-plus-responsive-ticket-system')?>.</span></button>
            </div>
            <?php
        }

        /**
         * Footer text on WPSP Settings page
         */
        public function footer_text($text){

            if( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'wp-support-plus' ) :

                $text = 'Thank you for using WP Support Plus.<br>'
                . 'Visit our <a href="https://www.wpsupportplus.com/" target="__blank">website</a> for documentation, support and offers.<br>'
                . 'Give us a <a href="https://wordpress.org/support/plugin/wp-support-plus-responsive-ticket-system/reviews/#new-post" target="__blank"><strong>5 Star</strong></a> rating if you are happy with us. . A huge thanks in advance!';

            endif;

            return $text;
        }

        /**
         * Delete user
         * Mark tickets of user being deleted as guest tickets
         * If user is agent, delete agent from wpsp_users
         * If user is supervisor, remove from categories where he assigned and also delete from wpsp_users
         */
        public function delete_user($user_id){
          
            include WPSP_ABSPATH . 'includes/admin/user-actions/delete_user.php';
						
						  $values=array(
								 'created_by' => 0,
								 'type'       => 'guest'
							);
									 
							$wpdb->update($wpdb->prefix.'wpsp_ticket', $values, array('created_by'=>$user_id));

				}

        public function wpsp_logout(){
            if(isset($_COOKIE["wpsp_user_session"])){
                unset($_COOKIE["wpsp_user_session"]);
                setcookie("wpsp_user_session", null, strtotime('-1 day'), COOKIEPATH);
            }
        }
      
				function my_profile_update( $user_id, $old_user_data ) {
	         global $wpdb;
					 
					 $latest_data=get_userdata($user_id);
					 $new_email=$latest_data->user_email;
					 $old_email=$old_user_data->data->user_email;
					 
					 $values=array(
					    'guest_email'=>$new_email
					 );

					 $wpdb->update($wpdb->prefix.'wpsp_ticket', $values, array('guest_email'=>$old_email));
					 $wpdb->update($wpdb->prefix.'wpsp_ticket_thread', $values, array('guest_email'=>$old_email));

       }
			 
		}
			
endif;


new WPSP_Admin();
