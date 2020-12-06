<?php
/**
 * Template name: Doe mee
 */


get_header(); ?>

<?php function getSection($section_id) {
  return (object) get_field($section_id);
} ?>

<?php function insertURL($page_id) {
  echo get_permalink(apply_filters('wpml_object_id', $page_id, 'page', true));
} ?>

<div class="join">
  <div class="bg-blue text-center text-white join-cover-image py-5" style="background: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.45)), url('<?php the_field('join_cover_image_url'); ?>') no-repeat;">
      <h1 class="display-2 text-uppercase font-xr"><?php the_title(); ?></h1>
      <div class="container">
        <div class="col-lg-8 mx-auto">
          <?php the_content(); ?>
        </div>
      </div>
  </div>

  <?php $section = getSection('signup_section'); ?>
  <?php if ($section->enabled) : ?>
    <section class="join-section container-fluid bg-pink">
      <div class="row">
        <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-6 mx-auto">
          <h2><?php echo($section->heading); ?></h2>
          <?php echo($section->content); ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <?php $section = getSection('actions_section'); ?>
  <?php if ($section->enabled) : ?>
    <section class="join-section bg-blue text-white join-cover-image" style="background: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.45)), url('<?php echo($section->cover_image); ?>') no-repeat;">
      <div class="row">
        <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-6 mx-auto">
          <h2><?php echo($section->heading); ?></h2>
          <p><?php echo($section->description); ?></p>
          <div class="join-action-btns">
            <?php if (is_array($section->broadcasts)) : ?>
              <?php foreach ($section->broadcasts as $broadcast) : ?>
                <a class="btn btn-pink my-2" href="<?php echo($broadcast['link']); ?>"><?php echo($broadcast['label']); ?></a>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <?php $section = getSection('groups_section'); ?>
  <?php if ($section->enabled) : ?>
    <section class="join-section bg-white">
      <div class="row">
        <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-6 mx-auto">
          <h2><?php echo($section->heading); ?></h2>
          <p><?php echo($section->description); ?></p>
          <div class="join-action-btns">
            <?php if(is_array($section->groups)) : ?>
              <?php foreach ($section->groups as $group) : ?>
                <a class="btn btn-yellow my-2" href="<?php echo($group['button_link']); ?>"><?php echo($group['button_label']); ?></a>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <?php $section = getSection('resources_section'); ?>
  <?php if ($section->enabled) : ?>
    <section class="join-section container-fluid bg-yellow">
      <div class="row">
        <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-6 mx-auto">
          <h2><?php echo($section->heading); ?></h2>
          <p><?php echo($section->description); ?></p>
          <?php if(!empty($section->button_label)) : ?>
            <a class="btn btn-black" href="<?php insertURL(7221) ?>"><?php echo($section->button_label); ?></a>
          <?php endif; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <?php $section = getSection('events_section'); ?>
  <?php if ($section->enabled) : ?>
    <section class="join-section bg-blue text-white join-cover-image" style="background: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.45)), url('<?php echo($section->cover_image); ?>') no-repeat;">
      <div class="row">
        <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-6 mx-auto">
          <h2><?php echo($section->heading); ?></h2>
          <p><?php echo($section->description); ?></p>
          <?php if(!empty($section->button_label)) : ?>
            <a class="btn btn-blue" href="<?php insertURL(548) ?>"><?php echo($section->button_label); ?></a>
          <?php endif; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <?php $section = getSection('do_more_section'); ?>
  <?php if ($section->enabled) : ?>
    <section class="join-section container-fluid bg-blue">
      <div class="row">
        <div class="col-12 col-sm-10 col-md-9 col-lg-8 col-xl-8 mx-auto">
          <h2><?php echo($section->heading); ?></h2>
          <p><?php echo($section->description); ?></p>
          <div class="row do-more-actions">
            <?php if(is_array($section->actions)) : ?>
              <?php foreach ($section->actions as $action) : ?>
                <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-6 mx-auto mb-5">
                  <h3><?php echo($action['title']) ?></h3>
                  <p><?php echo($action['description']) ?></p>
                  <?php if(is_array($action['buttons'])) : ?>
                    <?php foreach ($action['buttons'] as $button) : ?>
                      <?php if ($button['button_label']): ?>
                        <a class="btn btn-black my-2 mx-2" href="<?php echo($button['button_link']); ?>" target="_blank"><?php echo($button['button_label']); ?></a>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </section>
  <?php endif; ?>
</div>

<?php get_footer(); ?>
