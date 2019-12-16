<?php
/*
Plugin Name: WPSP Ticket Support
Plugin URI: hztech.biz
description: Ticket support
Version: 1.0
Author: Hztech
Author URI: hztech.biz
*/

/*
 * Notes:
 * Remove current user condition in /includes/ajax/submit_ticket.php:50
 */

if ( ! class_exists( 'WPTS_TicketSupport' ) ) {
	class WPTS_TicketSupport
	{
		public function __construct()
		{
			add_action( 'init', array( $this, 'handle_fax' ) );
			add_action( 'wpsp_after_ticket_reply', array( $this, 'wpsp_after_ticket_reply' ), 10, 2 );
			add_action( 'wp_ajax_create_pickups', array( $this, 'create_pickups' ) );
			add_action( 'wp_ajax_nopriv_create_pickups', array( $this, 'create_pickups' ) );
			add_action( 'admin_bar_menu', array( $this, 'add_toolbar_items' ), 100 );
			add_action( 'admin_menu', array( $this, 'admin_menu' ), 5 );
			add_action( 'wp_footer', array( $this, 'refresh_support_tickets' ) );
			add_action( 'wp_footer', array( $this, 'add_attachment_button' ) );
			add_action( 'wp_ajax_get_file_url', array( $this, 'render_get_file_url' ) );
		}

		function render_get_file_url()
		{
			if ( isset( $_REQUEST['link'] ) ) {
				global $wpdb;

				$url   = $_REQUEST['link'];
				$parts = parse_url( $url );
				parse_str( $parts['query'], $query );
				$attachment_id = $query['wpsp_attachment'];

				$attach_id = intval( sanitize_text_field( $attachment_id ) );

				if ( $attach_id ) {

					$attachment = $wpdb->get_row( "select * from {$wpdb->prefix}wpsp_attachments where id=" . $attach_id );
					$upload_dir = wp_upload_dir();

					$filepath     = $attachment->filepath;
					$filepath     = explode( '/', $filepath );
					$file_name    = $filepath[ count( $filepath ) - 1 ];
					$filepath     = $upload_dir['basedir'] . '/wpsp/' . $file_name;
					$content_type = $attachment->filetype;

					//header('Content-Description: File Transfer');
					//header('Cache-Control: public');
					header( 'Content-Type: ' . $content_type );
					//header("Content-Transfer-Encoding: binary");
					//header('Content-Disposition: attachment; filename='. $attachment->filename);
					header( 'Content-Length: ' . filesize( $filepath ) );

					flush();

					readfile( $filepath );
				}
			}
			die;
		}

		function add_attachment_button()
		{
			if ( is_page( 10 ) ) {
				?>
                <style>
                    header#masthead,
                    footer#colophon,
                    #wpadminbar {
                        display: none !important;
                    }
                </style>
			<?php } ?>
            <script>
                jQuery(function ($) {
                    var i;
                    i = setInterval(function () {
                        //if($(".attachment_link").length > 0)
                        //clearInterval(i);

                        $(".wpsp_ticket_thread_attachment a").each(function () {
                            $a = $("<a />");

                            $a.text("Show");
                            $a.addClass("show_attachment btn btn-info");
                            $a.css("margin-left", 20);

                            $a.attr("data-href", $(this).attr("href"));
                            $a.attr("href", "#");

                            $a.click(function (e) {
                                e.preventDefault();

                                $btn = $(this);

                                if (!$btn.parents('table').next().is("iframe")) {
                                    $btn.text("Please wait...");
                                    $btn.attr("disabled", "disabled");

                                    var url = '<?php echo admin_url( "admin-ajax.php" ); ?>?action=get_file_url&link=' + $btn.attr("data-href");
                                    $iframe = $("<iframe />");

                                    $iframe.css("display", "none");
                                    $iframe.css("width", "100%");
                                    $iframe.css("height", 700);
                                    $iframe.css("margin-top", 10);
                                    $iframe.attr("src", url);

                                    $btn.parents('table').parent().append($iframe);
                                    $iframe.slideDown();

                                    $btn.text("Hide");
                                    $btn.removeAttr("disabled");

                                    /*
                                    $.ajax({
                                        url: '<?php echo admin_url( "admin-ajax.php" ); ?>',
                                        data: {
                                            action: "get_file_url",
                                            link: $btn.attr("data-href")
                                        },
                                        dataType: "JSON",
                                        success: function (response) {
                                            if (response.status == true) {

                                            } else {
                                                $btn.text("Show");
                                                $btn.removeAttr("disabled");
                                            }
                                        },
                                        error: function (e) {
                                            $btn.text("Show");
                                            $btn.removeAttr("disabled");
                                        }
                                    });
                                    */
                                } else if (!$btn.parents('table').next().is(":visible")) {
                                    $iframe = $btn.parents('table').next();
                                    $iframe.slideDown();

                                    $btn.text("Hide");
                                } else {
                                    $iframe = $btn.parents('table').next();
                                    $iframe.slideUp();

                                    $btn.text("Show");
                                }
                            });

                            if ($(this).parent().find("a.show_attachment").length <= 0)
                                $a.insertAfter($(this));

                        });
                    }, 1000);
                });
            </script>
			<?php
		}

		function refresh_support_tickets()
		{
			if ( ! isset( $_GET['id'] ) ) {
				?>
                <script>
                    jQuery(function ($) {
                        setInterval(function () {
                            var is_active = $('.navbar-nav #ticket-list').hasClass('active');

                            if (is_active) {
                                window.location.href = window.location.href;
                            }
                        }, 60 * 1000);
                    });
                </script>
				<?php
			}
		}

		function admin_menu()
		{
			add_menu_page( __( 'Support Tickets', WPSP_LANG ), __( 'Support Tickets', WPSP_LANG ), 'manage_tickets', 'wpts_support_tickets', array(
				$this,
				'wpts_support_tickets'
			) );
		}

		function wpts_support_tickets()
		{
			$url = get_bloginfo( 'url' ) . '/support';
			echo '<iframe width="100%" style="height: 100vh;" src="' . $url . '"></iframe>';
		}

		function add_toolbar_items( $admin_bar )
		{
			$admin_bar->add_menu( array(
				                      'id'    => 'support-tickets',
				                      'title' => 'Support Tickets',
				                      'href'  => add_query_arg( 'page', 'wpts_support_tickets', admin_url( "admin.php" ) ),
				                      'meta'  => array(
					                      'title' => __( 'Support Tickets' ),
				                      ),
			                      ) );
		}

		public function create_pickups()
		{
			global $wpdb;

			$date_now  = date( 'Y-m-d H:i:s' );
			$q         = "SELECT * FROM {$wpdb->prefix}shipments WHERE pickup_date > '{$date_now}';";
			$shipments = $wpdb->get_results( $q );

			foreach ( $shipments as $shipment ) {
				$pickup_date = $shipment->pickup_date;
				$pickup_time = strtotime( $pickup_date );
				$time_now    = time();
				$diff        = $pickup_time - $time_now;
				$minutes     = $diff / 60;

				if ( $minutes <= 5 ) {
					$shipment_url = admin_url( "admin.php?page=wpsp-shipments&id={$shipment->id}" );
					$desc         = "Shipment #{$shipment->id} pickup was scheduled (" . $shipment->pickup_date . ")<br/>{$shipment_url}";
					$subject      = "Shipment #{$shipment->id} Pickup";
					$captcha      = $this->request( [
						                                'action' => 'wpsp_get_captcha_code'
					                                ], false );
					$params       = array(
						"subject"          => $subject,
						"description"      => $desc,
						"category"         => "1",
						"priority"         => "2",
						"user_id"          => $shipment->customer_id,
						"agent_created"    => $shipment->creator_id,
						"create_ticket_as" => 1,
						"guest_name"       => "",
						"guest_email"      => "",
						"action"           => "wpsp_submit_ticket",
						"nonce"            => wp_create_nonce( 'wpsp_nonce' ),
						"captcha_code"     => $captcha
					);

					$params = array(
						"subject"          => $subject,
						"description"      => $desc,
						"category"         => "1",
						"priority"         => "High",
						"user_id"          => $shipment->customer_id,
						"agent_created"    => $shipment->creator_id,
						"create_ticket_as" => 1,
						"guest_name"       => "",
						"guest_email"      => "",
						"action"           => "wpsp_submit_ticket",
						"nonce"            => wp_create_nonce( 'wpsp_nonce' ),
					);

					$this->request( $params );

					$ticket_id = $wpdb->get_var( "SELECT id from {$wpdb->prefix}wpsp_ticket WHERE subject = '{$subject}' ORDER BY id DESC" );
					$ticket_id = intval( $ticket_id );

					include_once WPSP_ABSPATH . 'template/tickets/class-ticket-operations.php';

					$ticket_oprations = new WPSP_Ticket_Operations();
					$ticket_oprations->change_assign_agent( [ $shipment->creator_id ], $ticket_id );
				}
			}
			die;
		}

		public function wpsp_after_ticket_reply( $ticket_id, $reply )
		{
			global $wpdb;

			$reply           = $wpdb->get_row( 'SELECT * FROM `wp_wpsp_ticket_thread` WHERE id = ' . $reply );
			$reply           = (array) $reply;
			$current_user_id = $reply['created_by'];

			$reply['ticket']             = $wpdb->get_row( 'SELECT * FROM wp_wpsp_ticket WHERE id = ' . $ticket_id );
			$reply['thread_user_object'] = get_user_by( 'id', $reply['created_by'] );

			$reply = (object) $reply;

			if ( empty( $reply ) ) {
				return;
			}

			$to             = $reply->ticket->created_by;
			$attachment_ids = $reply->attachment_ids;

			$fax_to = WPSP_Customer::get_fax_number( $to );

			if ( ! empty( $fax_to ) ) {

				if ( ! empty( $reply->body ) ) {
					$user = $reply->thread_user_object;

					if ( $current_user_id == $to ) {
						return;
					}

					$body = strip_tags( html_entity_decode( $reply->body ) );

					$text = "Ticket ID: {$ticket_id}";
					$text .= "<br/>Subject: {$reply->ticket->subject}";
					$text .= "<br/>Timestamp: {$reply->ticket->update_time}";
					$text .= "<br/><br/>{$user->display_name}: {$body}";

					$file_path = apply_filters( 'wpsp_file_dir', "ticket-reply-{$reply->ticket->id}.pdf" );
					$file_url  = apply_filters( 'wpsp_file_url', "ticket-reply-{$reply->ticket->id}.pdf" );

					WPSP_PdfHelper::generate( $text, $file_path, "Ticket #{$reply->ticket->id} Reply" );

					if ( class_exists( 'WPTM_FaxManager' ) ) {
						try {
							$twilio = new WPTM_FaxManager();
							$twilio->sendFax( "+" . $fax_to, $file_url );
						} catch ( Exception $e ) {
							echo $e->getMessage();
						}
					}
				}
			}
		}

		public function handle_fax()
		{
			if ( isset( $_GET['fxrequest'] ) ) {

				file_put_contents( dirname( __FILE__ ) . '/fax.txt', date( 'Y-m-d H:i:s' ) . ' - ' . json_encode( $_REQUEST ) . PHP_EOL, FILE_APPEND );

				if ( isset( $_REQUEST['FaxSid'] ) ) {
					$faxsid      = $_REQUEST['FaxSid'];
					$from        = $_REQUEST['From'];
					$to          = $_REQUEST['To'];
					$from        = preg_replace( '/[^0-9]/', '', $from );
					$to          = preg_replace( '/[^0-9]/', '', $to );
					$is_standard = $to == WPTM_TWILIO_STANDARD_NUMBER;

					$users     = get_users();
					$from_user = false;

					foreach ( $users as $user ) {
						$fax = WPSP_Customer::get_fax_number( $user->ID );

						if ( ! empty( $fax ) && $fax == $from ) {
							$from_user = $user;
							break;
						}
					}
					file_put_contents( dirname( __FILE__ ) . '/fax.txt', "Got user: {$from_user->ID}" . PHP_EOL, FILE_APPEND );

					if ( class_exists( 'WPTM_FaxManager' ) ) {
						$twilio    = new WPTM_FaxManager();
						$attach_id = $twilio->getFax( $faxsid );

						file_put_contents( dirname( __FILE__ ) . '/fax.txt', "Attach id: {$attach_id}" . PHP_EOL, FILE_APPEND );

						if ( $attach_id ) {

							$create_new_ticket = true;
							$last_ticket       = false;

							if ( $from_user && isset( $from_user->ID ) ) {
								$last_ticket = $this->get_last_ticket( $from_user->ID, date( "Y-m-d" ) );

								if ( $last_ticket ) {
									$create_new_ticket = false;
								}
							}

							file_put_contents( dirname( __FILE__ ) . '/fax.txt', "Creating new ticket" . PHP_EOL, FILE_APPEND );

							$from_id = ( ( $from_user && isset( $from_user->ID ) ) ? $from_user->ID : '' );

							if ( $create_new_ticket ) {
								$params = array(
									"subject"            => $faxsid,
									"desc_attachment[0]" => $attach_id,
									"description"        => "Ticket via Fax, From: {$from}; To: {$to}",
									"category"           => $is_standard ? "1" : "2", // General or Special,
									"priority"           => $is_standard ? "Normal" : "High",
									"user_id"            => $from_id,
									"agent_created"      => $from_id,
									"create_ticket_as"   => 1,
									"guest_name"         => "",
									"guest_email"        => "",
									"action"             => "wpsp_submit_ticket",
									"nonce"              => wp_create_nonce( 'wpsp_nonce' ),
								);

								$res = $this->request( $params );

							} else {
								$_POST = array(
									"reply_body"      => "Replied via Fax",
									"desc_attachment" => [ $attach_id ],
									"user_id"         => $from_id,
									"type"            => "user",
									"guest_name"      => "",
									"guest_email"     => "",
									"action"          => "wpsp_ticket_reply",
									"ticket_id"       => $last_ticket->id,
									"notify"          => true,
									"nonce"           => wp_create_nonce( $last_ticket->id )
								);

								ob_start();

								include_once WPSP_ABSPATH . 'template/tickets/class-ticket-operations.php';

								$ticket_oprations = new WPSP_Ticket_Operations();
								$ticket_oprations->reply_ticket();

								$res = ob_get_clean();
								file_put_contents( dirname( __FILE__ ) . '/fax.txt', "Res: {$res}" . PHP_EOL, FILE_APPEND );
							}
						}
					}
				}
				if ( ! isset( $_GET['debug'] ) ) {
					header( "content-type: text/xml" );
					echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
				}
				?>
                <Response>
                    <Receive/>
                </Response>
				<?php
				die();
			}
		}

		public function request( $params, $post = true, $url = false )
		{
			$ch = curl_init();

			if ( ! $url ) {
				$url = admin_url( "admin-ajax.php" );
			}

			if ( $post ) {
				curl_setopt( $ch, CURLOPT_POST, 1 );
				curl_setopt( $ch, CURLOPT_POSTFIELDS, $params );
			} else {
				$url = add_query_arg( $params, $url );
			}

			curl_setopt( $ch, CURLOPT_URL, $url );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_HEADER, false );

			$output = curl_exec( $ch );

			curl_close( $ch );

			return $output;
		}

		public function get_last_ticket( $customer_id, $date )
		{
			global $wpdb;

			$query = "SELECT t.* FROM {$wpdb->prefix}wpsp_ticket_thread AS tt JOIN {$wpdb->prefix}wpsp_ticket AS t ON tt.ticket_id = t.id WHERE t.created_by = {$customer_id} AND DATE(tt.create_time) = '{$date}' AND t.active = 1 order by tt.id DESC LIMIT 1";

			return $wpdb->get_row( $query );
		}
	}
}

if ( defined( 'WPSP_LANG' ) ) {
	new WPTS_TicketSupport();
}