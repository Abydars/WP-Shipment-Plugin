<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSPPipeCron' ) ) :

    final class WPSPPipeCron {

        public function pipe(){

            require_once WPSP_PIPE_PLUGIN_DIR. 'asset/lib/google-api-php/vendor/autoload.php';
            require_once WPSP_PIPE_PLUGIN_DIR. 'includes/cron/class-google-connection-pipe.php';
						require_once WPSP_PIPE_PLUGIN_DIR. 'includes/cron/class-imap-process.php';
            require_once WPSP_PIPE_PLUGIN_DIR. 'includes/cron/class-create-pipe-ticket.php';

            $email_piping      = get_option('wpsp_email_pipe_settings');
            $gmail_connections = get_option('wpsp_ep_gmail_connections');
						$imap_connections  = get_option('wpsp_ep_imap_connections');

						// IMAP Pipe
						if( $email_piping && $imap_connections ){
							foreach ( $imap_connections as $key => $connection ){
								$this->execute_imap_connection( $key, $connection );
              }
            }

						// gmail pipe
						if( $email_piping && $email_piping['gmail_client_secret'] && $gmail_connections ){
							define('GMAIL_APPLICATION_NAME', 'Gmail API PHP');
	            define('GMAIL_CLIENT_SECRET_PATH', $email_piping['gmail_client_secret']);
	            define('GMAIL_SCOPES', implode(' ', array(
	              Google_Service_Gmail::GMAIL_READONLY)
	            ));
							foreach ( $gmail_connections as $connection ){
								$this->execute_gmail_connection( $connection );
              }
            }

        }

				/**
         * IMAP pipe load
         */
			 	function execute_imap_connection( $key, $connection ){

					$imap_connections  = get_option('wpsp_ep_imap_connections');

					$imap_encryption = $connection['encryption']=='none' ? '/novalidate-cert':'/imap/ssl/novalidate-cert';
					$imap_encryption = apply_filters('wpsp_ep_imap_encryption',$imap_encryption);
					$imap_server     = $connection['mail_server'];
					$imap_port       = $connection['server_port'];
					$imap_username   = $connection['username'];
					$imap_password   = $connection['password'];
					$conn   = imap_open('{'.$imap_server.':'.$imap_port.$imap_encryption.'}INBOX', $imap_username, $imap_password);
					if (!$conn) {
						error_log('IMAP Connection for email '.$connection['email'].' failed.');
						error_log(imap_last_error());
						return;
					}

					$last_uid = $connection['last_uid'];

					$history = imap_fetch_overview($conn, ($last_uid+1).":*", FT_UID);
					$uids    = array();
					if ($history) {
					  foreach ($history as $overview) {
					    $uids[] = $overview->uid;
					  }
					}

					$uids = $uids[0] != $last_uid ? $uids : array();

					foreach ($uids as $uid) {
					  $mail = new WPSP_EP_Imap_Mail_Process($conn,$uid);
						$body = $mail->html_body ? $mail->html_body : nl2br($mail->text_body);
						$ticket = new WPSP_Create_Pipe_Ticket($connection['email'], $mail->from_email, $mail->from_name, $mail->subject, $body, $mail->attachments);
						if( $ticket->is_create_ticket && $ticket->ticket_id === 0 ){
								$ticket->create_ticket();
						}
						if( $ticket->is_create_ticket && $ticket->ticket_id !== 0 ){
								$ticket->reply_ticket();
						}
						$imap_connections[$key]['last_uid'] = $uid;
						update_option( 'wpsp_ep_imap_connections', $imap_connections );
					}

					imap_close($conn);

			 	}

        /**
         * Gmail pipe load
         */
        function execute_gmail_connection( $connection ){

            $gmail_connections  = get_option('wpsp_ep_gmail_connections');

            $client = new Google_Client();
            $client->setApplicationName(GMAIL_APPLICATION_NAME);
            $client->setScopes(GMAIL_SCOPES);
            $client->setAuthConfig(GMAIL_CLIENT_SECRET_PATH);
            $client->setAccessType('offline');
            $client->setAccessToken($connection['access_token']);
            if ($client->isAccessTokenExpired()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                $gmail_connections[$connection['email_address']]['access_token'] = $client->getAccessToken();
                update_option('wpsp_ep_gmail_connections',$gmail_connections);
            }

            $service = new Google_Service_Gmail($client);
            $user = $connection['email_address'];
            $optParams = array(
                'startHistoryId'    => $connection['historyId'],
                'labelId'           => 'INBOX',
                'historyTypes'      => 'messageAdded'
            );
            $results = $service->users_history->listUsersHistory($user,$optParams);

            $google_pipe = new WPSP_Google_Connection_Pipe();

            if(isset($results['history'])){

                foreach ( $results['history'] as $history ){

                    $messageobj = $history['messagesAdded'][0]['message'];

                    if( isset($messageobj['labelIds']) && in_array('INBOX',$messageobj['labelIds']) ){

                        $message_id = $messageobj['id'];
                        $message    = $service->users_messages->get($user, $message_id);
                        $payload    = $message->getPayload();
                        $headers    = $payload->getHeaders();

                        $from_email     = $google_pipe->get_from_email($headers);
                        $from_name      = $google_pipe->get_from_name($headers);
                        $subject        = $google_pipe->getHeader($headers, 'Subject');
                        $body           = $google_pipe->get_body($payload);
                        $attachments    = $google_pipe->get_attachments( $user, $service, $payload, $message_id );

                        $ticket = new WPSP_Create_Pipe_Ticket($user, $from_email, $from_name, $subject, $body, $attachments);

                        if( $ticket->is_create_ticket && $ticket->ticket_id === 0 ){
                            $ticket->create_ticket();
                        }

                        if( $ticket->is_create_ticket && $ticket->ticket_id !== 0 ){
                            $ticket->reply_ticket();
                        }
                    }

                    $gmail_connections[$connection['email_address']]['historyId'] = $history['id'];
                    update_option('wpsp_ep_gmail_connections',$gmail_connections);
                }

            }

        }

    }

endif;
