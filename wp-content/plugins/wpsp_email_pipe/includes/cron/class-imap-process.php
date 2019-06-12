<?php
/**
 * Class for processing mail
 */
final class WPSP_EP_Imap_Mail_Process {

  var $conn;
  var $uid;
  var $header;
  var $from_name;
  var $from_email;
  var $subject;
  var $text_body      = '';
  var $html_body      = '';
  var $attachment_ids = array();
  var $attachments    = '';

  function __construct( $conn, $uid ) {

    $this->conn   = $conn;
    $this->uid    = $uid;
    $this->header = imap_rfc822_parse_headers(imap_fetchheader($this->conn, $this->uid, FT_UID));

    $this->get_from_email();
    $this->get_from_name();
    $this->get_subject();
    $this->process_mail_structure();

    $this->attachments = implode(',', $this->attachment_ids);

  }

  function get_from_email(){
    $this->from_email = strtolower($this->header->from[0]->mailbox . '@' . $this->header->from[0]->host);
  }

  function get_from_name(){
    $this->from_name = isset($this->header->from[0]->personal) ? $this->decodeMimeStr($this->header->from[0]->personal) : $this->from_email;
  }

  function get_subject(){
    $this->subject = isset($this->header->subject) ? $this->decodeMimeStr($this->header->subject) : '';
  }

  function process_mail_structure(){

    $mailStructure = imap_fetchstructure($this->conn, $this->uid, FT_UID);

    if(empty($mailStructure->parts)) {
			$this->initMailPart($mailStructure, 0);
		}
		else {
			foreach($mailStructure->parts as $partNum => $partStructure) {
				$this->initMailPart($partStructure, $partNum + 1);
			}
		}

  }

  protected function initMailPart( $partStructure, $partNum, $markAsSeen = true) {

    global $wpdb, $wpsupportplus, $current_user;

    $options = FT_UID;
		if(!$markAsSeen) {
			$options |= FT_PEEK;
		}
		if($partNum) {
			$data = $this->imap('fetchbody', [$this->uid, $partNum, $options]);
		}
		else {
			$data = $this->imap('body', [$this->uid, $options]);
		}
		if($partStructure->encoding == 1) {
			$data = imap_utf8($data);
		}
		elseif($partStructure->encoding == 2) {
			$data = imap_binary($data);
		}
		elseif($partStructure->encoding == 3) {
			$data = preg_replace('~[^a-zA-Z0-9+=/]+~s', '', $data); // https://github.com/barbushin/php-imap/issues/88
			$data = imap_base64($data);
		}
		elseif($partStructure->encoding == 4) {
			$data = quoted_printable_decode($data);
		}
		$params = [];
		if(!empty($partStructure->parameters)) {
			foreach($partStructure->parameters as $param) {
				$params[strtolower($param->attribute)] = $this->decodeMimeStr($param->value);
			}
		}
		if(!empty($partStructure->dparameters)) {
			foreach($partStructure->dparameters as $param) {
				$paramName = strtolower(preg_match('~^(.*?)\*~', $param->attribute, $matches) ? $matches[1] : $param->attribute);
				if(isset($params[$paramName])) {
					$params[$paramName] .= $param->value;
				}
				else {
					$params[$paramName] = $param->value;
				}
			}
		}
		$isAttachment = $partStructure->ifid || isset($params['filename']) || isset($params['name']);
		// ignore contentId on body when mail isn't multipart (https://github.com/barbushin/php-imap/issues/71)
		if(!$partNum && TYPETEXT === $partStructure->type) {
			$isAttachment = false;
		}
		if($isAttachment) {

      $attachmentId = mt_rand() . mt_rand();
			if(empty($params['filename']) && empty($params['name'])) {
				$fileName = $attachmentId . '.' . strtolower($partStructure->subtype);
			}
			else {
				$fileName = !empty($params['filename']) ? $params['filename'] : $params['name'];
				$fileName = $this->decodeMimeStr($fileName);
				$fileName = $this->decodeRFC2231($fileName);
			}

      $replace = [
        '/\s/' => '_',
        '/[^0-9a-zа-яіїє_\.]/iu' => '',
        '/_+/' => '_',
        '/(^_)|(_$)/' => '',
      ];
      $file_name = preg_replace('~[\\\\/]~', '', time() . '_' . preg_replace(array_keys($replace), $replace, $fileName));


      $isError=false;
      if( !$isError ){
        $tempExtension = explode('.', $file_name);
        $extension     = $tempExtension[count($tempExtension)-1];
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

        $save_directory = $upload_dir['basedir'] . '/wpsp/'.$file_name;

        if(strlen($save_directory) > 255) {
          $ext = pathinfo($save_directory, PATHINFO_EXTENSION);
          $save_directory = substr($save_directory, 0, 255 - 1 - strlen($ext)) . "." . $ext;
        }

        $save_directory .= '.txt';

        $myfile = fopen($save_directory, "w+");
        fwrite($myfile, $data);
        fclose($myfile);

        $mime_type = mime_content_type($save_directory);

        $values=array(
            'filename'  => $fileName,
            'filepath'  => $save_directory,
            'filetype'  => $mime_type,
            'active'    => 1
        );
        $wpdb->insert($wpdb->prefix.'wpsp_attachments',$values);
        $this->attachment_ids[] = $wpdb->insert_id;

      }

		} else {

			if(!empty($params['charset'])) {
				$data = $this->convertStringEncoding($data, $params['charset'], 'utf-8');
			}
			if($partStructure->type == 0 && $data) {
				if(strtolower($partStructure->subtype) == 'plain') {
					$this->text_body .= $data;
				}
				else {
					$this->html_body .= $data;
				}
			}
			elseif($partStructure->type == 2 && $data) {
				$this->text_body .= trim($data);
			}

		}

    if(!empty($partStructure->parts)) {
			foreach($partStructure->parts as $subPartNum => $subPartStructure) {
				if($partStructure->type == 2 && $partStructure->subtype == 'RFC822' && (!isset($partStructure->disposition) || $partStructure->disposition !== "attachment")) {
					$this->initMailPart($subPartStructure, $partNum, $markAsSeen);
				}
				else {
					$this->initMailPart($subPartStructure, $partNum . '.' . ($subPartNum + 1), $markAsSeen);
				}
			}
		}

	}

