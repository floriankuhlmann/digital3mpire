<?php
/**
 * The default template for displaying content. Used for both single and index/archive/search.
 *
 * @subpackage FoundationPress
 * @since FoundationPress 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header>
		<?php FoundationPress_entry_meta(); ?>
		<div class="categorylist"><?php the_category(', '); ?></div>		
		<h2 class="green font-effect-shadow-multiple font-effect-neon"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
	</header>
    <figure class="post-thumbnail">
        <a class="post-thumbnail-inner alignwide" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
            <?php the_post_thumbnail( 'post-thumbnail' ); ?>
        </a>
        <?php if ( wp_get_attachment_caption( get_post_thumbnail_id() ) ) : ?>
            <figcaption class="wp-caption-text"><?php echo wp_kses_post( wp_get_attachment_caption( get_post_thumbnail_id() ) ); ?></figcaption>
        <?php endif; ?>
    </figure>
	<div class="entry-content">
		<?php the_content(__('Continue reading...', 'FoundationPress')); ?>
	</div>
	<footer>
		<?php $tag = get_the_tags(); if (!$tag) { } else { ?><p><?php the_tags(); ?></p><?php } ?>
	</footer>
	<!-- <hr />-->
</article>
