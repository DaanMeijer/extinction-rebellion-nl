<?php

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

<div class="home">
  <div class="masthead bg-blue px-3 py-lg-5 pb-5 text-center text-white cover-image" style="background: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.45)), url('<?php the_field('cover_image'); ?>') no-repeat;">
    <div class="py-5">
      <div class="container">
        <?php the_content(); ?>
      </div>
    </div>
  </div>

  <div class="my-sm-5 my-4">
    <div class="container py-5 text-center">
      <div class="row">
        <div class="col-lg-8 mx-auto">
          <?php the_field('about'); ?>
        </div>
      </div>
    </div>
  </div>

  <div class="py-sm-5 py-4 text-white cover-image" style="background: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.55)), url('<?php the_field('join_cover_image'); ?>') no-repeat; background-position: 50% 0%;">
    <div class="container">
      <div class="row py-5 text-center">
        <div class="col-12 col-lg-8 mx-auto">
          <?php the_field('join_description'); ?>
        </div>
      </div>
    </div>
  </div>

  <div class="text-center">
		<?php
			$image = get_field('image');
			$image_mobile = get_field('image_mobile');
		 ?>
	 <img class="image-desktop img-fluid my-2" src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
	 <img class="image-mobile my-2" src="<?php echo esc_url($image_mobile['url']); ?>" alt="<?php echo esc_attr($image_mobile['alt']); ?>">
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

  <div class="py-5 my-5">
    <div class="container">
      <div class="row text-center">
        <div class="col-lg-8 mx-auto">
          <?php the_field('donate') ?>
        </div>
      </div>
    </div>
  </div>

  <div class="embed-responsive embed-responsive-21by9">
    <?php the_field('video') ?>
  </div>
</div>

<?php get_footer(); ?>
