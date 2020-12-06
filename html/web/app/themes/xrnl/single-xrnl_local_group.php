<?php
/**
 * The template for a local group page
 */

get_header(); ?>

<?php
  $localPage = apply_filters('wpml_object_id', 272, 'page', true);
  $localPageURL = get_permalink($localPage);

  function getContext($env) {
    // In development environment, allow reading from the
    // local filesystem without requiring a signed SSL certificate.
    $dev = $env === 'development';
    $contextOptions = array(
      "ssl" => array(
          "verify_peer" => $dev ? false : true,
          "verify_peer_name" => $dev ? false : true,
          "allow_self_signed" => $dev ? true : false
      ),
    );
    return stream_context_create($contextOptions);
  }

  $bg_symbol = file_get_contents(get_template_directory_uri() . '/assets/images/bird.svg', false, getContext(WP_ENV));
  $appearance = get_field('appearance');
  $logo_url = $appearance['logo'] ?: get_template_directory_uri() . '/assets/images/XR-symbol.svg';



  function getSection($section_id) {
    return (object) get_field($section_id);
  }

  function getLocalRoles()
  {
    $vacancies = new WP_Query([
      'post_type' => 'volunteer_vacancy',
      'posts_per_page' => -1
    ]);
    $local_roles = array();
    $n_local_roles = 0;
    $group_name = strtolower('XR ' . get_field('group_name'));

    foreach ($vacancies->posts as $post) {
      $role = json_decode($post->post_content);
      if (strtolower($role->localGroup) === $group_name) {
        $role->id = $post->ID;
        array_push($local_roles, $role);
        $n_local_roles++;
      }
    }
    wp_reset_query();
    return array($n_local_roles, $local_roles);
  }

  function getLocalEvents()
  {
    $events_query = array(
      'posts_per_page' => null,
      'paged' => 1,
      'post_type' => 'meetup_events',
      'orderby' => 'meta_value',
      'meta_key' => 'event_start_date',
      'order' => 'ASC',
      'meta_query' => array(
        array(
          'key' => 'event_start_date',
          'value' => date("Y-m-d"),
          'compare' => '>=',
          'type' => 'DATE'
        ),
        array(
          'key' => 'venue_city',
          'value' => get_field('place'),
          'compare' => '='
        )
      )
    );
    $events = new WP_Query($events_query);
    wp_reset_query();
    return $events;
  }
?>

<script src="/app/themes/xrnl/node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="/app/themes/xrnl/node_modules/owl.carousel/dist/owl.carousel.min.js"></script>
<link rel="stylesheet" href="/app/themes/xrnl/node_modules/owl.carousel/dist/assets/owl.carousel.min.css" />
<link rel="stylesheet" href="/app/themes/xrnl/node_modules/owl.carousel/dist/assets/owl.theme.default.min.css" />

<script type="text/javascript">
  const sections = ['about', 'demands', 'actions', 'channels', 'events', 'positions', 'pictures'];

  function checkSectionExists(section) {
    if (! jQuery('#' + section).length) {
      jQuery('#lg-navigation').find('a[href=\'#' + section + '\']').parent().remove();
    }
  }

  jQuery(document).ready(function(){

    // Remove optional sections from the navigation if they don't exist;
    // If there is only one nav-item left, remove that too.
    sections.forEach(checkSectionExists);
    if (jQuery('#lg-navigation .nav .nav-item').length < 2){
      jQuery('#lg-navigation .nav .nav-item').remove();
    };

    // If the Over Ons section exists, make it active on page load (instead of Contact).
    if (jQuery('#about-nav').length) {
      jQuery('#about-nav').tab('show');
    }

    // Settings for the picture gallery
    jQuery(".owl-carousel").owlCarousel({
      loop:true,
      margin:5,
      nav:true,
      items:1
    });
  });
</script>

