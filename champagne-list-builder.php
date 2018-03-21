<?php
	
/*
Plugin Name: Champagne List Builder
Plugin URI: http://adamchampagne.com
Description: The ultimate email list building plugin for WordPress. Capture new subscribers. Reward subscribers with a custom download upon opt-in. Build unlimited lists. Import and export subscribers easily with .csv
Version: 1.0
Author: Adam Champagne
Author URI: http://adamchampagne.com
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: champagne-list-builder
*/


/* !0. TABLE OF CONTENTS */

/*
	
	1. HOOKS
		1.1 - registers all our custom shortcodes
		1.2 - register custom admin column headers
		1.3 - register custom admin column data
		1.4 - register ajax actions
		1.5 - load external files to public website
	
	2. SHORTCODES
		2.1 - clb_register_shortcodes()
		2.2 - clb_form_shortcode()
		
	3. FILTERS
		3.1 - clb_subscriber_column_headers()
		3.2 - clb_subscriber_column_data()
		3.3 - clb_list_column_headers()
		3.4 - clb_list_column_data()
		
	4. EXTERNAL SCRIPTS
		4.1 - Include ACF
		4.2 - clb_public_scripts()
		
	5. ACTIONS
		5.1 - clb_save_subscription()
		5.2 - clb_save_subscriber()
		5.3 - clb_add_subscription()
		
	6. HELPERS
		6.1 - clb_has_subscriptions()
		6.2 - clb_get_subscriber_id()
		6.3 - clb_get_subscritions()
		6.4 - clb_return_json()
		6.5 - clb_get_acf_key()
		6.6 - clb_get_subscriber_data()
		
	7. CUSTOM POST TYPES
		7.1 - subscribers
		7.2 - lists
	
	8. ADMIN PAGES
	
	9. SETTINGS

*/




/* !1. HOOKS */

// 1.1
// hint: registers all our custom shortcodes on init
add_action('init', 'clb_register_shortcodes');

// 1.2
// hint: register custom admin column headers
add_filter('manage_edit-clb_subscriber_columns','clb_subscriber_column_headers');
add_filter('manage_edit-clb_list_columns','clb_list_column_headers');

// 1.3
// hint: register custom admin column data
add_filter('manage_clb_subscriber_posts_custom_column','clb_subscriber_column_data',1,2);
add_filter('manage_clb_list_posts_custom_column','clb_list_column_data',1,2);

// 1.4
// hint: register ajax actions
add_action('wp_ajax_nopriv_clb_save_subscription', 'clb_save_subscription'); // regular website visitor
add_action('wp_ajax_clb_save_subscription', 'clb_save_subscription'); // admin user

// 1.5
// load external files to public website
add_action('wp_enqueue_scripts', 'clb_public_scripts');

// 1.6
// Advanced Custom Fields Settings
add_filter('acf/settings/path', 'clb_acf_settings_path');
add_filter('acf/settings/dir', 'clb_acf_settings_dir');
add_filter('acf/settings/show_admin', 'clb_acf_show_admin');
if( !defined('ACF_LITE') ) define('ACF_LITE',true); // turn off ACF plugin menu

/* !2. SHORTCODES */

// 2.1
// hint: registers all our custom shortcodes
function clb_register_shortcodes() {
	
	add_shortcode('clb_form', 'clb_form_shortcode');
	
}

