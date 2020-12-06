<?php get_header(); ?>

<div class="container">
    <div class="row mt-5">
        <div class="col-12">
            <?php if ( have_posts() ) : ?>
                <?php if ( is_home() && ! is_front_page() ) : ?>
                    <h1 class="page-title"><?php single_post_title(); ?></h1>
                <?php endif; ?>

                <?php
                // Start the loop.
                while ( have_posts() ) : the_post();

                    /*
                    * Include the Post-Format-specific template for the content.
                    * If you want to override this in a child theme, then include a file
                    * called content-___.php (where ___ is the Post Format name) and that will be used instead.
                    */
                    get_template_part( 'template-parts/content', get_post_format() );

                // End the loop.
                endwhile;

                // Pagination
                the_posts_pagination( array(
                    // 'format' => '?page=%#%', // todo find out why query params don't work
                    'type' => 'list',
                    'screen_reader_text' => ' '
                ) );

            // If no content, include the "No posts found" template.
            else :
                get_template_part( 'template-parts/content', 'none' );
            endif;
            ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
