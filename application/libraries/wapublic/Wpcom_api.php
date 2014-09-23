<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *  Waloli Public Blogging CMS (wapublic)
 *  Wapublic Wordpress.com API library file
 *  @author latuminggi
 */

class Wpcom_api
{
        function __construct(  )
        {
                // Wordpress.com REST API v.1 Service URL
                $this->service_url              = 'https://public-api.wordpress.com/rest/v1';
                
                // Load CodeIgniter object
                $this->CI =& get_instance();
                
                // Load wapublic config
                $this->CI->load->config('wapublic');
                
                $this->app_name                 = $this->CI->config->item('wpcom_app_name');
                $this->client_id                = $this->CI->config->item('wpcom_client_id');
                $this->client_secret            = $this->CI->config->item('wpcom_client_secret');
                $this->auth_code                = $this->CI->config->item('wpcom_auth_code');
                $this->login_url                = $this->CI->config->item('wpcom_login_url');
                $this->redirect_url             = $this->CI->config->item('wpcom_redirect_url');
                $this->request_token_url        = $this->CI->config->item('wpcom_request_token_url');
                $this->authenticate_url         = $this->CI->config->item('wpcom_authenticate_url');
                $this->site                     = $this->CI->config->item('wpcom_site');
                $wpcom_tag_lang                 = $this->CI->config->item('wpcom_tag_lang');
                $wpcom_per_page                 = $this->CI->config->item('wpcom_per_page');
                
                $this->site_info                = $this->get_site(); // wordpress.com site info
                $this->site_id                  = $this->site_info->ID; // wordpress.com site ID
                $this->get_posts_url            = $this->site_info->meta->links->posts; // wordpress.com posts url
                $this->get_comments_url         = $this->site_info->meta->links->comments; // wordpress.com comments url
                
                if ( $wpcom_tag_lang == '' ) { $this->lang = ''; }
                        else { $this->lang = 'tag='. $wpcom_tag_lang; }
                if ( $wpcom_per_page == '' ) { $this->per_page = 'number=10'; }
                        else { $this->per_page = 'number='. $wpcom_per_page; }
        }
        
        public function get_auth_button(  )
        {
                // Load CodeIgniter session library
                $this->CI->load->library('session');
                
                $wpcc_state     = md5( mt_rand() ); $this->CI->session->set_userdata( 'wpcc_state', $wpcc_state );
                
                $params         = array(
                                        'response_type' => 'code',
                                        'client_id'     => $this->client_id,
                                        'state'         => $wpcc_state,
                                        'redirect_uri'  => $this->redirect_url,
                                );
                $url_to         = $this->authenticate_url .'?'. http_build_query( $params );
                
                if ( $this->CI->input->get('error', true) == '' ) { $error_message = ''; }
                        else { $error_message = $this->CI->input->get('error_description', true); }
                    
                $button         = '<h3>Connect to '. $this->app_name .'</h3>';
                $button        .= '<p style="color:red">'. $error_message .'</p>';
                $button        .= '<a href="'. $url_to .'"><img src="//s0.wp.com/i/wpcc-button.png" width="200" /></a>';
                return $button;
        }
        
        public function get_auth( $auth_code )
        {
                $wpcc_state     = $this->CI->session->userdata('wpcc_state');
                $auth_code      = $this->CI->input->get('code', true);
                $auth_state     = $this->CI->input->get('state', true);
        
                if ( $auth_code == true )
                {
                        if ( $wpcc_state == '' )
                                { die( 'Warning! State variable missing after authentication.' ); }
                        if ( $auth_state != $wpcc_state )
                                { die( 'Warning! State mismatch. Authentication attempt may have been compromised.' ); }
                        
                        $curl   = curl_init( $this->request_token_url );
                                  curl_setopt( $curl, CURLOPT_POST, true );
                                  curl_setopt( $curl, CURLOPT_POSTFIELDS, array(
                                                'client_id'     => $this->client_id,
                                                'redirect_uri'  => $this->redirect_url,
                                                'client_secret' => $this->client_secret,
                                                'code'          => $auth_code, // The code from the previous request
                                                'grant_type'    => 'authorization_code',
                                        )
                                  );
                                  curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1);
                        
                        $auth   = curl_exec( $curl );
                        $secret = json_decode( $auth );
                        return $secret;
                }
                
