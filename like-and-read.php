<?php
/*
Plugin Name: Like and Read
Plugin URI: https://wordpress.org/plugins/like-and-read/
Description: Like and read full post/page content. Only excerpt is displayed initially. The width of excerpt is set by admin.
Version: 1.0
Author: Sangay Tenzin
Author URI: http://sangaytenzin.com

*/


// Plugin shortcode i.e. [like-and-read]Hidden content [/like-and-read]
add_shortcode ( 'like-and-read', 'like_and_read_function' );

// Function to include CSS and Scripts (including Facebook's js)
function like_read_scripts() {
  global $post;
  
  //wp_register_style( 'like_read_style', plugins_url( 'css/style.css', __FILE__ ) );
  wp_register_script( 'like_read_script', plugins_url( 'js/script.js', __FILE__ ), array( 'jquery' ),'',true );
  
	if( has_shortcode( $post->post_content, 'like-and-read' ) ) {
	//Load the scripts only when the shortcode [like-and-read]...[/like-and-read] is used
  	wp_enqueue_style( 'like_read_style' );
		wp_enqueue_script( 'like_read_script_fb', 'http://connect.facebook.net/en_US/all.js#xfbml=1', array( 'jquery' ),'',FALSE );
		wp_enqueue_script( 'like_read_script' );
		
		//Using this method, only the latest post ID gets passed to script.js. So use post URL for cookie name instead
		//wp_localize_script( 'like_read_script', 'like_and_read_vars', array( 'ID'=> $post->ID ) );
	}	
}
// Register stylesheet and javascript with hook 'wp_enqueue_scripts'
add_action( 'wp_enqueue_scripts', 'like_read_scripts' );

// PLUGIN MAIN FUNCTION
function like_and_read_function ( $atts, $content ) {
	global $post;
	
	//Get only chars of the URL because thats the cookie name
	$url = get_permalink( $post->ID );
	$cookiename = preg_replace("/[^a-zA-Z0-9]+/", "", $url);
	
	//Facebook like button
	$facebook_like = '
	<div class="fb-like" data-href="'.$url.'" data-layout="standard" data-action="like" data-show-faces="false" data-share="false"></div>';
	
	//Check if cookie is set for the URL
	if($_COOKIE[$cookiename]==true) {
		return do_shortcode( $content ).' '.$facebook_like;
						
	} else { 
		
		//Get option values from setting page
	  $myoptions = get_option('like_read_settings');
		$excerpt_chars= $myoptions['excerpt_chars'];
		$excerpt_bg= $myoptions['excerpt_bg'];
		$excerpt_color= $myoptions['excerpt_color'];
	  
	  $read_more_text= $myoptions['read_more_text'];
	  $read_more_color= $myoptions['read_more_color'];
	  
	  //Defaults
	  if($excerpt_chars=="") $excerpt_chars=100;
	  if($excerpt_bg=="") $excerpt_bg="#f6f6f6";
	  if($excerpt_color=="") $excerpt_bg="#cccccc";
	  
	  if($read_more_text=="") $read_more_text="Like us to read more";
	  if($read_more_color=="") $read_more_color='#3B5998';
		
		?>
		<style type="text/css">
		.like-read-container {
			background:<?php echo $myoptions['excerpt_bg']; ?>;
			padding:20px 30px 30px;
		}
		.like-read-container p {
			color:<?php echo $myoptions['excerpt_color']; ?>;
		}
		.like-read-container p span {
			display:block;
			margin-top:30px;
			text-align: center;
			color:<?php echo $myoptions['read_more_color']; ?>;
		}
		</style>
        
    <?php
	  if($excerpt_chars=="") $excerpt_chars=100;
	  if($read_more_text=="") $read_more_text="Like us to read more";
	  
		//Display only excerpt
  	$excerpt=substr(strip_tags($content),0,$excerpt_chars).'...';
  	return '<div class="like-read-container"><p>'.$excerpt.'<span>'.$read_more_text.'</span></p>'.$facebook_like.'</div>';
	}
}

