<?php
/*
Plugin Name: InstaLocker Shortcode
Plugin URI: http://instafollow.in
Description: This plugin provides a shortcode that let you hide premium content from users until they follow you on instagram
Version: 1.0
Author: Anikendra Das Choudhury	
Author URI: http://instafollow.in
License: GPLv2
*/

// register the shortcode that accepts one parameter
add_shortcode ( 'premium-content', 'instafollow_content_locker' );

// Hook for adding admin menus
add_action('admin_menu', 'mt_add_pages');

function admin_register_head() {
    $siteurl = get_option('siteurl');
    $url = plugins_url( 'script.js', __FILE__ );
    echo '<script src="'.$url.'" type="text/javascript"></script>';
}
add_action('admin_head', 'admin_register_head');

// action function for above hook
function mt_add_pages() {
    // Add a new submenu under Settings:
    add_options_page(__('InstaLocker Settings','menu-test'), __('InstaLocker Settings','menu-test'), 'manage_options', 'instalockersettings', 'mt_settings_page');
}

// mt_settings_page() displays the page content for the Test settings submenu
function mt_settings_page() {

    //must check that the user has the required capability 
    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }

    // variables for the field and option names 
    $opt_name = 'mt_instagram_username';
    $hidden_field_name = 'mt_submit_hidden';
    $data_field_name = 'mt_instagram_username';

    // Read in existing option value from database
    $opt_val = get_option( $opt_name );

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
        // Read their posted value
        $opt_val = $_POST[ $data_field_name ];
        // Save the posted value in the database
        update_option( $opt_name, $opt_val );
        // Put an settings updated message on the screen
?>
    <div class="updated"><p><strong><?php _e('Instagram Username saved.', 'menu-test' ); ?></strong></p>
        <p><a href="http://instafollow.in/follow/instagram/instafollow.in/?utm_source=instalocker-settings">Follow InstaFollow</a></p>
    </div>
<?php
    }
    // Now display the settings editing screen
    echo '<div class="wrap">';
    // header
    echo "<h2>" . __( 'InstaLocker Plugin Settings', 'menu-test' ) . "</h2>";
    // settings form
?>
<?php $url = admin_url('options-general.php?page=instalockersettings');?>
<form name="form1" method="post" action="<?php echo $url;?>" id="instalockeradminform">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

<p><?php _e("Verify your Instagram Account:", 'menu-test' ); ?> 
    <a class="button-secondary" href="http://instafollow.in/whoami/?referer=<?php echo $url;?>" title="Verify Instagram Account">Authenticate</a>
    <input id="<?php echo $data_field_name; ?>" type="hidden" name="<?php echo $data_field_name; ?>" value="<?php echo $opt_val; ?>" size="20">
</p><hr />

</form>
<?php if(isset($opt_val) && !empty($opt_val)):?>
<p>
    Verified Instagram username : <b><?php echo $opt_val;?></b>
</p>
<?php endif;?>
</div>
<?php
}

//shortcode function
function instafollow_content_locker( $atts, $content ) {
    extract( shortcode_atts( array (
        'method' => ''
    ), $atts ) );
    global $post; 
    	//Check if we have a cookie already set for this post
    	if( isset( $_COOKIE['instafollow-lock_'.$post->ID] ) ) {
	    	//We return the content
    		return do_shortcode( $content );
    	} else {
		$opt_val = get_option('mt_instagram_username');
	    	//We ask the user to like post to see content
	    	return '<div class="instafollow-content-locker">Please follow me on Instagram to access the content <div class="instafollow-button" data-href="' . get_permalink( $post->ID ) . '" data-layout="button_count" data-action="follow" data-show-faces="false" data-share="false">InstaFollow</div></div><script>var to="'.$opt_val.'"</script>';
    	
    	}
}


// Register stylesheet and javascript with hook 'wp_enqueue_scripts', which can be used for front end CSS and JavaScript
add_action( 'wp_enqueue_scripts', 'instafollow_content_locker_scripts' );

//function that enqueue script only if shortcode is used
function instafollow_content_locker_scripts() {
    global $post;
    wp_register_style( 'instafollow_content_locker_style', plugins_url( 'style.css', __FILE__ ) );
    wp_register_script( 'instafollow_content_locker_js', plugins_url( 'script.js', __FILE__ ), array( 'jquery' ),'',true );
    
	if( has_shortcode( $post->post_content, 'premium-content' ) ) {
	    	wp_enqueue_style( 'instafollow_content_locker_style' );
	//	wp_enqueue_script( 'instafollow_content_locker_js-fb', 'http://connect.facebook.net/en_US/all.js#xfbml=1', array( 'jquery' ),'',FALSE );
		wp_enqueue_script( 'instafollow_content_locker_js' );
		wp_localize_script( 'instafollow_content_locker_js', 'instafollow_content_locker', array( 'ID'=> $post->ID ) );
	}	
}
