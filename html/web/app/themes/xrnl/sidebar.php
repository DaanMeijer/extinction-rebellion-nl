<?php if ( is_active_sidebar( 'blog_sidebar' )  ) : ?>
	<aside class="sidebar widget-area" role="complementary">
		<ul class="list-unstyled mb-5">
            <?php dynamic_sidebar( 'blog_sidebar' ); ?>
        </ul>
	</aside>
<?php endif; ?>
