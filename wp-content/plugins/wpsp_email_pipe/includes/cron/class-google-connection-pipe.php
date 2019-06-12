<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSP_Google_Connection_Pipe' ) ) :

    final class WPSP_Google_Connection_Pipe {

        public function getHeader($headers, $name) {

            foreach ($headers as $header) {

                if ($header['name'] == $name) {
                    return $header['value'];
                }
            }
        }

        public function decodeBody($body) {
            $rawData = $body;
            $sanitizedData = strtr($rawData,'-_', '+/');
            $decodedMessage = base64_decode($sanitizedData);
            if(!$decodedMessage){
                $decodedMessage = FALSE;
            }
            return $decodedMessage;
        }


        public function get_from_email($headers){

            $text = $this->getHeader($headers, 'From');
            preg_match_all("/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i", $text, $matches);

            if(!$matches){
                $text = $this->getHeader($headers, 'Authentication-Results');
                preg_match_all("/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i", $text, $matches);
            }

            return $matches[0][0];
        }

        public function get_from_name($headers){

            $name = $this->getHeader($headers, 'From');
            $name = explode('<', $name);

            if( !trim($name[0]) ){

                $email = $this->get_from_email($headers);

                $name = explode('@', $email);

            }

            return str_replace('"', '', $name[0]);
        }


        function get_body($payload) {

            $FOUND_BODY = '';

            $parts = $payload->getParts();

            foreach ($parts as $part) {

                if ( $part['mimeType'] === 'text/html' && $part['body'] ) {
                    $FOUND_BODY = $this->decodeBody($part['body']->data);
                    break;
                }

                if( !$FOUND_BODY && isset($part['parts']) ){

                    foreach ( $part['parts'] as $p ){

                        if ( $p['mimeType'] === 'text/html' && $p['body'] ) {
                            $FOUND_BODY = $this->decodeBody($p['body']->data);
                            break;
                        }

                        if($FOUND_BODY) {
                            break;
                        }
                    }

                }

                if($FOUND_BODY) {
                    break;
                }
            }

            if(!$FOUND_BODY){

                foreach ($parts as $part) {

                    if ( $part['mimeType'] === 'text/plain' && $part['body']) {
                        $FOUND_BODY = $this->decodeBody($part['body']->data);
                        break;
                    }

                    if( !$FOUND_BODY && isset($part['parts']) ){

                        foreach ( $part['parts'] as $p ){

                            if ( $p['mimeType'] === 'text/plain' && $p['body'] ) {
                                $FOUND_BODY = $this->decodeBody($p['body']->data);
                                break;
                            }

                            if($FOUND_BODY) {
                                break;
                            }
                        }

                    }

                    if($FOUND_BODY) {

                        $FOUND_BODY = nl2br($FOUND_BODY);
                        break;
                    }
                }
            }

            if( !$FOUND_BODY ){

                $body = $payload->getBody();

                $FOUND_BODY = isset($body['data']) ? $this->decodeBody($body['data']) : '';

                if( !( strpos($FOUND_BODY, '<html') > -1 || strpos($FOUND_BODY, '<body') > -1 ) ){
                    $FOUND_BODY = nl2br($FOUND_BODY);
                }

            }

            return $FOUND_BODY;
        }

        public function get_attachments( $user, $service, $payload, $message_id ){

            global $wpdb;

            $attachment_ids = array();

            $parts = $payload->getParts();

            foreach ($parts as $part) {

                if (isset($part['filename']) && $part['filename']) {

                    $file_name      = $part['filename'];
                    $mime_type      = $part['mimeType'];
                    $attachmentId   = $part['body']['attachmentId'];

                    $attachmentObj = $service->users_messages_attachments->get($user, $message_id, $attachmentId);
                    $data = $attachmentObj->getData(); //Get data from attachment object

                    $isError=false;
                    if( !$isError ){
                        $tempExtension=  explode('.', $file_name);
                        $extension=$tempExtension[count($tempExtension)-1];
                        switch ($extension){
                            case 'exe':
                            case 'php':
                            case 'js':
                                $isError=true;
                                break;
                        }
												if ( preg_match('/php/i', $extension) || preg_match('/phtml/i', $extension) ){
										      $isError=true;
										    }
                    }

                    if( !$isError ){

                        $upload_dir = wp_upload_dir();
                        if (!file_exists($upload_dir['basedir'] . '/wpsp/')) {
                            mkdir($upload_dir['basedir'] . '/wpsp/', 0755, true);
                        }

                        $save_file_name = str_replace(' ','_',$file_name);
                        $save_file_name = explode('.', $save_file_name);

                        $extension      = $save_file_name[count($save_file_name)-1];
                        unset( $save_file_name[count($save_file_name)-1] );

                        $save_file_name = implode('-', $save_file_name);

                        $save_file_name = preg_replace('/[^A-Za-z0-9\-]/', '', $save_file_name).'.'.$extension.'.txt';

                        $save_directory = $upload_dir['basedir'] . '/wpsp/'.time().'_'.$save_file_name;

                        $data = strtr($data, array('-' => '+', '_' => '/'));
                        $myfile = fopen($save_directory, "w+");;
                        fwrite($myfile, base64_decode($data));
                        fclose($myfile);

                        $values=array(
                            'filename'  => $file_name,
                            'filepath'  => $save_directory,
                            'filetype'  => $mime_type,
                            'active'    => 1
                        );
                        $wpdb->insert($wpdb->prefix.'wpsp_attachments',$values);
                        $attachment_ids[] = $wpdb->insert_id;

                    }

                }
            }

            return implode(',', $attachment_ids);
        }
    }

endif;
