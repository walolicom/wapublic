<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *  Waloli Public Blogging CMS (wapublic)
 *  Wapublic Wordpress.com API contoller example file
 *  @author walolicom
 */

class Wordpresscom extends CI_Controller
{
    function __construct(  )
    {
        parent::__construct();
        
        // Load Wapublic Wordpress.com API library
        $this->load->library( 'wapublic/wpcom_api' );
    }

	// View site information
	public function index(  )
	{
		$site = $this->wpcom_api->get_site();
		echo '<pre>'; print_r( $site );
	}

	// View recent posts
	public function blog( $page = '' )
	{
		$posts = $this->wpcom_api->get_posts( $page );
		echo '<pre>'; print_r( $posts );
	}

	// View a post
	public function post( $var )
	{
		// Load CodeIgniter url helper
		$this->load->helper('url');

		if ( is_numeric( $var ) ) { $get_by = 'ID'; } else { $get_by = 'slug'; }
		$post = $this->wpcom_api->get_post( $get_by, $var );
		if ( is_numeric( $var ) ) { $post_id = $var; } else { $post_id = $post->ID; }

		// echo '<pre>'; print_r( $post );
		echo 'Post ID: '. $post->ID .'<br>';
		echo 'Post slug: '. $post->slug .'<br>';
		echo 'Post title: '. $post->title .'<br>';
		echo 'Post content: ' .$post->content .'<br>';
		echo 'Comments open: '. $post->comments_open .'<br>';
		echo 'Comments count: '. $post->comment_count .'<br>';

		if ( $post->comments_open == 1 )
		{
			echo '<br><a href="'. site_url( 'wordpresscom/input_comment/'. $var ) .'">';
			echo 'Post a comment</a><br>';
		}

		$post_comments = $this->wpcom_api->get_comments_post( $post_id );
		foreach ( $post_comments->comments as $comment )
		{
			echo '<br><a href="'. $comment->author->URL .'">'. $comment->author->name .'</a><br>';
			echo $comment->content;
		}
	}

	// View recent comments
	public function recent_comments( $page = '' )
	{
		$recent_comments = $this->wpcom_api->get_comments_recent( $page );
		echo '<pre>'; print_r( $recent_comments );
	}

	// View a post comments
	public function post_comments( $var, $page = '' )
	{
		$post_comments = $this->wpcom_api->get_comments_post( $var, $page );
		echo '<pre>'; print_r( $post_comments );
	}

	// Get authentication code with Wordpress.com API
	public function auth(  )
	{
		$auth_code = $this->input->get('code', true);

		if ( $auth_code == true )
		{
			$auth = $this->wpcom_api->get_auth( $auth_code );
			echo 'Code: '. $auth_code; echo '<pre>'; print_r( $auth );
		}
		else
		{
			$auth_button = $this->wpcom_api->get_auth_button();
			echo $auth_button;
		}	
	}

	// Input comment on a post
	public function input_comment( $var )
	{
		// Load CodeIgniter form validation library
		$this->load->library('form_validation');

		// Load CodeIgniter form helper
		$this->load->helper('form');

		if ( is_numeric( $var ) ) { $get_by = 'ID'; } else { $get_by = 'slug'; }
		$post = $this->wpcom_api->get_post( $get_by, $var );
		if ( is_numeric( $var ) ) { $post_id = $var; } else { $post_id = $post->ID; }

		echo 'Post title: '. $post->title .'<br>';

		if ( $post->comments_open == 1 )
		{
			echo form_open( $this->wpcom_api->post_comment_post_url( $post_id ) ) .'<br>';
			echo form_input('email', '', 'placeholder="Email"') .'<br>';
			echo form_input('author', '', 'placeholder="Name"') .'<br>';
			echo form_input('url', '', 'placeholder="Website"') .'<br>';
			echo form_textarea('content', '', 'placeholder="Comment"') .'<br>';
			echo form_submit('submit', 'Post comment') .'<br>';
			echo form_close();
		}

		// $this->wpcom_api->post_comment_post( $var, $content );
	}
}

/* End of file wordpresscom.php */
/* Location: ./application/controllers/wapublic/wordpresscom.php */