<div class="local-group">

  <div class="lg-hero-container px-3 py-5 pb-5 text-center">
    <h1 class="display-2">XR <?php echo preg_replace('/\//', '/ ', get_field('group_name')); ?></h1>
    <div class="bg-symbol left">
      <?php echo $bg_symbol; ?>
    </div>
    <div class="bg-symbol right">
      <?php echo $bg_symbol; ?>
    </div>
    <div class="lg-logo-bg">
    </div>
    <img class="lg-logo" src="<?php echo $logo_url ?>" alt="Extinction Rebellion <?php the_field('group_name'); ?> logo">
  </div>

  <nav id="lg-navigation" role="navigation" aria-label="Local group secondary">
    <ul class="nav nav-pills justify-content-center">
      <li class="nav-item">
        <a class="nav-link" id="about-nav" href="#about" data-toggle="pill"><?php _e('About us', 'theme-xrnl') ?></a>
      </li>
      <li class="nav-item">
        <a class="nav-link active" id="contact-nav" href="#contact" data-toggle="pill"><?php _e('Contact', 'theme-xrnl') ?></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="demands-nav" href="#demands" data-toggle="pill"><?php _e('Demands', 'theme-xrnl') ?></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="actions-nav" href="#actions" data-toggle="pill"><?php _e('Actions', 'theme-xrnl') ?></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="channels-nav" href="#channels" data-toggle="pill"><?php _e('Channels', 'theme-xrnl') ?></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="events-nav" href="#events" data-toggle="pill"><?php _e('Events', 'theme-xrnl') ?></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="postitions-nav" href="#positions" data-toggle="pill"><?php _e('Volunteer', 'theme-xrnl') ?></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="pictures-nav" href="#pictures" data-toggle="pill"><?php _e('Pictures', 'theme-xrnl') ?></a>
      </li>
    </ul>
  </nav>

  <div class="tab-content" id="pills-tabContent">

    <?php $section = getSection('contact'); ?>
      <section id="contact" class="lg-section container-fluid tab-pane fade show active">
        <div class="row">
          <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-6 mx-auto">
            <h1><?php echo($section->heading); ?></h1>
            <p><?php echo($section->message); ?></p>
            <p>
              <?php if($section->email): ?>
                <a href="mailto:<?php echo $section->email; ?>"  target="_blank" rel="noreferrer noopener" class="btn email" aria-label="email"><?php echo $section->email; ?></a>
              <?php endif; ?>
            </p>
            <span class="group-social-icons">
              <?php if($section->twitter): ?>
                <a href="<?php echo $section->twitter; ?>"  target="_blank" rel="noreferrer noopener" class="btn twitter" aria-label="twitter"><i class="fab fa-2x fa-twitter"></i></a>
              <?php endif; ?>

              <?php if($section->mastodon): ?>
                <a href="<?php echo $section->mastodon; ?>"  target="_blank" rel="noreferrer noopener" class="btn mastodon" aria-label="mastodon"><i class="fab fa-2x fa-mastodon"></i></a>
              <?php endif; ?>

              <?php if($section->facebook_page): ?>
                <a href="<?php echo $section->facebook_page; ?>"  target="_blank" rel="noreferrer noopener" class="btn facebook" aria-label="facebook"><i class="fab  fa-2x fa-facebook-f"></i></a>
              <?php endif; ?>

              <?php if($section->instagram_page): ?>
                <a href="<?php echo $section->instagram_page; ?>"  target="_blank" rel="noreferrer noopener" class="btn instagram" aria-label="instagram"><i class="fab fa-2x fa-instagram"></i></a>
              <?php endif; ?>

              <?php if($section->youtube_channel): ?>
                <a href="<?php echo $section->youtube_channel; ?>"  target="_blank" rel="noreferrer noopener" class="btn youtube" aria-label="youtube"><i class="fab fa-2x fa-youtube"></i></a>
              <?php endif; ?>
            </span>
          </div>
        </div>
      </section>

    <?php $section = getSection('about_us'); ?>
    <?php if ($section->enabled) : ?>
      <section id="about" class="lg-section container-fluid tab-pane fade">
        <div class="row">
          <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-6 mx-auto">
            <h1><?php echo($section->heading); ?></h1>
            <p><?php echo($section->content); ?></p>
            <?php if (!empty($section->picture_url)) : ?>
              <img src="<?php echo($section->picture_url); ?>" alt="Extinction Rebellion <?php the_field('group_name'); ?>">
            <?php endif; ?>
          </div>
        </div>
      </section>
    <?php endif; ?>

    <?php $section = getSection('demands'); ?>
    <?php if ($section->enabled) : ?>
      <section id="demands" class="lg-section container-fluid tab-pane fade">
        <div class="row">
          <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-6 mx-auto">
            <h1><?php echo($section->heading); ?></h1>
            <?php if (is_array($section->demands_list)) : ?>
              <div class="demands-list">
                <?php foreach ($section->demands_list as $demand) : ?>
                <div class="demands-item">
                  <h5 class="demands-item-title"><?php echo($demand['title']); ?></h5>
                  <p class="demands-item-desc"><?php echo($demand['description']); ?></p>
                </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </section>
    <?php endif; ?>

    <?php $section = getSection('actions'); ?>
    <?php if ($section->enabled) : ?>
      <section id="actions" class="lg-section container-fluid tab-pane fade">
        <div class="row">
          <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-6 mx-auto">
            <h1><?php echo($section->heading); ?></h1>
            <?php if (is_array($section->actions_list)) : ?>
              <div class="actions-list">
                <?php foreach ($section->actions_list as $action) : ?>
                <div class="action-item">
                  <h5 class="action-item-title"><?php echo($action['title']); ?></h5>
                  <p class="action-item-desc"><?php echo($action['description']); ?></p>
                  <?php if (isset($action['picture_url'])) : ?>
                    <img src="<?php echo($action['picture_url']); ?>" alt="Extinction Rebellion <?php the_field('group_name'); ?>">
                  <?php endif; ?>
                </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </section>
    <?php endif; ?>

    <?php $section = getSection('broadcast_channels'); ?>
    <?php if ($section->enabled) : ?>
      <section id="channels" class="lg-section container-fluid tab-pane fade">
        <div class="row">
          <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-6 mx-auto">
            <h1><?php echo($section->heading); ?></h1>
            <p><?php echo($section->message); ?></p>
            <?php if (is_array($section->channels)) : ?>
              <div class="broadcast-list">
                <?php foreach ($section->channels as $channel) : ?>
                  <a class="btn btn-black my-2" href="<?php echo($channel['url']); ?>"><?php echo($channel['button_label']); ?></a>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </section>
    <?php endif; ?>

    <?php $section = getSection('events'); ?>
    <?php if ($section->enabled) : ?>
      <?php $events = getLocalEvents(); ?>
      <?php if ($events->have_posts()) : ?>
        <section id="events" class="lg-section container-fluid tab-pane fade">
          <div class="row">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-6 mx-auto">
              <h1><?php echo($section->heading); ?></h1>
              <p><?php echo($section->message); ?></p>

              <div class="events-list">
                <?php while ($events->have_posts()) : $events->the_post(); ?>
                  <?php
                    $event_date = get_post_meta(get_the_ID(), 'event_start_date', true );
                    if(!empty($event_date)){
                      $event_date = strtotime($event_date);
                    }
                    $event_address = get_post_meta(get_the_ID(), 'venue_city', true);
                    $venue_address = get_post_meta(get_the_ID(), 'venue_address', true);
                  ?>
                  <div class="events-list-item">
                    <div class="event-details">
                      <div class="event-date">
                        <?php echo date_i18n('M d', $event_date) ; ?>
                      </div>
                      <div class="event-location">
                        <span class="event-place"><?php echo $event_address; ?></span>
                        <span class="event-venue"><?php echo $venue_address; ?></span>
                      </div>
                    </div>
                    <div class="event-description">
                      <a href="<?php echo esc_url(get_permalink()) ?>" class="event-title"><?php the_title(); ?></a>
                      <div class="event-excerpt">
                        <?php echo excerpt(30); ?>
                      </div>
                    </div>
                    <div class="event-btn">
                      <a class="btn btn-black" href="<?php echo esc_url(get_permalink()) ?>"><?php _e('VIEW', 'theme-xrnl') ?></a>
                    </div>
                  </div>
                <?php endwhile; ?>
              </div>

              <?php wp_reset_query(); ?>
            </div>
          </div>
        </section>
      <?php endif; ?>
    <?php endif; ?>

    <?php $section = getSection('positions');?>
    <?php if ($section->enabled) : ?>
      <?php list($n, $roles) = getLocalRoles();?>
      <?php if ($n > 0) : ?>
        <section id="positions" class="lg-section container-fluid tab-pane fade">
          <div class="row">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-6 mx-auto m-1">
              <h1><?php echo $section->heading ?></h1>
              <p><?php echo $section->message ?></p>
                <div class="positions-list">
                  <?php foreach ($roles as $role) : ?>
                    <div class="positions-list-item">
                      <div>
                        <h5><?php echo $role->workingGroup ?></h5>
                        <h4 class="role-title"><?php echo $role->title; ?></h4>
                      </div>
                      <div class="positions-info">
                        <span class="time-commitment">
                          <?php echo $role->timeCommitment->min ?>&ndash;<?php echo $role->timeCommitment->max . ' '; _e('hours / week', 'theme-xrnl'); ?>
                        </span>
                        <a href="<?php echo get_permalink(apply_filters('wpml_object_id', $role->id, 'post', true)) ?>" class="btn btn-black"><?php _e('Learn more', 'theme-xrnl'); ?></a>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
            </div>
          </div>
        </section>
      <?php endif; ?>
    <?php endif; ?>

    <?php $section = getSection('pictures'); ?>
    <?php if ($section->enabled && $section->picture_gallery) : ?>
      <section id="pictures" class="lg-section container-fluid tab-pane fade">
        <div class="row">
          <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-6 mx-auto picture-gallery">
            <?php if (is_array($section->picture_gallery)) : ?>
              <div class="owl-carousel owl-theme">
                <?php foreach ($section->picture_gallery as $picture) : ?>
                  <img src="<?php echo($picture['image_url']); ?>" alt="Extinction Rebellion <?php the_field('group_name'); ?>">
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </section>
    <?php endif; ?>
  </div>

  <a href="<?php echo $localPageURL ?>" class="btn btn-blue my-4"><?php _e('View all local groups', 'theme-xrnl'); ?></a>
</div>

<?php get_footer(); ?>
