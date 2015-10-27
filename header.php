<?php
	/**
	 * The header template file.
	 *
	 * @link https://codex.wordpress.org/Template_Hierarchy
	 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<?php wp_head(); ?>
	<?php if ( is_active_sidebar( 'blog-sidebar' ) ) { ?>
		<style type="text/css">
			/* media queries */
			@media screen and (min-width: 37.5em) {
				body.blog #blog-sidebar {
					background: url(images/dash.png) repeat-y top right;
					float: left;
					margin-top: 2em;
					width: 25%;
				}
				body.blog #primary.content-area {
					float: right;
					width: 75%;
				}
			}
		</style>
	<?php } ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed site">

	<header id="masthead" class="site-header" role="banner">
		<nav id="site-navigation-header" class="main-navigation" role="navigation">
			<button class="menu-toggle" aria-controls="header-menu" aria-expanded="false"><?php esc_html_e( 'Header Menu', 'redandblack' ); ?></button>
			<?php
			$menu_parameters = array(
				'echo'			=> 0, /* 0 causes menu to be returned rather than echoed */
				'items_wrap'	  => '%3$s',
				'depth'		   => 1, /* see: https://codex.wordpress.org/Function_Reference/wp_nav_menu */
				'theme_location'  => 'header-menu',
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
		</nav>
		<?php
		// check if the post has a Post Thumbnail assigned to it.
		if ( has_post_thumbnail() ) {
			the_post_thumbnail();
		}
		?>
	</header>

	<div id="content" class="site-content">
		<?php /* content div started here; continued in index.php; terminated in footer.php */ ?>