                // Redirect errors or cancelled requests back to login page
                header( 'Location: '. $this->login_url ); die();
        }
        
        // Get site information
        public function get_site(  )
        {
                $get_site_uri   = '/sites/'. $this->site;
                $options        = array( 'http' => array( 'method' => 'GET', 'ignore_errors' => true ) );
                $context        = stream_context_create( $options );
                $response       = file_get_contents( $this->service_url . $get_site_uri, false, $context );
                $result         = json_decode( $response );
                return $result;
        }
        
        // Get recent posts
        public function get_posts( $page = '' )
        {
                $get_posts_uri  = $this->get_posts_url .'?'. $this->per_page .'&'. $this->lang;
                if ( $page != '' ) { $get_posts_uri .= '&page='. $page; }
                $options        = array( 'http' => array( 'method' => 'GET', 'ignore_errors' => true ) );
                $context        = stream_context_create( $options );
                $response       = file_get_contents( $get_posts_uri, false, $context );
                $result         = json_decode( $response );
                return $result;
        }
        
        // Get a post (whether by post's slug or ID)
        public function get_post( $get_by = 'slug', $var )
        {
                $get_post_uri   = $this->get_posts_url .'slug:'. $var;
                if ( $get_by != 'slug' ) { $get_post_uri = $this->get_posts_url . $var; }
                $options        = array( 'http' => array( 'method' => 'GET', 'ignore_errors' => true ) );
                $context        = stream_context_create( $options );
                $response       = file_get_contents( $get_post_uri, false, $context );
                $result         = json_decode( $response );
                return $result;
        }
        
        // Get recent comments
        public function get_comments_recent( $page = '' )
        {
                $get_comments_uri       = $this->get_comments_url .'?'. $this->per_page;
                if ( $page != '' ) { $get_comments_uri .= '&page='. $page; }
                $options                = array( 'http' => array( 'method' => 'GET', 'ignore_errors' => true ) );
                $context                = stream_context_create( $options );
                $response               = file_get_contents( $get_comments_uri, false, $context );
                $result                 = json_decode( $response );
                return $result;
        }
        
        // Get a post comments
        public function get_comments_post( $var, $page = '' )
        {
                $get_comments_uri       = $this->get_posts_url . $var .'/replies/?'. $this->per_page;
                if ( $page != '' ) { $get_comments_uri .= '&page='. $page; }
                $options                = array( 'http' => array( 'method' => 'GET', 'ignore_errors' => true ) );
                $context                = stream_context_create( $options );
                $response               = file_get_contents( $get_comments_uri, false, $context );
                $result                 = json_decode( $response );
                return $result;
        }
        
        // Post a comment on a post service url
        public function post_comment_post_url( $var )
        {
                $post_comment_uri       = $this->get_posts_url . $var .'/replies/new';
                return $post_comment_uri;
        }
        
        // Post a comment on a post
        public function post_comment_post( $var, $content )
        {
                $post_comment_uri       = $this->get_posts_url . $var .'/replies/new';
                $options                = array(
                                                'http' => array(
                                                        'method' => 'POST', 'ignore_errors' => true,
                                                        'header' => array(
                                                                0 => 'authorization: Bearer '. $token,
                                                                1 => 'Content-Type: application/x-www-form-urlencoded'
                                                        ),
                                                        'content' => http_build_query( array( 'content' => $content ) ),
                                                )
                                        );
                $context                = stream_context_create( $options );
                $response               = file_get_contents( $post_comment_uri, false, $context );
                $result                 = json_decode( $response );
                return $result;
        }
}

/* End of file Wpcom_api.php */
/* Location: ./application/libraries/wapublic/Wpcom_api.php */