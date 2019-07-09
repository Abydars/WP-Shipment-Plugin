<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSP_Redirect_SC' ) ) :
    
    class WPSP_Redirect_SC {
        
        /**
         * Constructor
         */
        public function __construct(){
            
            add_action( 'admin_notices', array( $this, 'show_notice' ) );
            
        }
        
        public function show_notice() {
          
            $action       = 'install-plugin';
            $slug         = 'supportcandy';
            $install_link = wp_nonce_url(
                add_query_arg(
                    array(
                        'action' => $action,
                        'plugin' => $slug
                    ),
                    admin_url( 'update.php' )
                ),
                $action.'_'.$slug
            );
            
            ?>
            <div class="notice notice-error">
                <h1>WP Support Plus</h1>
                <p style="font-size:16px;">
                  This project is being maintained for its existing users.
                  You need to install our latest helpdesk <b><a target="_blank" href="https://wordpress.org/plugins/supportcandy/">SupportCandy</a></b> - developed by same team which comes with more features and easy to use user interface.<br>
                  Click below button to install SupportCandy. Once you installed and activated it, remove WP Support Plus from your plugins.
                </p>
                <a href="<?php echo $install_link?>" class="button button-primary" style="margin-bottom:15px;">Install Now</a>
            </div>
            <?php
        }
				
		}
    
endif;

new WPSP_Redirect_SC();