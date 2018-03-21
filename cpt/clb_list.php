<?php

function clb_register_clb_list() {


	/**
	 * Post Type: Lists.
	 */

	$labels = array(
		"name" => __( "Lists", "udemy" ),
		"singular_name" => __( "List", "udemy" ),
	);

	$args = array(
		"label" => __( "Lists", "udemy" ),
		"labels" => $labels,
		"description" => "",
		"public" => false,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => false,
		"rest_base" => "",
		"has_archive" => false,
		"show_in_menu" => true,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"rewrite" => array( "slug" => "clb_list", "with_front" => false ),
		"query_var" => true,
		"supports" => array( "title" ),
	);

	register_post_type( "clb_list", $args );
}

add_action( 'init', 'clb_register_clb_list' );
