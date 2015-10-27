<?php
/**
 * Custom template tags for this theme.
 * 
 * Some functions modified from WordPress Twenty Fourteen theme.
 */

/*** GLOBAL VARIABLES ***/

/* max number featured pages user can select */
	/* 
	 * NOTE: ensure that REDANDBLACK_MAX_NUM_FEATURED_PAGES value matches number of
	 * featured pages user can select in the Customizer
	 * (set in customizer.php)
	 */
define( 'REDANDBLACK_MAX_NUM_FEATURED_PAGES', 2 );

/* store social site names in array that can be used as needed */
$redandblack_social_sites = array(
	'facebook',
	'google-plus',
	'twitter',
	'youtube',
	'linkedin',
	'flickr',
	'pinterest',
	'tumblr',
	'dribbble',
	'instagram',
	'rss',
);

/* store response message for contact form submit */
$redandblack_response = "";


/*** FUNCTIONS ***/

if ( ! function_exists( 'redandblack_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function redandblack_posted_on() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

	$posted_on = sprintf(
		esc_html_x( 'Posted on %s', 'post date', 'redandblack' ), $time_string
	);

	$byline = sprintf(
		esc_html_x( 'by %s', 'post author', 'redandblack' ),
		'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
	);

	echo '<span class="posted-on">' . $posted_on . '</span><span class="byline"> ' . $byline . '</span>'; // WPCS: XSS OK.

}
endif;


if ( ! function_exists( 'redandblack_entry_footer' ) ) :
/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function redandblack_entry_footer() {
	// Hide category and tag text for pages.
	if ( 'post' === get_post_type() ) {
		/* translators: used between list items, there is a space after the comma */
		$categories_list = get_the_category_list( esc_html__( ', ', 'redandblack' ) );
		if ( $categories_list && redandblack_categorized_blog() ) {
			printf( '<div class="cat-links">' . esc_html__( 'Tagged as %1$s', 'redandblack' ) . '</div>', $categories_list ); // WPCS: XSS OK.
		}

		/* translators: used between list items, there is a space after the comma */
		$tags_list = get_the_tag_list( '', esc_html__( ', ', 'redandblack' ) );
		if ( $tags_list ) {
			printf( '<div class="tags-links">' . esc_html__( 'Tagged %1$s', 'redandblack' ) . '</div>', $tags_list ); // WPCS: XSS OK.
		}
	}

	if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
		echo '<div class="comments-link">';
		comments_popup_link( esc_html__( 'Leave a comment', 'redandblack' ), esc_html__( '1 Comment', 'redandblack' ), esc_html__( '% Comments', 'redandblack' ) );
		echo '</div>';
	}

	edit_post_link( esc_html__( 'Edit', 'redandblack' ), '<div class="edit-link">', '</div>' );
}
endif;


/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function redandblack_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'redandblack_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'	 => 'ids',
			'hide_empty' => 1,

			// We only need to know if there is more than one category.
			'number'	 => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'redandblack_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so redandblack_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so redandblack_categorized_blog should return false.
		return false;
	}
}


/**
 * Flush out the transients used in redandblack_categorized_blog.
 */
function redandblack_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'redandblack_categories' );
}
add_action( 'edit_category', 'redandblack_category_transient_flusher' );
add_action( 'save_post',	 'redandblack_category_transient_flusher' );


if ( ! function_exists( 'redandblack_fix_post_menu_item' ) ) :
/**
 * Removes slug portion of URL for posts page (page with slug of "blog").
 *
 * @param $menu_string : string containing menu with only <nav> and <a> tags.
 * 
 * @return mixed|string : new menu string with "home" posts page link for blog rather than "page" link.
 */
function redandblack_fix_posts_link($menu_string) {
	$menu_string = str_replace('/blog/', '/', $menu_string );
	return $menu_string;
}
endif;


if ( ! function_exists( 'redandblack_add_item_classes_to_menu' ) ) :
/**
 * Place 'menu_item' class into menu 'a' tags.
 *
 * @param $menu_string : string containing menu with only <nav> and <a> tags.
 * 
 * @return mixed|string : new menu string with appropriate item classes added.
 */