// REGISTER SETTING PAGE
function like_read_options(){
  register_setting('options_group', 'like_read_settings');
}
add_action('admin_init', 'like_read_options'); //Register setting link(s)
 
//Function to add setting page (called Like and Read) under WP Dashboard -> Settings
function like_read_admin_menu() {
	add_options_page('', 'Like and Read', 'manage_options', 'like_read', 'like_and_read_admin' );
}
add_action('admin_menu', 'like_read_admin_menu');

// SETTING PAGE
function like_and_read_admin() { ?>

  <h2>Like and Read: Options</h2>
  <?php //Wordpress core form processor ?>
  <form method="post" action="options.php">
  <?php
  settings_fields('options_group');
  $myoptions = get_option('like_read_settings');
  ?>
  
  <hr>
  <table width="100%">
	  <tr>
		  <td colspan="2">
			  <h3>Excerpt</h3>
		  </td>
	  </tr>
	  <tr>
		  <td width="200"><label for="excerpt_chars">Number of characters</label></td>
		  <td><input type="text" id="excerpt_chars" name="like_read_settings[excerpt_chars]" placeholder="100"
  value="<?php echo $myoptions['excerpt_chars']; ?>" type="text" onkeyup="this.value=this.value.replace(/[^\d]/,'')" /></td>
	  </tr>
	  <tr>
		  <td valign="top"><label for="excerpt_bg">Excerpt background color</label></td>
		  <td><input type="text" id="excerpt_bg" name="like_read_settings[excerpt_bg]" value="<?php echo $myoptions['excerpt_bg']; ?>" data-default-color="#f6f6f6" /></td>
	  </tr>
	  <tr>
		  <td valign="top"><label for="excerpt_color">Excerpt font color</label></td>
		  <td><input type="text" id="excerpt_color" name="like_read_settings[excerpt_color]" value="<?php echo $myoptions['excerpt_color']; ?>" data-default-color="#cccccc" /></td>
	  </tr>
	  
	  <tr>
		  <td colspan="2">
			  <hr>
			  <h3>Read more text</h3>
		  </td>
	  </tr>
	  <tr>
		  <td valign="top"><label for="read_more_text">Read more text: </label></td>
		  <td><input style="width:350px;" type="text" id="read_more_text" name="like_read_settings[read_more_text]" placeholder="Like us to read more" value="<?php echo $myoptions['read_more_text']; ?>" type="text" /></td>
	  </tr>
	  <tr>
		  <td valign="top"><label for="read_more_color">Read more font color </label></td>
		  <td><input type="text" id="read_more_color" name="like_read_settings[read_more_color]" value="<?php echo $myoptions['read_more_color']; ?>" data-default-color="#3B5998" /></td>
	  </tr>
	  <tr>
		  <td colspan="2">
			  <hr>
			  
		  </td>
	  </tr>
  </table>
  
    
	<?php submit_button(); ?>
  </form>
<?php } 

//ADD COLOR PICKER
function lr_enqueue_color_picker( $hook_suffix ) {
  // first check that $hook_suffix is appropriate for your admin page
  wp_enqueue_style( 'wp-color-picker' );
  wp_enqueue_script( 'my-script-handle', plugins_url('js/jscolor/jscolor.js', __FILE__ ), array( 'wp-color-picker' ), false, true )		;
}
add_action( 'admin_enqueue_scripts', 'lr_enqueue_color_picker' );

// ADD shortcode button on Wordpress editor
function lr_register_buttons($buttons) {
 array_push($buttons, 'separator', 'like_read_plugin');
 return $buttons;
}
add_filter('mce_buttons', 'lr_register_buttons');

function lr_register_tinymce_javascript($plugin_array) {
 $plugin_array['like_read_plugin'] = plugins_url('/js/button.js',__FILE__);
 return $plugin_array;
}
add_filter('mce_external_plugins', 'lr_register_tinymce_javascript');
?>