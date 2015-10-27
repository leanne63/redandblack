<?php
/**
 * Red and Black Customizer support
 */

/**
 * Implement Customizer additions and adjustments.
 *
 * based on tutorial here:
 * http://themefoundation.com/wordpress-theme-customizer/
 *
 * @param WP_Customize_Manager $wp_customize Customizer object.
 */
function redandblack_customize_register( $wp_customize ) {
	/***** start copyright panel *****/
	$wp_customize->add_section(
		'copyright_section', // section name
		array(
			'title' => 'Copyright Text',
			'description' => 'Set your custom copyright text here.',
			'priority' => 200,
		)
	);

	/*** copyright textbox ***/
	$wp_customize->add_setting(
		'copyright_textbox', // setting id
		array(
			'default' => '',
			'sanitize_callback' => 'wp_filter_nohtml_kses',
		)
	);

	$wp_customize->add_control(
		'copyright_textbox', // setting id associated with this control
		array(
			'section' => 'copyright_section', // section associated with this control
			'label' => 'Copyright Text',
			'type' => 'text',
		)
	);

	/*** hide copyright checkbox ***/
	$default_copyright_checked_val = ( get_theme_mod( 'copyright_textbox' ) == '' ? 1 : 0 );
	$wp_customize->add_setting(
		'hide_copyright',
		array(
			'default' => $default_copyright_checked_val,
			'sanitize_callback' => 'redandblack_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'hide_copyright',
		array(
			'section' => 'copyright_section',
			'label' => 'Hide Copyright Text',
			'type' => 'checkbox',
		)
	);

	/***** end copyright panel *****/


	/***** start featured pages panel *****/
	$wp_customize->add_section(
		'featured_page_section', // section name
		array(
			'title' => 'Featured Pages',
			'description' => 'Select pages whose content you wish to feature on your Home page.',
			'priority' => 210,
		)
	);

	/*** featured pages dropdown 1 ***/
	$wp_customize->add_setting(
		'featured_page_1', // setting id
		array(
			'default' => 0,
			'sanitize_callback' => 'redandblack_sanitize_dropdown_pages',
		)
	);

	$wp_customize->add_control(
		'featured_page_1', // setting id associated with this control
		array(
			'section' => 'featured_page_section', // section associated with this control
			'label' => 'Featured Page 1',
			'type' => 'dropdown-pages',
		)
	);

	/*** featured pages dropdown 2 ***/
	$wp_customize->add_setting(
		'featured_page_2', // setting id
		array(
			'default' => 0,
			'sanitize_callback' => 'redandblack_sanitize_dropdown_pages',
		)
	);

	$wp_customize->add_control(
		'featured_page_2', // setting id associated with this control
		array(
			'section' => 'featured_page_section', // section associated with this control
			'label' => 'Featured Page 2',
			'type' => 'dropdown-pages',
		)
	);

	/***** end featured pages panel *****/
	/* 
	 * NOTE: if changing number of featured pages that user can select,
	 * ensure that MAX_NUM_FEATURED_PAGES value is set correctly
	 * (in template-tags.php)
	 */

	/***** start social media section *****/
	$section_id = 'social_media';
	$wp_customize->add_section(
		$section_id,
		array(
			'title' => 'Social Media',
			'description' => 'Enter the URL to your account for each service for the icon to appear in the header.',
			'priority' => '220',
		) 
	);
	
	$priority = 5;

	/*** email ***/
	$setting_id = $section_id . '_email';
	$wp_customize->add_setting(
		$setting_id, // setting id
		array(
			'default' => '',
			'sanitize_callback' => 'sanitize_email',
		)
	);

	$wp_customize->add_control(
		$setting_id, // setting id associated with this control
		array(
			'section' => $section_id, // section associated with this control
			'label' => 'email:',
			'type' => 'text',
			'priority' => $priority,
		)
	);
	
	/*** other social media ***/
	global $social_sites;
	
 	foreach($social_sites as $social_site) {
		$social_site = sanitize_text_field( $social_site );
		$setting_id = $section_id . '_' . $social_site;
 
		$wp_customize->add_setting(
			$setting_id, // setting id
			array(
			'default' => '',
			'sanitize_callback' => 'esc_url_raw',
		) );
 
		$wp_customize->add_control(
			"$setting_id", // setting id associated with this control
			array(
				'section' => $section_id,
				'label' => $social_site . ':',
				'type' => 'text',
				'priority' => $priority += 5,
			)
		);
	}
	
	/***** end social media panel *****/
}
add_action( 'customize_register', 'redandblack_customize_register' );


/***** sanitization functions *****/

/**
 * Checkbox sanitization callback.
 *
 * Sanitization callback for 'checkbox' type controls. This callback sanitizes `$checked`
 * as a boolean value, either TRUE or FALSE.
 *
 *
 * from:
 * https://github.com/WPTRT/code-examples/blob/master/customizer/sanitization-callbacks.php
 *
 *
 * @param bool $checked Whether the checkbox is checked.
 * @return bool Whether the checkbox is checked.
 */
function redandblack_sanitize_checkbox( $checked ) {
	// Boolean check.
	$return_val = ( ( isset( $checked ) && true == $checked ) ? true : false );
	return $return_val;
}


/**
 * Drop-down Pages sanitization callback (modified by LML).
 *
 * - Sanitization: dropdown-pages
 * - Control: dropdown-pages
 * 
 * Sanitization callback for 'dropdown-pages' type controls. This callback sanitizes `$page_id`
 * as an absolute integer, and then validates that $input is the ID of a published page.
 * 
 * @see absint() https://developer.wordpress.org/reference/functions/absint/
 * @see get_post_status() https://developer.wordpress.org/reference/functions/get_post_status/
 *
 * @param int                  $page    Page ID.
 * @param WP_Customize_Setting $setting Setting instance.
 * @return int|string Page ID if the page is published; otherwise, the setting default.
 */
function redandblack_sanitize_dropdown_pages( $page_id, $setting ) {
	// Ensure $input is an absolute integer.
	$page_id = absint( $page_id );

	// If $page_id is an ID of a published page, return it; otherwise, return the default.
	$is_published = 'publish' === get_post_status( $page_id );
	$is_valid_page = $is_published && ( 'page' === get_post_type( $page_id ) );

	return ( $is_valid_page ? $page_id : $setting->default );
}


/**
 * Email sanitization callback.
 *
 * - Sanitization: email
 * - Control: text
 * 
 * Sanitization callback for 'email' type text controls. This callback sanitizes `$email`
 * as a valid email address.
 * 
 * @see sanitize_email() https://developer.wordpress.org/reference/functions/sanitize_key/
 * @link sanitize_email() https://codex.wordpress.org/Function_Reference/sanitize_email
 *
 * @param string               $email   Email address to sanitize.
 * @param WP_Customize_Setting $setting Setting instance.
 * @return string The sanitized email if not null; otherwise, the setting default.
 */
function redandblack_sanitize_email( $email, $setting ) {
	// Strips out all characters that are not allowable in an email address.
	$email = sanitize_email( $email );

	// If $email is a valid email, return it; otherwise, return the default.
	$return_val = ( ! is_null( $email ) ? $email : $setting->default );
	return $return_val;
}