function redandblack_add_item_classes_to_menu($menu_string) {
	if ( empty( $menu_string ) ) {
		return false;
	}
	
	// retrieve posts page title
	$posts_page_id = get_option('page_for_posts');
	$posts_page_title = get_post($posts_page_id)->post_title;

	// looking for title in menu string; so, if title not present, use default of 'Nothing Found'
	$current_page_title = is_home() || is_single() ? $posts_page_title : get_the_title();
	if (empty( $current_page_title ) ) {
		$current_page_title = 'Nothing Found';
	}

	// set up text replacement parameters
	$text_to_find = '<a ';
	$text_length = strlen($text_to_find);
	
	$class_menu_item = 'class="menu-item';
	$class_current_menu_item = $class_menu_item . ' current-menu-item';
	$close_quote = '" ';

	// find anchor positions
	$start_position = strpos($menu_string, $text_to_find, 0);

	// set up misc other variables for use in search/replace loop
	$class_text = '';
	
	// start replacing (inserting) class attributes, as needed, for each menu item
	do {
		// check to see if current page's title is present in menu (position if yes, else false)
		$current_page_title_position = strpos($menu_string, $current_page_title);
		$is_selected_page = ( false !== $current_page_title_position );

		// get the next menu item's starting point (making sure to search after
		//  current start position, but not past end of menu string!)
		$next_search_offset = $start_position + 1;
		$next_start_position = strpos($menu_string, $text_to_find, $next_search_offset);

		// check to see if we've reached the current page's menu item (if present)
		//  so we can indicate that it's selected
		$insert_current_menu_item_class = ( $is_selected_page )  &&
										  ( $start_position < $current_page_title_position ) &&
										  ( $next_start_position !== false && $next_start_position > $current_page_title_position );

		// insert the regular menu item class unless the current menu item class is applicable (ie, this is the selected page)
		$class_text = ( ! $insert_current_menu_item_class ) ? $class_menu_item : $class_current_menu_item;
		$class_text = $class_text . $close_quote;

		$menu_string = substr_replace($menu_string, $class_text, $start_position + $text_length, 0);

		// get the next start position
		if ( false !== $next_start_position ) {
			$start_position = strpos( $menu_string, $text_to_find, $next_search_offset );
		}

	} while ( $next_start_position !== false );

	return $menu_string;
}
endif;


if ( ! function_exists( 'redandblack_comicpress_copyright' ) ) :
/*
 * from:
 * http://www.wpbeginner.com/wp-tutorials/how-to-add-a-dynamic-copyright-date-in-wordpress-footer/
 *
 * @return mixed|string : copyright symbol followed by min - max post years
 */
