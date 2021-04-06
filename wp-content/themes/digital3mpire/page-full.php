<?php
/*
Template Name: Full Width
*/
get_header(); ?>
<div class="row">
	<div class="large-12 medium-12 small-12 columns small-centered" role="main">

	<?php /* Start loop */ ?>
	<?php while (have_posts()) : the_post(); ?>
		<article <?php post_class() ?> id="post-<?php the_ID(); ?>">
			<header>
				<h2 class="entry-title font-effect-shadow-multiple font-effect-neon"><?php the_title(); ?></h2>
			</header>
			<div class="entry-content">
				<?php the_content(); ?>
			</div>
			<footer>
				<?php wp_link_pages(array('before' => '<nav id="page-nav"><p>' . __('Pages:', 'FoundationPress'), 'after' => '</p></nav>' )); ?>
				<p><?php the_tags(); ?></p>
			</footer>
			<!-- <?php comments_template(); ?> -->
		</article>
	<?php endwhile; // End the loop ?>

	</div>
</div>

<?php get_footer(); ?>
