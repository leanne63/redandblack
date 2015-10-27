<?php
/**
 * The template for displaying all "pages".
 * The default blog ("posts") page runs with index.php.
 */
?>

<?php get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">

		<?php /*************** SHOW PARENT PAGE CONTENT, IF ANY ***************/ ?>
		
		<?php if($wp_query->post_count > 0 && ! $wp_query->posts[0]->post_content == '') : ?>
		<section id="parent-page-section">
		<?php else : ?>
		<section id="parent-page-section" class="section-hide">
		<?php endif; ?>
			<?php /* The Loop */
				while ( have_posts() ) :
					the_post();

					// get appropriate content template
					switch ($post->post_name) :
						case 'contact-us':
							get_template_part('template-parts/content', 'contact-us');
							break;
						default:
							get_template_part('template-parts/content', 'page');
							break;
					endswitch;

					// If comments are open or we have at least one comment, load up the comment template.
					if ( comments_open() || get_comments_number() ) {
						comments_template();
					}

				endwhile;
				// End of The Loop.
			?>
		</section><!-- #parent-page-section -->

		<?php /*************** SHOW CHILD PAGE CONTENT, IF ANY ***************/ ?>

		<?php
			// child pages will be positioned based on whether featured pages are present
			$featured_page_1_id = get_theme_mod( 'featured_page_1' );
			$have_featured_page_1 = ( 0 !== $featured_page_1_id );
			
			$featured_page_2_id = get_theme_mod( 'featured_page_2' );
			$have_featured_page_2 = ( 0 !== $featured_page_2_id );
			
			$have_featured_pages = $have_featured_page_1 || $have_featured_page_2;
			$num_featured_pages = 0;
			if ( $have_featured_pages ) :
				$num_featured_pages = ( $have_featured_page_1 && $have_featured_page_2 ) ? 2 : 1;
			endif;

			$is_page_to_show_on_front = ( get_option('show_on_front') === 'page' ) && ( is_front_page() );
			$is_home_page = $is_page_to_show_on_front || ( $post->post_name === 'home' );

			/* setup for 2nd Loop to retrieve and display child pages, if any exist */  
			$args = array(
				'post_parent' => $post->ID,
				'post_type' => 'page',
				'orderby' => array( 'menu_order' => 'ASC' ), // sorts pages by "order" field set in editor
			);
			$child_query = new WP_Query( $args );
			$num_child_pages = $child_query->post_count;
			
			 // only display the child section if child pages are present
			if ( $num_child_pages > 0 ) :
				$num_child_pages_loaded = 0;
				
				$child_section_class_string = '';
				if ( $is_home_page ) :
					switch ( $num_featured_pages ) :
						case 0:
							$child_section_class_string = 'class="full-width"';
							break;
						case 1:
							$child_section_class_string = 'class="half-width"';
							break;
						case 2:
							$child_section_class_string = 'class="third-width"';
							break;
					endswitch;
				endif;
		?>
		<section id="child-page-section" <?php echo $child_section_class_string ?>>
			<?php
				/* 2nd Loop to retrieve and display child pages */
				while ( $child_query->have_posts() ) :
					$child_query->the_post();

					$class_list = '';

					if ( ! $is_home_page ) :
						$class_list = ( $num_child_pages % 2 == 0 ) ? 'half-width ' : 'full-width ';
					else :
						$class_list = 'full-width ';
					endif;

					if ( ! $is_home_page && stripos( $class_list, 'full-width') === false ) :  
						switch ($num_child_pages_loaded % 2) :
							case 0:
								$class_list = $class_list . ' article-left ';
								break;
							case 1:
								$class_list = $class_list . ' article-right ';
								break;
					   endswitch;
					endif;
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class($class_list); ?>>
				<?php
					switch ( $post->post_name ) :
						case 'contact-us':
							get_template_part( 'template-parts/content', 'contact-us' );
							break;
						default:
							get_template_part( 'template-parts/content', 'child-page' );
							break;
					endswitch;
				?>
			</article><!-- #post-## -->
			<?php
					$num_child_pages_loaded++;
				endwhile;
			// End of the secondary loop.
			?>
		</section><!-- #child-page-section -->
		<?php
				wp_reset_postdata();
			endif; // end 'if' to display section only if child pages are present
		?>
		
		<?php /*************** SHOW FEATURED PAGE CONTENT, IF ANY ***************/ ?>

		<?php
			if ( $is_home_page && $have_featured_pages ) :
				$featured_pages_section_class_string = '';
				$article_class_list = ' featured-page';
				$num_sections = ( $num_child_pages > 0 ) ? $num_featured_pages + 1 : $num_featured_pages;
				
				// featured pages section width is relative to child pages section
				switch ( $num_sections ) :
					case 1:  // no children, 1 featured page - can take whole width
						$featured_pages_section_class_string = 'class="full-width"';
						break;
					case 2:  // either 1 child/1 featured, or two featured - can take 1/2 width
						$featured_pages_section_class_string = 'class="half-width"';
						break;
					case 3:  // 1 child/2 featured - featured section needs 2/3 width
						$featured_pages_section_class_string = 'class="two-thirds-width"';
						break;
				endswitch;
		?>
		<section id="featured-pages-section" <?php echo $featured_pages_section_class_string ?>>
			<?php
				if ( $have_featured_page_1 ) :
					$args = array(
						'page_id' => $featured_page_1_id,
						'post_type' => 'page',
					);
					$featured_pages_query = new WP_Query( $args );

					while ( $featured_pages_query->have_posts() ) :
						$featured_pages_query->the_post();
			?>
			<article id="featured_page_1" class="article-left <?php echo ($num_featured_pages == 1 ? 'full-width' : 'half-width'); ?>">
			<!-- <article id="post-<?php the_ID(); ?>" <?php post_class($class_list); ?>> -->
				<?php get_template_part( 'template-parts/content', 'featured-page' ); ?>
			</article>
			<?php
					endwhile;
					wp_reset_postdata();
				endif;

				if ( $have_featured_page_2 ) :
					$args = array(
						'page_id' => $featured_page_2_id,
						'post_type' => 'page',
					);
					$featured_pages_query = new WP_Query( $args );

					while ( $featured_pages_query->have_posts() ) :
						$featured_pages_query->the_post();
			?>
			<article id="featured_page_2" class="article-right <?php echo ($num_featured_pages == 1 ? 'full-width' : 'half-width'); ?>">
			<!-- <article id="post-<?php the_ID(); ?>" <?php post_class($class_list); ?>> -->
				<?php get_template_part( 'template-parts/content', 'featured-page' ); ?>
			</article>
			<?php
					endwhile;
					wp_reset_postdata();
				endif;
			?>
		</section><!-- #featured-pages-section -->
		<?php endif; ?>


	</main><!-- #main -->
</div><!-- #primary - .content-area -->

<?php get_footer(); ?>
