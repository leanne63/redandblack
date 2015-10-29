<?php
/**
 * Template part for displaying contact page content in page.php.
 *
 * This template will display content of any page with a slug of 'contact-us',
 * followed by a contact form.
 * 
 * Note: we arrive here via page.php identification of slug 'contact-us'.
 * 
 * Form on this page based on examples found here:
 * https://premium.wpmudev.org/blog/how-to-build-your-own-wordpress-contact-form-and-why/
 */
?>

<?php /**************************************************************************/ ?>
<?php
	//response messages and other constants
	define( 'REDANDBLACK_NOT_HUMAN', 'Human verification incorrect.' );
	define( 'REDANDBLACK_MISSING_CONTENT', 'Please supply all required information.' );
	define( 'REDANDBLACK_NAME_INVALID', 'Name contains invalid characters. Only letters, apostrophe, and hyphen allowed.' );
	define( 'REDANDBLACK_EMAIL_INVALID', 'Email address invalid.' );
	define( 'REDANDBLACK_PHONE_INVALID', 'Phone contains invalid characters.' );
	define( 'REDANDBLACK_MESSAGE_SEND_FAILED', 'Message was not sent. Try Again.' );
	define( 'REDANDBLACK_MESSAGE_SENT', 'Thanks, %s! Your message has been sent.' );
	
	define( 'REDANDBLACK_HUMAN_VERIFICATION_VALUE', '2' );
	define( 'REDANDBLACK_DISALLOWED_NAME_VALUES', "/[^A-Za-z' -]/" ); // note: ^ means NOT

	// default form values
	$user_name = '';
	$honeypot_email_address = '';
	$email_address = '';
	$physical_address = '';
	$phone = '';
	$message = '';
	$verify_human = '';
	$disabled_attribute = '';
	
	// using all checks so user can't submit from within Customizer
	$in_customizer_preview =
			isset( $_POST['wp_customize'] ) && 'on' === sanitize_text_field( $_POST['wp_customize'] );
	$post_var_set = ( 'POST' === sanitize_text_field( $_SERVER['REQUEST_METHOD'] ) );
	$submit_button_pressed =
			isset( $_POST[ 'submit-button' ] ) && 'submit' === sanitize_text_field( $_POST[ 'submit-button' ] );
	$is_submit_postback = ( ! $in_customizer_preview && $post_var_set && $submit_button_pressed );

	if ( $is_submit_postback ) {
		// contact-email is a "honeypot" field, hidden from user, to prevent spam
		// if a value is present there, this must be a spambot trying to use our form!
		$is_human_message = ( empty( $_POST['contact-email'] ) );
		
		if ( $is_human_message ) {		
			$user_name = stripslashes( $_POST['contact-name'] );
			$is_valid_name = ( 0 === preg_match( REDANDBLACK_DISALLOWED_NAME_VALUES, $user_name ) );

			$email_address = is_email( $_POST['contact-econtact'] );
			$is_valid_email = ( false !== $email_address );

			$phone = $_POST['contact-phone'];
			$formatted_phone = empty( $phone ) ? '' : redandblack_validate_phone( $phone );
			$is_valid_phone = ( empty( $phone ) || ! empty( $formatted_phone ) );

			$physical_address = esc_textarea( stripslashes( $_POST['contact-address'] ) );
			$message = stripslashes( $_POST['contact-text'] );

			$verify_human = $_POST['contact-verify-human'];
			$is_verified_human = ( REDANDBLACK_HUMAN_VERIFICATION_VALUE === $verify_human );

			// validate contact fields
			$is_validated = false;
			if ( empty( $user_name )				||
				 empty( $email_address )	||
				 empty( $message ) ) {

				redandblack_contact_form_generate_response("error", REDANDBLACK_MISSING_CONTENT);

			} else if ( ! $is_valid_name ) {
				redandblack_contact_form_generate_response("error", REDANDBLACK_NAME_INVALID);

			} else if ( ! $is_valid_email ) {
				redandblack_contact_form_generate_response("error", REDANDBLACK_EMAIL_INVALID);

			} else if ( ! $is_valid_phone ) {
				redandblack_contact_form_generate_response("error", REDANDBLACK_PHONE_INVALID);

			} else if ( ! $is_verified_human ) {
				redandblack_contact_form_generate_response("error", REDANDBLACK_NOT_HUMAN);

			} else {
				$is_validated = true;
			}

			if ( $is_validated ) {
				//php mailer variables
				$website_name = sanitize_text_field( get_bloginfo('name') );
				$domain_array = redandblack_get_domain();
				$from_email = 'noreply@' . implode( '.', $domain_array );
				$reply_to_email = $email_address;

				$admin_email = sanitize_email( get_option('admin_email') );
				$contact_email = sanitize_email( get_theme_mod( 'social_media_email' ) );
				$to = ( $contact_email != false ? $contact_email : $admin_email );
				$subject = 'Message from the ' . $website_name . ' website!';
				
				$headers[] = "From: {$domain_array[ 'domain' ]}-admin <$from_email>";
				$headers[] = "Reply-To: $reply_to_email";

				// put together message parts (note: PHP mailer requires double quotes to process newlines)
				// message doesn't need sanitization 'cause not being saved and is sent plain text
				$newline = "\n";
				$full_message = '';
				$full_message = $full_message . 'Name: ' . $user_name . $newline;
				$full_message = $full_message . 'Email: ' . $email_address . $newline;
				$full_message = $full_message . 'Phone: ' . $formatted_phone . $newline;
				$full_message = $full_message . 'Address: ' . $newline . $physical_address . $newline;
				$full_message = $full_message . $newline;
				$full_message = $full_message . 'Message:' . $newline . $message . $newline;

				$sent = wp_mail($to, $subject, $full_message, $headers);
				if ($sent) {
					if ( WP_DEBUG ) {
						$log_string = 'EMAIL MESSAGE SENT' . $newline;
						$log_string = $log_string . $headers[ 0 ] . $newline;
						$log_string = $log_string . $headers[ 1 ] . $newline;
						$log_string = $log_string . 'To: ' . $to . $newline;
						$log_string = $log_string . 'Subject: ' . $subject . $newline;
						$log_string = $log_string . 'Message Body: ' . $newline . $full_message . $newline;
						error_log($log_string);
					}
					
					$disabled_attribute = 'disabled ';
					redandblack_contact_form_generate_response("success", REDANDBLACK_MESSAGE_SENT);
					
				} else {
					if ( WP_DEBUG ) {
						$log_string = 'MESSAGE MESSAGE FAILED' . $newline;
						$log_string = $log_string . $headers[ 0 ] . $newline;
						$log_string = $log_string . $headers[ 1 ] . $newline;
						$log_string = $log_string . 'To: ' . $to . $newline;
						$log_string = $log_string . 'Subject: ' . $subject . $newline;
						$log_string = $log_string . 'Message Body: ' . $newline . $full_message . $newline;
						error_log($log_string);
					}
					
					redandblack_contact_form_generate_response("error", REDANDBLACK_MESSAGE_SEND_FAILED);
				}
			}
		} else { // this is a spambot message - just clear everything out
			$user_name = '';
			$honeypot_email_address = '';
			$email_address = '';
			$physical_address = '';
			$phone = '';
			$message = '';
			$verify_human = '';
			$disabled_attribute = '';
		}
	} // end if is_submit_postback
