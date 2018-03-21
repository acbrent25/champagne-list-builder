// wait until the page and jQuery have loaded before running the code below
jQuery(document).ready(function($){
	
	// stop our admin menus from collapsing
	if( $('body[class*=" clb_"]').length || $('body[class*=" post-type-clb_"]').length ) {

		$clb_menu_li = $('#toplevel_page_clb_dashboard_admin_page');
		
		$clb_menu_li
		.removeClass('wp-not-current-submenu')
		.addClass('wp-has-current-submenu')
		.addClass('wp-menu-open');
		
		$('a:first',$clb_menu_li)
		.removeClass('wp-not-current-submenu')
		.addClass('wp-has-submenu')
		.addClass('wp-has-current-submenu')
		.addClass('wp-menu-open');
		
	}
	
});