<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 */

?>

		<?php /* content div started in header.php; continued in index.php; terminated here */ ?>
	</div><!-- #content -->

	<footer id="colophon" class="site-footer" role="contentinfo">
		<nav id="site-navigation-footer" class="main-navigation" role="navigation">
		<?php
			$menu_parameters = array(
				'echo'			=> 0, /* 0 causes menu to be returned rather than echoed */
				'items_wrap'	  => '%3$s',
				'depth'		   => 1, /* see: https://codex.wordpress.org/Function_Reference/wp_nav_menu */
				'theme_location'  => 'footer-menu',
			);

			// retrieve menu without WordPress default 'ul' or 'li' tags
			$menu_string = strip_tags(wp_nav_menu($menu_parameters ), '<div><a>' );
			// if Static Front Page is set to 'Your latest posts', the posts page link needs to change
			if ( ! ( get_option('show_on_front') == 'page' ) ) {
				$menu_string = redandblack_fix_posts_link($menu_string);
			}
			$menu_string = redandblack_add_item_classes_to_menu($menu_string);

			echo $menu_string;
		?>
			<button class="menu-toggle" aria-controls="footer-menu" aria-expanded="false"><?php esc_html_e( 'Footer Menu', 'redandblack' ); ?></button>
		</nav><!-- #site-navigation-footer -->
		<?php
			$section_id = 'social_media';
			$social_mods = redandblack_get_customizer_section_settings( $section_id );

			if ( ! empty( $social_mods ) ) :
		?>
		<nav id="social-media-navigation-footer" class="social-media-navigation">
			<div id="social-media-links">
			<?php
				foreach ( $social_mods as $mod_id=>$mod_value ) :
					$active_site = substr( strrchr( $mod_id, '_'), 1 );
					if ( 'email' !== $active_site ) :
			?>
				<a id="<?php echo $mod_id; ?>" target="_blank" href="<?php echo esc_url( $mod_value ); ?>">
					<img src="<?php echo get_template_directory_uri() . "/images/$active_site.png" ?>" alt="<?php echo $active_site; ?>" />
				</a>
			<?php	else : ?>
				<a id="<?php echo $mod_id; ?>" target="_blank" href="mailto:<?php echo antispambot( is_email( $mod_value ) ); ?>">
					<img src="<?php echo get_template_directory_uri() . "/images/$active_site.png"; ?>" alt="<?php echo $active_site; ?>" />
				</a>
			<?php
					endif;
				endforeach;
			?>
			</div>
		</nav><!-- #social-media-navigation-footer -->
		<?php endif; ?>
		<?php if( ! get_theme_mod( 'hide_copyright' ) ) : ?>
		<div id="copyright" class="copyright-text">
			<?php echo get_theme_mod( 'copyright_textbox' ), ' ', redandblack_comicpress_copyright(), '<br />'; ?>
		</div><!-- #copyright -->
		<?php endif; ?>
	</footer>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
