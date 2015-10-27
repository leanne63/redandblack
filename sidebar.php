<?php
/**
 * The Blog (Posts) Sidebar
 *
 */
?>

<?php if ( is_active_sidebar( 'blog-sidebar' ) ) { ?>
	<div id="blog-sidebar" class="sidebar widget-area" role="complementary">
		<?php dynamic_sidebar( 'blog-sidebar' ); ?>
	</div><!-- #blog-sidebar -->
<?php } ?>
