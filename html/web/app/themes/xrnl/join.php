<?php
/**
 * Template name: Join
 */

// city query
$query_city = array();

// Events Query
$args = array(
	'posts_per_page' => 5,
	'paged' => 1,
	'post_type' => 'meetup_events',
	'orderby' => 'meta_value',
	'meta_key' => 'event_start_date',
	'order' => 'ASC',
	'meta_query' => array(
		array(
			'key' => 'event_start_date', // Check the start date field
			'value' => date("Y-m-d"), // Set today's date (note the similar format)
			'compare' => '>=', // Return the ones greater than today's date
			'type' => 'DATE' // Let WordPress know we're working with date
    ),
    // adds city filter
    $query_city
	)
);
$events = new WP_Query( $args );

get_header(); ?>

<div class="join">
  <div class="bg-blue text-center text-white join-cover-image" style="background: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.45)), url('<?php the_field('join_cover_image_url'); ?>') no-repeat;">
    <div class="py-5">
      <h1 class="display-2 text-uppercase font-xr"><?php the_title(); ?></h1>
      <div class="container">
        <div class="col-lg-8 mx-auto">
          <?php the_content(); ?>
        </div>
      </div>
    </div>
  </div>

  <div class="my-sm-5 my-4">
    <div class="container py-5 text-center">
      <h1 class="text-uppercase font-xr"><?php the_field('welcome_title'); ?></h1>
      <div class="row">
        <div class="col-12 col-lg-8 mx-auto">
          <?php the_field('welcome_description'); ?>
        </div>
      </div>
    </div>
  </div>

  <div class="container-fluid text-center py-sm-5 py-4 bg-pink">
    <div class="row py-5">
      <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5 mx-auto mt-4">
        <?php the_field('signup_form'); ?>
      </div>
    </div>
  </div>

  <?php if ( $events->have_posts() ) : ?>
    <div class="py-sm-5 py-4 bg-yellow">
      <div class="container my-5">
        <h1 class="text-center"><?php _e('EVENTS') ?></h1>
        <?php while ( $events->have_posts() ) : $events->the_post(); ?>
          <div class="row border-bottom border-black pt-3 pb-3">
            <?php
              $event_date = get_post_meta( get_the_ID(), 'event_start_date', true );
              if( $event_date != '' ){
                $event_date = strtotime( $event_date );
              }
              $event_address = get_post_meta( get_the_ID(), 'venue_city', true );
              $venue_address = get_post_meta( get_the_ID(), 'venue_address', true );
            ?>
            <div class="col-lg-2 col-sm-12">
              <div class="text-uppercase font-xr small pt-1">
                <?php echo date_i18n('M', $event_date) ; ?>
                <?php echo date_i18n('d', $event_date) ; ?>
              </div>
              <small>
                <strong><em><?php echo $event_address; ?></em></strong>
                <br>
                <em><?php echo $venue_address; ?></em>
              </small>
            </div>
            <div class="col-lg-8 col-sm-12">
              <a href="<?php echo esc_url( get_permalink() ) ?>" class="text-reset text-uppercase font-xr text-decoration-none small">
                <?php the_title(); ?>
              </a>
              <div class="small pt-2"><em><?php echo excerpt(30); ?></em></div>
            </div>
            <div class="col-lg-2 col-sm-12 text-lg-right">
              <a class="btn btn-black" href="<?php echo esc_url( get_permalink() ) ?>">
                <?php _e('VIEW') ?>
              </a>
            </div>
          </div>
        <?php endwhile; ?>
        <?php wp_reset_query(); ?>
        <br>
        <div class="text-center">
          <a class="btn btn-lg btn-black" href="/events">
            <?php _e('VIEW ALL EVENTS') ?>
          </a>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <div class="pt-5 mt-5">
    <div class="container">
      <div class="row text-center">
        <div class="col-12 col-lg-8 mx-auto">
          <h1>
            <?php _e('LOCAL GROUPS', 'theme-xrnl'); ?>
          </h1>
          <?php the_field('local_groups_description') ?>
        </div>
      </div>
    </div>
    <div class="mt-5"><?php the_field('local_map'); ?></div>
  </div>


  <div class="container-fluid text-center bg-blue py-sm-5 py-4">
    <div class="row pt-5">
      <div class="col-12 col-lg-8 mx-auto">
        <h1><?php _e('WANT TO DO MORE?', 'theme-xrnl'); ?></h1>
        <?php the_field('do_more_description'); ?>
      </div>
    </div>

    <div class="row text-center pb-5">
      <?php while ( have_rows('do_more') ){ the_row(); ?>
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
</div>

<?php get_footer(); ?>