  protected function decodeMimeStr($string, $toCharset = 'utf-8') {

    $newString = '';
		foreach(imap_mime_header_decode($string) as $element) {
			if(isset($element->text)) {
				$fromCharset = !isset($element->charset) || $element->charset == 'default' ? 'iso-8859-1' : $element->charset;
				$newString .= $this->convertStringEncoding($element->text, $fromCharset, $toCharset);
			}
		}
		return $newString;

  }

  protected function convertStringEncoding($string, $fromEncoding, $toEncoding) {

    if(!$string || $fromEncoding == $toEncoding) {
			return $string;
		}
		$convertedString = function_exists('iconv') ? @iconv($fromEncoding, $toEncoding . '//IGNORE', $string) : null;
		if(!$convertedString && extension_loaded('mbstring')) {
			$convertedString = @mb_convert_encoding($string, $toEncoding, $fromEncoding);
		}
		if(!$convertedString) {
			throw new Exception('Mime string encoding conversion failed');
		}
		return $convertedString;

	}
  
  function isUrlEncoded($string) {
		$hasInvalidChars = preg_match('#[^%a-zA-Z0-9\-_\.\+]#', $string);
		$hasEscapedChars = preg_match('#%[a-zA-Z0-9]{2}#', $string);
		return !$hasInvalidChars && $hasEscapedChars;
	}
  
  protected function decodeRFC2231($string, $charset = 'utf-8') {
		if(preg_match("/^(.*?)'.*?'(.*?)$/", $string, $matches)) {
			$encoding = $matches[1];
			$data = $matches[2];
			if($this->isUrlEncoded($data)) {
				$string = $this->convertStringEncoding(urldecode($data), $encoding, $charset);
			}
		}
		return $string;
	}

  public function imap($methodShortName, $args = [], $prependConnectionAsFirstArg = true) {
		if(!is_array($args)) {
			$args = [$args];
		}
		foreach($args as &$arg) {
			if(is_string($arg)) {
				$arg = imap_utf7_encode($arg);
			}
		}
		if($prependConnectionAsFirstArg) {
			array_unshift($args, $this->conn);
		}
		imap_errors(); // flush errors
		$result = @call_user_func_array("imap_$methodShortName", $args);
		if(!$result) {
			return false;
		}
		return $result;
	}


}
