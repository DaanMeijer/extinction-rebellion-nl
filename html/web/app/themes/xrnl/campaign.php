<?php
/**
 * Template name: Campaign
 */

get_header(); ?>

<div class="rwb">
  <div class="bg-blue px-3 py-lg-5 pb-5 text-center text-white rwb-cover-image" style="background: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.45)), url('<?php the_field('campaign_cover_image_url'); ?>') no-repeat;">
    <div class="py-5">
      <h1 class="display-2"><?php the_title(); ?></h1>
      <?php if(get_field('campaign_caption')): ?>
        <h1 class="font-xr text-uppercase">
          <?php the_field('campaign_caption'); ?>
        </h1>
      <?php endif; ?>
    </div>

    <?php if(get_field('campaign_date')): ?>
      <h1 class="font-xr text-uppercase"><?php the_field('campaign_date'); ?></h1>
    <?php endif; ?>
    <?php if(get_field('campaign_location')): ?>
      <div class="rwb-location">
        <?php the_field('campaign_location'); ?>
      </div>
    <?php endif; ?>

    <?php if(get_field('campaign_cta_title') && get_field('campaign_cta_url')): ?>
      <div class="my-3">
        <a class="btn btn-lg btn-blue" href="<?php the_field('campaign_cta_url'); ?>" target="_blank">
          <?php the_field('campaign_cta_title'); ?>
        </a>
      </div>
    <?php elseif (get_field('campaign_cta_title')): ?>
      <div class="my-3">
        <a class="btn btn-lg btn-blue" href="#details">
          <?php the_field('campaign_cta_title'); ?>
        </a>
      </div>
    <?php endif; ?>

    <?php if(get_field('campaign_facebook_event_url')): ?>
      <a href="<?php the_field('campaign_facebook_event_url'); ?>" target="_blank" class="text-white text-reset text-underline">Facebook</a>
      &nbsp;
    <?php endif; ?>
    <?php if(get_field('campaign_facebook_event_url') && get_field('campaign_meetup_event_url')): ?>
      <?php _e('or', 'theme-xrnl') ?>
    <?php endif; ?>
    <?php if(get_field('campaign_meetup_event_url')): ?>
      &nbsp;
      <a class="text-white text-reset text-underline" href="<?php the_field('campaign_meetup_event_url'); ?>" target="_blank">Meetup</a>
    <?php endif; ?>
  </div>

  <div class="text-center my-sm-5 my-4">
    <a name="details"></a>
    <div class="container">
      <div class="row py-5">
        <div class="col-12 col-lg-8 mx-auto">
          <a name="details"></a>
          <?php the_content(); ?>
        </div>
      </div>
    </div>
  </div>

  <?php if(get_field('campaign_join_title') && get_field('campaign_join_description') && get_field('sign_up_form_code')): ?>
  <div class="bg-blue py-sm-5 py-4">
    <a name="join"></a>
    <div class="container">
      <div class="row py-5 text-center">
        <div class="col-12 col-lg-8 mx-auto">
          <h2><?php the_field('campaign_join_title'); ?></h2>
          <?php the_field('campaign_join_description'); ?>
        </div>
        <div class="col-12 col-lg-6 col-md-8 mx-auto mt-4">
          <?php the_field('sign_up_form_code'); ?>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <?php if(get_field('campaign_why_protest') && get_field('campaign_demands')): ?>
  <div class="container text-center py-sm-5 py-4">
    <div class="row py-5">
      <?php if(get_field('campaign_why_protest')): ?>
        <div class="col-12 col-lg-8 mx-auto">
          <h2>
            <?php _e('WHY ARE WE REBELLING', 'theme-xrnl'); ?>?
          </h2>
          <p><?php the_field('campaign_why_protest'); ?></p>
        </div>
      <?php endif; ?>
      <?php if(get_field('campaign_demands')): ?>
        <div class="col-12 col-lg-6 mx-auto mt-3">
          <a class="btn btn-yellow btn-lg" data-toggle="collapse" href="#demands" role="button" aria-expanded="false" aria-controls="demands">
            <?php _e('OUR DEMANDS', 'theme-xrnl'); ?>
            <i class="fas fa-chevron-down"></i>
          </a>
          <div class="text-left collapse" id="demands">
            <?php the_field('campaign_demands'); ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
  <?php endif; ?>

  <?php if(get_field('campaign_who_is_xr')): ?>
  <div class="rwb-cover-image" style="background: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.55)), url('<?php the_field('campaign_why_protest_background_url'); ?>') no-repeat; min-height: 45vh;">
    <div class="container">
      <div class="row py-5 text-center text-white">
        <div class="col-12 col-lg-8 mx-auto my-5">
          <h2>
            <?php _e('WHO IS EXTINCTION REBELLION', 'theme-xrnl'); ?>?
          </h2>
          <?php the_field('campaign_who_is_xr'); ?>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <?php if(have_rows('campaign_faqs')): ?>
  <div class="container-fluid my-sm-5 my-4">
    <a name="faq"></a>
    <div class="row py-5 text-center">
      <div class="col-12 col-lg-8 mx-auto">
        <h2><?php _e('FAQ AND PRACTICAL INFO', 'theme-xrnl'); ?></h2>
        <?php the_field('campaign_faq_description') ?>
        <div class="row my-3">
          <div class="text-left col-lg-10 col-xl-8 mx-auto">
            <?php while ( have_rows('campaign_faqs') ){ the_row(); ?>
              <div class="my-3 border border-yellow">
                <a class="btn btn-yellow btn-lg btn-block d-flex justify-content-between align-content-center text-left" data-toggle="collapse" href="#faq-<?php echo get_row_index(); ?>" role="button" aria-expanded="false" aria-controls="faq-<?php echo get_row_index(); ?>">
                  <span><?php the_sub_field('question'); ?></span>
                  <i class="fas fa-chevron-down pt-1"></i>
                </a>
                <div class="collapse" id="faq-<?php echo get_row_index(); ?>">
                  <div class="p-4"><?php the_sub_field('answer'); ?></div>
                </div>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <?php if(have_rows('campaign_do_more')): ?>
  <div class="container-fluid text-center bg-blue py-sm-5 mt-5 py-4 mt-4">
    <div class="row pt-5">
      <div class="col-12 col-lg-8 mx-auto">
        <h2><?php _e('WANT TO DO MORE?', 'theme-xrnl'); ?></h2>
        <?php the_field('campaign_do_more_description'); ?>
      </div>
    </div>

    <div class="row text-center pb-5">
      <?php while ( have_rows('campaign_do_more') ){ the_row(); ?>
        <div class="col-12 col-lg-4 pt-5">
          <h3 class="font-xr text-uppercase">
            <?php _e(the_sub_field('title'), 'theme-xrnl'); ?>
          </h3>
          <div class="mt-3 px-3">
            <?php the_sub_field('description'); ?>
          </div>
        </div>
      <?php } ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php get_footer(); ?>
