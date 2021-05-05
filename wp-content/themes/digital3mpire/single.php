<?php get_header(); ?>
<div class="row">
	<div class="small-12 medium-10 columns small-centered" role="main">

	<?php do_action('foundationPress_before_content'); ?>

	<?php while (have_posts()) : the_post(); ?>
		<article <?php post_class() ?> id="post-<?php the_ID(); ?>">
			<header>
				<?php FoundationPress_entry_meta(); ?>
				<h1 class="entry-title font-effect-shadow-multiple font-effect-neon"><?php the_title(); ?></h1>
			</header>
			<?php do_action('foundationPress_post_before_entry_content'); ?>
			<div class="entry-content">

			<?php if ( has_post_thumbnail() ): ?>
				<div class="row">
					<div class="column">
						<?php the_post_thumbnail('', array('class' => 'th')); ?>
					</div>
				</div>
			<?php endif; ?>

			<?php the_content(); ?>
			</div>
			<footer>
				<?php wp_link_pages(array('before' => '<nav id="page-nav"><p>' . __('Pages:', 'FoundationPress'), 'after' => '</p></nav>' )); ?>
				<p><?php the_tags(); ?></p>
			</footer>
			<?php do_action('foundationPress_post_before_comments'); ?>
<div class="small-12 medium-10 large-10 columns">
			<?php comments_template(); ?>
		</div>
			<?php do_action('foundationPress_post_after_comments'); ?>
		</article>
	<?php endwhile;?>

	<?php do_action('foundationPress_after_content'); ?>

	</div>
	<?php /* get_sidebar(); */ ?>
</div>
<?php get_footer(); ?>
