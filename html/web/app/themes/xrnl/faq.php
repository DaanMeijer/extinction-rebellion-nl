<?php
/**
 * Template name: FAQ
 */

get_header(); ?>

<div class="my-sm-5 my-4">
  <div class="container">
    <h1><?php the_title(); ?></h1>
    <?php the_content(); ?>

    <?php while ( have_rows('sections') ){ the_row(); ?>
      <section class="my-5">
        <h2><?php the_sub_field('name') ?></h2>
        <?php $index = get_row_index(); ?>
        <?php while ( have_rows('faqs') ){ the_row(); ?>
          <div class="py-3">
            <a class="btn btn-yellow btn-lg btn-block d-flex justify-content-between align-content-center text-left" data-toggle="collapse" href="#faq-<?php echo $index ."-". get_row_index(); ?>" role="button" aria-expanded="false" aria-controls="faq-<?php echo $index ."-". get_row_index(); ?>">
              <span><?php the_sub_field('question'); ?></span>
              <i class="fas fa-chevron-down pt-1"></i>
            </a>
            <div class="collapse border border-yellow" id="faq-<?php echo $index ."-". get_row_index(); ?>">
              <div class="p-4"><?php the_sub_field('answer'); ?></div>
            </div>
          </div>
        <?php } ?>
      </section>
    <?php } ?>
  </div>
</div>

<?php get_footer(); ?>
