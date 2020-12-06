<?php
/**
 * The template for displaying all single posts and attachments
 */

get_header(); ?>

<div class="container">
    <div class="row mt-5">
        <div class="col-12 col-lg-8">
            <?php
            // Start the loop.
            while ( have_posts() ) : the_post();

                // Include the single post content template.
                get_template_part( 'template-parts/content', 'single' );

                if ( is_singular( 'attachment' ) ) {
                    // Parent post navigation.
                    the_post_navigation( array(
                        'screen_reader_text' => ' ',
                        'prev_text' => _x( '<span class="meta-nav">Published in</span><span class="post-title">%title</span>', 'Parent post link', 'theme-xrnl' ),
                    ) );
                } elseif ( is_singular( 'post' ) ) {
                    // Previous/next post navigation.
                    the_post_navigation( array(
                        'screen_reader_text' => ' ',
                        'next_text' => '<span class="screen-reader-text">' . __( 'Next post:', 'theme-xrnl' ) . '</span> ' .
                            '<span class="post-title">%title</span>',
                        'prev_text' => '<span class="screen-reader-text">' . __( 'Previous post:', 'theme-xrnl' ) . '</span> ' .
                            '<span class="post-title">%title</span>',
                    ) );
                }

                // End of the loop.
            endwhile;
            ?>
        </div>
        <div class="col-12 col-lg-4">
            <?php get_sidebar(); ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
