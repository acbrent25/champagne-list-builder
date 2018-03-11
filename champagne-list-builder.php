<?php
/*
Plugin Name: Champagne List Builder
Plugin URI: http://adamchampagne.com
Description: The ultimate email list building plugin
Version: 1.0
Author: Adam Champagne
Author URI: http://adamchampagne.com
License: GPL-2.0+
 License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/


/* !0. TABLE OF CONTENTS */

/*
	
	1. HOOKS
		1.1 - registers all our custom shortcodes
	
	2. SHORTCODES
		2.1 - clb_register_shortcodes()
		2.2 - clb_form_shortcode()
		
	3. FILTERS
		
	4. EXTERNAL SCRIPTS
		
	5. ACTIONS
		
	6. HELPERS
		
	7. CUSTOM POST TYPES
	
	8. ADMIN PAGES
	
	9. SETTINGS

*/


/* !1. HOOKS */

// 1.1
// hint: registers all our custom shortcodes on init
add_action('init', 'clb_register_shortcodes');

// 1.2
// hint: register custom admin column headers
add_filter('manage_edit-clb_subscriber_columns', 'clb_subscriber_column_headers');

// 1.3
// hint: registers custom amdmin column data
add_filter('manage_clb_subscriber_posts_custom_column', 'clb_subscriber_column_data',1,2);



/* !2.1 SHORTCODES */
// hint: registers all our custom shortcodes
function clb_register_shortcodes() {

    add_shortcode( 'clb_form', 'clb_form_shortcode' );
}

function clb_form_shortcode( $args, $content="") {

  // setup our output variable - the form html
  $output = '
	
  <div class="clb">
  
    <form id="clb_form" name="clb_form" class="clb-form" method="post">
    
      <p class="clb-input-container">
      
        <label>Your Name</label><br />
        <input type="text" name="clb_fname" placeholder="First Name" />
        <input type="text" name="clb_lname" placeholder="Last Name" />
      
      </p>
      
      <p class="clb-input-container">
      
        <label>Your Email</label><br />
        <input type="email" name="clb_email" placeholder="ex. you@email.com" />
      
      </p>';
      
      // including content in our form html if content is passed into the function
      if( strlen($content) ):
      
        $output .= '<div class="clb-content">'. wpautop($content) .'</div>';
      
      endif;
      
      // completing our form html
      $output .= '<p class="clb-input-container">
      
        <input type="submit" name="clb_submit" value="Sign Me Up!" />
      
      </p>
    
    </form>
  
  </div>

';
	
	// return our results/html
	return $output;
	
}


/* !3. FILTERS */

//3.1
function clb_subscriber_column_headers( $columns ) {
  // create cusomt column header data
  $columns = array (
    'cb'=>'<input type="checkbox" />',
    'title'=>__('Subscriber Name'),
    'email'=>__('Email Address'),
  );

  // returning new columns
  return $columns;
  
}

// 3.2
function clb_subscriber_column_data( $column, $post_id) {

  // setup our return text
  $output = '';

    switch( $column ){
      case 'title':
        //get the custom name data
        $fname = get_field('clb_fname', $post_id );
        $lname = get_field('clb_lname', $post_id );
        $output .= $fname .' '. $lname;
        break;

      case 'email':
        //get the custom email data
        $email = get_field('clb_email_adress', $post_id );
        $output .= $email;
        break;
    }

    // echo the output
    echo $output;
}

// 3.2.2
// hint: registers special custom admin title columns
function clb_register_custom_admin_titles() {
  add_filter(
    'the_title',
    'clb_custom_admin_titles',
    99,
    2
  );
}



/* !4. EXTERNAL SCRIPTS */




/* !5. ACTIONS */




/* !6. HELPERS */




/* !7. CUSTOM POST TYPES */




/* !8. ADMIN PAGES */




/* !9. SETTINGS */
