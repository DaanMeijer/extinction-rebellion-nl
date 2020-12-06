<?php
/**
 * The template part for displaying single posts
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <h1 class="entry-title"><?php the_title(); ?></h1>
    </header>
    <?php if (get_post_type( get_the_ID() ) != 'meetup_events') { ?>
        <div class="my-2 text-muted"><?php the_date(); ?></div>
    <?php } ?>
    <div class="entry-content">
        <?php
            the_content();

            wp_link_pages( array(
            'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'theme-xrnl' ) . '</span>',
            'after'       => '</div>',
            'link_before' => '<span>',
            'link_after'  => '</span>',
            'pagelink'    => '<span class="screen-reader-text">' . __( 'Page', 'theme-xrnl' ) . ' </span>%',
            'separator'   => '<span class="screen-reader-text">, </span>',
            ) );
        ?>
    </div>
</article>