function redandblack_comicpress_copyright() {
	global $wpdb;
	$copyright_dates = $wpdb->get_results("
		SELECT
		YEAR(min(post_date_gmt)) AS firstdate,
		YEAR(max(post_date_gmt)) AS lastdate
		FROM
		$wpdb->posts
		WHERE
		post_status = 'publish'
	");
	$output = '';
	if($copyright_dates) {
		$copyright = "&copy; " . $copyright_dates[0]->firstdate;
		if($copyright_dates[0]->firstdate != $copyright_dates[0]->lastdate) {
			$copyright .= '-' . $copyright_dates[0]->lastdate;
		}
		$output = $copyright;
	}
	return $output;
}
endif;


/*
 * from:
 * http://www.wpbeginner.com/wp-tutorials/how-to-add-a-dynamic-copyright-date-in-wordpress-footer/
 */
if ( ! function_exists( 'redandblack_contact_form_generate_response' ) ) :
	/*
	 * from:
	 * http://www.wpbeginner.com/wp-tutorials/how-to-add-a-dynamic-copyright-date-in-wordpress-footer/
	 *
	 * @param $type : string indicating form validation error or success
	 * @param $message : string containing message related to validation result
	 * 
	 * @return not applicable : side effect - sets global $redandblack_response variable value
	 */
function redandblack_contact_form_generate_response($type, $message)
{
	global $redandblack_response;

	if ($type == "success") {
		$redandblack_response = "<div class=\"success\">{$message}</div>";
	} else {
		$redandblack_response = "<div class=\"error\">{$message}</div>";
	}
}
endif;


if ( ! function_exists( 'redandblack_get_customizer_section_settings' ) ) {
/*
 * Retrieves list of customizer setting ids for a given section. This function assumes
 * that setting ids have been prefixed with their appropriate section id, eg:
 * section id = 'my_section_id'
 * setting id = 'my_section_id_setting_id'
 * 
 * @param $section_id : string containing section id for which to retrieve setting ids
 * 
 * @return mixed|array : array containing section settings with values, or FALSE if none found
 */
function redandblack_get_customizer_section_settings( $section_id ) {
	$theme_mods = get_theme_mods();
	$section_mods = [];
	
	foreach ($theme_mods as $setting_id=>$setting_value) {
		$section_id_found = strpos( $setting_id, $section_id );
		
		// only add items to section mods array if items have values
		if ( ( $section_id_found !== false ) && ( $setting_value !== false ) ) {
			$section_mods[ $setting_id ] = $setting_value;
		}
	}
	
	$number_section_mods_found = count( $section_mods );
	
	$return_val = $number_section_mods_found > 0 ? $section_mods : false;
	
	return $return_val;
}
}

if ( ! function_exists( 'redandblack_sanitize_phone' ) ) {
/*
 * Ensure phone number meets US standards.
 * 
 * @param $phone_string : string containing alleged phone number
 * 
 * @return mixed|string : formatted phone number value or false if invalid
 */
function redandblack_validate_phone( $phone_string ) {
	if ( empty( $phone_string ) ) {
		return false;
	}
	
	$disallowed_chars = '/[^0-9().+ -]/'; // note: ^ means NOT
	$has_valid_chars = ( 0 === preg_match( $disallowed_chars, $phone_string ) );
	if ( ! $has_valid_chars ) {
		return false;
	}
	
	$replace_chars = '/[^0-9]/'; // note: ^ means NOT
	$numeric_only_phone = preg_replace( $replace_chars, '', $phone_string );
	if ( empty( $numeric_only_phone ) ) {
		return false;
	}
	
	$len = strlen( $numeric_only_phone );
	$is_valid_numeric_US_phone_len = ( ($len === 7 ) || ( $len === 10 ) || ( $len === 11 ) );
	if ( ! $is_valid_numeric_US_phone_len ) {
		return false;
	}

	$formatted_phone = '';
	switch ( $len ) {
		case 7:
			// 123-1234
			$prefix = substr( $numeric_only_phone, 0, 3 );
			$line = substr( $numeric_only_phone, 3 );
			$formatted_phone = $prefix . '-' . $line;
			break;
		case 10:
			// (123) 123-1234
			$area = substr( $numeric_only_phone, 0, 3 );
			$prefix = substr( $numeric_only_phone, 3, 3 );
			$line = substr( $numeric_only_phone, 6 );
			$formatted_phone = '(' . $area . ') ' . $prefix . '-' . $line;
			break;
		case 11:
			// 1 (123) 123-1234
			$country = substr( $numeric_only_phone, 0, 1 );
			$area = substr( $numeric_only_phone, 1, 3 );
			$prefix = substr( $numeric_only_phone, 4, 3 );
			$line = substr( $numeric_only_phone, 7 );
			$formatted_phone = $country . ' (' . $area . ') ' . $prefix . '-' . $line;
			break;
	}
	
	return $formatted_phone;
}
}

if ( ! function_exists( 'redandblack_get_domain' ) ) {
/*
 * Get sanitized domain array for current site.
 * 
 * @return array : current site domain (eg, mysitedomain.com) separated into domain (eg, mysitedomain) and tld (top-level-domain, eg 'com' or 'co.uk')
 */
function redandblack_get_domain() {
	// home url, eg: http://mywordpress.mydomain.com
	// (params: '' ensures no closing slash, 'http' hardcodes scheme value)
	$home_url_parts = parse_url( home_url( '', 'http' ) );
	$host_part = $home_url_parts[ 'host' ];
	$domain_parts = explode( '.', $host_part );
	
	// create new array with domain (eg, google) and top-level-domain (TLD) (eg, com)
	$domain = [ 'domain' => '', 'tld' => '' ];
	switch ( count( $domain_parts ) ) {
		case 2:
			// eg, domain.com
			$domain = [ 'domain' => $domain_parts[0], 'tld' => $domain_parts[1] ];
		case 3:
			// eg, www.domain.com - want domain, com
			$domain = [ 'domain' => $domain_parts[1], 'tld' => $domain_parts[2] ];
			break;
		case 4:
			// eg, www.domain.co.uk - want domain, co.uk
			$full_tld = implode( '.', array_splice( $domain_parts, 2 ) );
			$domain = [ 'domain' => $domain_parts[1], 'tld' => $full_tld ];
			break;
		default:
			// non-standard domain, such as localhost
			$full_domain = implode( '.', $domain_parts );
			$domain = [ 'domain' => $full_domain, 'tld' => '' ];
			break;
	}
	
	array_walk( $domain, 'sanitize_text_field' );

	return $domain;
}
}
