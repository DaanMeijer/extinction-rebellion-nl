<?php
/**
 * The template part for displaying a message that posts cannot be found
 */
?>

<section class="no-results not-found">
    <header class="entry-header">
        <h1 class="page-title">
            <?php _e( 'Nothing Found', 'theme-xrnl' ); ?>
        </h1>
    </header>
    <div class="mb-5">
        <?php if ( is_search() ) : ?>
            <p><?php _e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'theme-xrnl' ); ?></p>
            <div class="my-3">
                <?php get_search_form(); ?>
            </div>
        <?php else : ?>
            <p><?php _e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'theme-xrnl' ); ?></p>
            <div class="my-3">
                <?php get_search_form(); ?>
            </div>
        <?php endif; ?>
    </div>
</section>
