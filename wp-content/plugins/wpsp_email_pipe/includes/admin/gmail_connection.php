<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once WPSP_PIPE_PLUGIN_DIR. 'asset/lib/google-api-php/vendor/autoload.php';

$email_piping       = get_option('wpsp_email_pipe_settings');
$gmail_connections  = get_option('wpsp_ep_gmail_connections');

if( !$email_piping || !$email_piping['gmail_client_secret']){
    exit;
}

define('GMAIL_APPLICATION_NAME', 'Gmail API PHP');
define('GMAIL_CLIENT_SECRET_PATH', $email_piping['gmail_client_secret']);

define('GMAIL_SCOPES', implode(' ', array(
  Google_Service_Gmail::GMAIL_READONLY)
));

$redirect_uri = admin_url( 'admin.php' ).'?page=wp-support-plus&setting=addons&section=email_piping&action=add_gmail_connection';
$client = new Google_Client();
$client->setApplicationName(GMAIL_APPLICATION_NAME);
$client->setScopes(GMAIL_SCOPES);
$client->setAuthConfig(GMAIL_CLIENT_SECRET_PATH);
$client->setRedirectUri($redirect_uri);
$client->setAccessType('offline');

if (isset($_GET['code'])) {
    
    $accessToken = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($accessToken);
    
}  else {

    $authUrl = $client->createAuthUrl();
    header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));

}

$service = new Google_Service_Gmail($client);

$results = $service->users->getProfile('me');

if(isset($results['emailAddress'])){
    
    $gmail_connections[$results['emailAddress']] = array(
        'email_address' => $results['emailAddress'],
        'access_token'  => $accessToken,
        'historyId'     => $results['historyId']
    );
    update_option('wpsp_ep_gmail_connections',$gmail_connections);
    
    $email_piping['email_categories'][$results['emailAddress']] = 1;
    update_option('wpsp_email_pipe_settings',$email_piping);

}
