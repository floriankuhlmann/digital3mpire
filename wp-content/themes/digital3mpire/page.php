<?php get_header(); ?>

<div class="row">
	<div class="large-10 medium-10 small-12 columns small-centered" role="main">

	<?php do_action('foundationPress_before_content'); ?>

	<?php while (have_posts()) : the_post(); ?>
		<article <?php post_class() ?> id="post-<?php the_ID(); ?>">
			<header>
				<h2 class="entry-title font-effect-shadow-multiple font-effect-neon"><?php the_title(); ?></h2>
			</header>
			<?php do_action('foundationPress_page_before_entry_content'); ?>
			<div class="entry-content">
				<?php the_content(); ?>
			</div>
			<footer>
				<?php wp_link_pages(array('before' => '<nav id="page-nav"><p>' . __('Pages:', 'FoundationPress'), 'after' => '</p></nav>' )); ?>
				<p><?php the_tags(); ?></p>
			</footer>
			<?php do_action('foundationPress_page_before_comments'); ?>
			<?php /* comments_template(); */ ?>
			<?php do_action('foundationPress_page_after_comments'); ?>
		</article>
	<?php endwhile;?>

	<?php do_action('foundationPress_after_content'); ?>

	</div>
	<?php /* get_sidebar(); */ ?>
</div>
<?php get_footer(); ?>