?>
<?php /**************************************************************************/ ?>

<?php
	$is_parent = (int) $post->post_parent === 0 ? true : false;
	
	if ( $is_parent ) : // needs an article tag if it's a parent page
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php
	endif;
	
	if ( ! $is_parent ) : 
?>
	<header class="entry-header child-page-leader">
		<span class="child-page-leader-text"><?php the_title(); ?></span>
	</header><!-- .entry-header -->
<?php endif; ?>
	
	<?php /**************************************************************************/ ?>
	<div class="entry-content">
		<?php the_content(); ?>
		<div id="contact-form">
			<?php
				printf( $GLOBALS['redandblack_response'], sanitize_text_field( $user_name ) );
			?>
			<form action="<?php esc_url( get_permalink( $post->post_parent ) ) ?>" method="POST">
				<div id="contact-name-div" class="contact-item-div">
					<label for="contact-name">Name: <span>*</span> <br>
						<input <?php echo $disabled_attribute; ?>class="form-input-item" type="text" name="contact-name" size="30" value="<?php echo sanitize_text_field( $user_name ); ?>">
					</label>
				</div>
				<div id="contact-email-div" class="contact-item-div">
					<label for="contact-email">Fake Email - DO NOT USE: <span>*</span> <br>
						<input <?php echo $disabled_attribute; ?>class="form-input-item" type="email" name="contact-email" size="30" value="<?php echo $honeypot_email_address; ?>">
					</label>
				</div>
				<div id="contact-econtact-div" class="contact-item-div">
					<label for="contact-econtact">Reply-To Email: <span>*</span> <br>
						<input <?php echo $disabled_attribute; ?>class="form-input-item" type="email" name="contact-econtact" size="30" value="<?php echo $email_address; ?>">
					</label>
				</div>
				<div id="contact-phone-div" class="contact-item-div">
					<label for="contact-name">Phone:<br>
						<input <?php echo $disabled_attribute; ?>class="form-input-item" type="text" name="contact-phone" size="20" maxlength="17" value="<?php echo $phone; ?>">
					</label>
				</div>
				<div id="contact-address-div" class="contact-item-div">
					<label for="contact-name">Address:<br>
						<textarea <?php echo $disabled_attribute; ?>class="form-input-item" type="text" name="contact-address" cols="30" rows="3"><?php echo $physical_address; ?></textarea>
					</label>
				</div>
				<div id="contact-text-div" class="contact-item-div">
					<label for="message_text">Message: <span>*</span> <br>
						<textarea <?php echo $disabled_attribute; ?>class="form-input-item" type="text" name="contact-text" cols="30" rows="5"><?php echo $message; ?></textarea>
					</label>
				</div>
				<div id="contact-verify-human-div" class="contact-item-div">
					<label for="message_verify_human">Human Verification: <span>*</span> <br>
						<input <?php echo $disabled_attribute; ?>class="form-input-item" type="text" name="contact-verify-human" size="5" value=""> + 3 = 5
					</label>
				</div>
				<?php if ( '' === $disabled_attribute ) : ?>
				<div id="contact-buttons-div" class="contact-item-div">
					<button type="submit" name="submit-button" value="submit">Submit</button>
					<button type="reset" name="reset-button" value="reset">Reset</button>
				</div>
				<?php endif; ?>
			</form>
		</div>
<?php /**************************************************************************/ ?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php edit_post_link( esc_html__( 'Edit', 'redandblack' ), '<span class="edit-link">', '</span>' ); ?>
	</footer><!-- .entry-footer -->

<?php if ( ( int ) $post->post_parent === 0 ) : // only parent pages need an article tag ?>
</article><!-- #post-## -->
<?php endif; ?>
