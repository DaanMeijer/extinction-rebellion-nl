<?php
/**
 * The template part for displaying content
 */
?>

<article class="my-5">
	<header class="entry-header">
		<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
	</header>

    <div class="row">
        <div class="col-12 col-md-8">
            <div class="excerpt"><?php the_excerpt(); ?></div>

            <div class="my-2 text-muted"><?php the_date(); ?></div>

            <a href="<?php the_permalink(); ?>" rel="bookmark" class="font-xr text-uppercase">
            <?php echo _e( 'Continue reading', 'theme-xrnl'); ?>
            </a>
        </div>
        <div class="col-12 col-md-4">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail('medium_large', ['class' => 'img-fluid pb-2 pt-0 pt-sm-2']); ?>
            </a>
        </div>
    </div>

</article>