// 2.2
// hint: returns a html string for a email capture form
function clb_form_shortcode( $args, $content="") {
	
	// get the list id
	$list_id = 0;
	if( isset($args['id']) ) $list_id = (int)$args['id'];
	
	// setup our output variable - the form html 
	$output = '
	
		<div class="clb">
		
			<form id="clb_form" name="clb_form" class="clb-form" method="post"
			action="/wp-admin/admin-ajax.php?action=clb_save_subscription" method="post">
			
				<input type="hidden" name="clb_list" value="'. $list_id .'">
			
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

// 3.1
function clb_subscriber_column_headers( $columns ) {
	
	// creating custom column header data
	$columns = array(
		'cb'=>'<input type="checkbox" />',
		'title'=>__('Subscriber Name'),
		'email'=>__('Email Address'),	
	);
	
	// returning new columns
	return $columns;
	
}

// 3.2
function clb_subscriber_column_data( $column, $post_id ) {
	
	// setup our return text
	$output = '';
	
	switch( $column ) {
		
		case 'name':
			// get the custom name data
			$fname = get_field('clb_fname', $post_id );
			$lname = get_field('clb_lname', $post_id );
			$output .= $fname .' '. $lname;
			break;
		case 'email':
			// get the custom email data
			$email = get_field('clb_email', $post_id );
			$output .= $email;
			break;
		
	}
	
	// echo the output
	echo $output;
	
}

// 3.3
function clb_list_column_headers( $columns ) {
	
	// creating custom column header data
	$columns = array(
		'cb'=>'<input type="checkbox" />',
		'title'=>__('List Name'),
		'shortcode'=>__('Shortcode'),	
	);
	
	// returning new columns
	return $columns;
	
}

// 3.2
function clb_list_column_data( $column, $post_id ) {
	
	// setup our return text
	$output = '';
	
	switch( $column ) {
		
		case 'shortcode':
			$output .= '[clb_form id="'. $post_id .'"]';
			break;
		
	}
	
	// echo the output
	echo $output;
	
}




/* !4. EXTERNAL SCRIPTS */

// 4.1
// Include ACF
include_once( plugin_dir_path( __FILE__ ) .'lib/advanced-custom-fields/acf.php' );

// 4.2
// hint: loads external files into PUBLIC website
function clb_public_scripts() {
	
	// register scripts with WordPress's internal library
	wp_register_script('champagne-list-builder-js-public', plugins_url('/js/public/champagne-list-builder.js',__FILE__), array('jquery'),'',true);
	
	// add to que of scripts that get loaded into every page
	wp_enqueue_script('champagne-list-builder-js-public');
	
}




/* !5. ACTIONS */

// 5.1
// hint: saves subscription data to an existing or new subscriber
function clb_save_subscription() {
	
	// setup default result data
	$result = array(
		'status' => 0,
    'message' => 'Subscription was not saved. ',
    'error'=>'',
		'errors'=>array()
	);
	
	try {
		
		// get list_id
		$list_id = (int)$_POST['clb_list'];
	
		// prepare subscriber data
		$subscriber_data = array(
			'fname'=> esc_attr( $_POST['clb_fname'] ),
			'lname'=> esc_attr( $_POST['clb_lname'] ),
			'email'=> esc_attr( $_POST['clb_email'] ),
    );
    
    // setup our errors array
		$errors = array();
		
		// form validation
		if( !strlen( $subscriber_data['fname'] ) ) $errors['fname'] = 'First name is required.';
		if( !strlen( $subscriber_data['email'] ) ) $errors['email'] = 'Email address is required.';
		if( strlen( $subscriber_data['email'] ) && !is_email( $subscriber_data['email'] ) ) $errors['email'] = 'Email address must be valid.';

			// IF there are errors
      if( count($errors) ):
		
        // append errors to result structure for later use
        $result['error'] = 'Some fields are still required. ';
        $result['errors'] = $errors;
      
      else: 
      // IF there are no errors, proceed...
      
        // attempt to create/save subscriber
        $subscriber_id = clb_save_subscriber( $subscriber_data );
        
        // IF subscriber was saved successfully $subscriber_id will be greater than 0
        if( $subscriber_id ):
        
          // IF subscriber already has this subscription
          if( clb_subscriber_has_subscription( $subscriber_id, $list_id ) ):
          
            // get list object
            $list = get_post( $list_id );
            
            // return detailed error
            $result['error'] = esc_attr( $subscriber_data['email'] .' is already subscribed to '. $list->post_title .'.');
            
          else: 
          
            // save new subscription
            $subscription_saved = clb_add_subscription( $subscriber_id, $list_id );
        
            // IF subscription was saved successfully
            if( $subscription_saved ):
            
              // subscription saved!
              $result['status']=1;
              $result['message']='Subscription saved';
              
            else: 
            
              // return detailed error
              $result['error'] = 'Unable to save subscription.';
            
            
            endif;
          
          endif;
        
        endif;
      
      endif;
      
    } catch ( Exception $e ) {
      
    }
    
    // return result as json
    clb_return_json($result);
    
  }

// 5.2
// hint: creates a new subscriber or updates and existing one
function clb_save_subscriber( $subscriber_data ) {
	
	// setup default subscriber id
	// 0 means the subscriber was not saved
	$subscriber_id = 0;
	
	try {
		
		$subscriber_id = clb_get_subscriber_id( $subscriber_data['email'] );
		
		// IF the subscriber does not already exists...
		if( !$subscriber_id ):
		
			// add new subscriber to database	
			$subscriber_id = wp_insert_post( 
				array(
					'post_type'=>'clb_subscriber',
					'post_title'=>$subscriber_data['fname'] .' '. $subscriber_data['lname'],
					'post_status'=>'publish',
				), 
				true
			);
		
		endif;
		
		// add/update custom meta data
		update_field(clb_get_acf_key('clb_fname'), $subscriber_data['fname'], $subscriber_id);
		update_field(clb_get_acf_key('clb_lname'), $subscriber_data['lname'], $subscriber_id);
		update_field(clb_get_acf_key('clb_email'), $subscriber_data['email'], $subscriber_id);
		
	} catch( Exception $e ) {
		
		// a php error occurred
		
	}
	
	// return subscriber_id
	return $subscriber_id;
	
}

// 5.3
// hint: adds list to subscribers subscriptions
function clb_add_subscription( $subscriber_id, $list_id ) {
	
	// setup default return value
	$subscription_saved = false;
	
	// IF the subscriber does NOT have the current list subscription
	if( !clb_subscriber_has_subscription( $subscriber_id, $list_id ) ):
	
		// get subscriptions and append new $list_id
		$subscriptions = clb_get_subscriptions( $subscriber_id );
		$subscriptions[]=$list_id;
		
		// update clb_subscriptions
		update_field( clb_get_acf_key('clb_subscriptions'), $subscriptions, $subscriber_id );
		
		// subscriptions updated!
		$subscription_saved = true;
	
	endif;
	
	// return result
	return $subscription_saved;
	
}





/* !6. HELPERS */

// 6.1
// hint: returns true or false
function clb_subscriber_has_subscription( $subscriber_id, $list_id ) {
	
	// setup default return value
	$has_subscription = false;
	
	// get subscriber
	$subscriber = get_post($subscriber_id);
	
	// get subscriptions
	$subscriptions = clb_get_subscriptions( $subscriber_id );
	
	// check subscriptions for $list_id
	if( in_array($list_id, $subscriptions) ):
	
		// found the $list_id in $subscriptions
		// this subscriber is already subscribed to this list
		$has_subscription = true;
	
	else:
	
		// did not find $list_id in $subscriptions
		// this subscriber is not yet subscribed to this list
	
	endif;
	
	return $has_subscription;
	
}

// 6.2
// hint: retrieves a subscriber_id from an email address
function clb_get_subscriber_id( $email ) {
	
	$subscriber_id = 0;
	
	try {
	
		// check if subscriber already exists
		$subscriber_query = new WP_Query( 
			array(
				'post_type'		=>	'clb_subscriber',
				'posts_per_page' => 1,
				'meta_key' => 'clb_email',
				'meta_query' => array(
				    array(
				        'key' => 'clb_email',
				        'value' => $email,  // or whatever it is you're using here
				        'compare' => '=',
				    ),
				),
			)
		);
		
		// IF the subscriber exists...
		if( $subscriber_query->have_posts() ):
		
			// get the subscriber_id
			$subscriber_query->the_post();
			$subscriber_id = get_the_ID();
			
		endif;
	
	} catch( Exception $e ) {
		
		// a php error occurred
		
	}
		
	// reset the Wordpress post object
	wp_reset_query();
	
	return (int)$subscriber_id;
	
}

// 6.3
// hint: returns an array of list_id's
function clb_get_subscriptions( $subscriber_id ) {
	
	$subscriptions = array();
	
	// get subscriptions (returns array of list objects)
	$lists = get_field( clb_get_acf_key('clb_subscriptions'), $subscriber_id );
	
	// IF $lists returns something
	if( $lists ):
	
		// IF $lists is an array and there is one or more items
		if( is_array($lists) && count($lists) ):
			// build subscriptions: array of list id's
			foreach( $lists as &$list):
				$subscriptions[]= (int)$list->ID;
			endforeach;
		elseif( is_numeric($lists) ):
			// single result returned
			$subscriptions[]= $lists;
		endif;
	
	endif;
	
	return (array)$subscriptions;
	
}

// 6.4
function clb_return_json( $php_array ) {
	
	// encode result as json string
	$json_result = json_encode( $php_array );
	
	// return result
	die( $json_result );
	
	// stop all other processing 
	exit;
	
}


//6.5
// hint: gets the unique act field key from the field name
function clb_get_acf_key( $field_name ) {
	
	$field_key = $field_name;
	
	switch( $field_name ) {
		
		case 'clb_fname':
			$field_key = 'field_5aa595882bd3e';
			break;
		case 'clb_lname':
			$field_key = 'field_5aa595a22bd3f';
			break;
		case 'clb_email':
			$field_key = 'field_5aa595b12bd40';
			break;
		case 'clb_subscriptions':
			$field_key = 'field_5aa595cf2bd41';
			break;
		
	}
	
	return $field_key;
	
}


// 6.6
// hint: returns an array of subscriber data including subscriptions
function clb_get_subscriber_data( $subscriber_id ) {
	
	// setup subscriber_data
	$subscriber_data = array();
	
	// get subscriber object
	$subscriber = get_post( $subscriber_id );
	
	// IF subscriber object is valid
	if( isset($subscriber->post_type) && $subscriber->post_type == 'clb_subscriber' ):
	
		$fname = get_field( clb_get_acf_key('clb_fname'), $subscriber_id);
		$lname = get_field( clb_get_acf_key('clb_lname'), $subscriber_id);
	
		// build subscriber_data for return
		$subscriber_data = array(
			'name'=> $fname .' '. $lname,
			'fname'=>$fname,
			'lname'=>$lname,
			'email'=>get_field( clb_get_acf_key('clb_email'), $subscriber_id),
			'subscriptions'=>clb_get_subscriptions( $subscriber_id )
		);
		
	
	endif;
	
	// return subscriber_data
	return $subscriber_data;
	
}



/* !7. CUSTOM POST TYPES */
// 7.1
// subscribers
include_once( plugin_dir_path( __FILE__ ) . 'cpt/clb_subscriber.php');

//7.2
// lists
include_once( plugin_dir_path( __FILE__ ) . 'cpt/clb_list.php');



/* !8. ADMIN PAGES */




/* !9. SETTINGS */

