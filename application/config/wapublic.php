<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *  Waloli Public Blogging CMS (wapublic)
 *  Wapublic configuration file
 *  @author latuminggi
 */

/**
 *  Wordpress.com API
 */

$config['wpcom_app_name']           = 'My Wordpress.com Blog';
// wordpress.com app name (string | CHANGE me!)
$config['wpcom_client_id']          = '123456';
// wordpress.com app client id (integer | CHANGE me!)
$config['wpcom_client_secret']      = 'abcdefghijklmnopqrstuvwxyz1234567890';
// wordpress.com app client secret (string | CHANGE me!)
$config['wpcom_auth_code']          = 'abcdef123456';
// wordpress.com auth code after authentication (string | CHANGE me!)
$config['wpcom_login_url']          = 'http://localhost/wapublic/wordpresscom/auth';
// wordpress.com app login url (url | CHANGE me!)
$config['wpcom_redirect_url']       = 'http://localhost/wapublic/wordpresscom/auth';
// wordpress.com app redirect url (url | CHANGE me!)
$config['wpcom_request_token_url']  = 'https://public-api.wordpress.com/oauth2/token';
// wordpress.com request token url (url | do NOT change!)
$config['wpcom_authorize_url']      = 'https://public-api.wordpress.com/oauth2/authorize';
// wordpress.com authorize url (url | do NOT change!)
$config['wpcom_authenticate_url']   = 'https://public-api.wordpress.com/oauth2/authenticate';
// wordpress.com authenticate url (url | do NOT change!)

$config['wpcom_site']               = 'abcdef.wordpress.com';
// subdomain of wordpress.com site (string | required)
$config['wpcom_tag_lang']           = '';
// default language based on tag slug, for multilingual purpose (string | optional)
$config['wpcom_per_page']           = '';
// wordpress.com posts/comments per page to show, default 10 per page (integer | optional)

/* End of file wapublic.php */
/* Location: ./application/config/wapublic.php */