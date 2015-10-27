	<?php
	/**
	 * Template part for displaying child page content within page.php.
	 *
	 * @link https://codex.wordpress.org/Template_Hierarchy
	 */
	?>
	<?php /* <article> tags associated with this content are in page.php  */ ?>

		<header class="entry-header">
			<?php //the_title( '<h6 class="entry-title">', '</h6>' ); ?>
		</header><!-- .entry-header -->

		<div class="entry-content">
			<hgroup class="child-page-leader">
				<span class="child-page-leader-text"><?php the_title(); ?></span>
			</hgroup>
			<?php the_content(); ?>
			<?php
				wp_link_pages( array(
					'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'redandblack' ),
					'after'  => '</div>',
				) );
			?>
		</div><!-- .entry-content -->

		<footer class="entry-footer">
			<?php edit_post_link( esc_html__( 'Edit', 'redandblack' ), '<span class="edit-link">', '</span>' ); ?>
		</footer><!-- .entry-footer -->
