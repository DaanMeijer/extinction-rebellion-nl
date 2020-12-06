<?php
/**
 * The template for displaying 404 pages (not found)
 */

get_header(); ?>
<div class="container">
    <div class="row mt-5">
        <div class="col-12">
            <h1 class="pt-5"><?php _e( 'Oops sorry! We were not able to find the page you were looking for...', 'theme-xrnl' ); ?></h1>
            <div class="my-5">
                <?php get_search_form(); ?>
            </div>
        </div>
    </div>
</div>
<?php get_footer(); ?>
