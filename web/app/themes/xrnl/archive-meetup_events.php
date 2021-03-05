<?php

/**
 * Template name: Events
 */

$param_organizer = stripslashes(get_query_var('organizer'));
$param_category = stripslashes(get_query_var('category'));

// organizer query
$query_organizer = $param_organizer ? array(
	'key' => 'organizer_name',
	'value' => $param_organizer,
	'compare' => '=',
	'sentence' => true
) : array();

// page query param
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

// Events Query
$args = array(
	'posts_per_page' => 10,
	'paged' => $paged,
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
		$query_organizer
	)
);
// push the taxonomy search if the category parameter is found:
if ($param_category) {
	// $query_category =
	$args['tax_query'] = array(
		array(
			'taxonomy' => 'meetup_category',
			'field' => 'name',
			'terms' => $param_category
		)
	);
}


$events = new WP_Query($args);

$param_values = event_params();

$events_page_id = (apply_filters('wpml_current_language', NULL) === 'nl') ? 548 : 567;
$featured_event = get_field('featured_event', $events_page_id);

get_header(); ?>

<div class="container my-5">

	<?php if ($featured_event['show_featured_event'] === 'yes') : ?>
		<div class="pb-5 mb-5">
			<div class="row">
				<div class="col-12 col-lg-5 mb-5 mb-lg-0">
					<h2><?php echo($featured_event['title']); ?></h2>
					<h5 class="font-xr"><?php echo($featured_event['subtitle']); ?></h5>
					<p class="pr-5">
						<?php echo($featured_event['description']); ?>
					</p>
					<div class="row px-3 mt-5">
						<a href="<?php echo($featured_event['button_link']); ?>" class="btn btn-lg bg-xr-light-green"><?php echo($featured_event['button_label']); ?></a>
					</div>
				</div>
				<div class="col-12 col-lg-7">
					<img src="<?php echo($featured_event['picture']['url']); ?>" alt="<?php echo($featured_event['picture']['alt'] ?? 'Extinction Rebellion Nederland' ); ?>">
				</div>
			</div>
		</div>
	<?php endif; ?>

	<h1 class="page-title"><?php _e('EVENTS'); ?> <?php echo $param_organizer; ?></h1>
	<p><?php the_field('intro_text', $events_page_id); ?></p>

	<?php if ($param_values['organizers'] || $param_values['categories']) { ?>
		<form class="mt-4 flex-nowrap" method="get">
			<div class="form-row">
				<input type="hidden" name="paged" value="1" />
				<?php if ($param_values['organizers']) { ?>
					<label class="my-1 mr-sm-2" for="organizer"><?php _e('Organizer') ?></label>
					<div class="col-sm-3">
						<select name="organizer" class="custom-select my-1 form-control" id="organizer">
							<option value=""><?php _e('All') ?></option>
							<option disabled>──────</option>
							<?php foreach ($param_values['organizers'] as $organizer => $count) { ?>
								<option value="<?php echo $organizer ?>" <?php echo $param_organizer == $organizer ? 'selected="selected"' : '' ?>>
									<?php echo $organizer . ' (' . $count . ')' ?>
								</option>
							<?php } ?>
						</select>
					</div>
				<?php } ?>
				<?php if ($param_values['categories']) { ?>
					<label class="my-1 mx-sm-2" for="category"><?php _e('Category') ?></label>
					<div class="col-sm-3">
						<select name="category" class="custom-select my-1 form-control" id="category">
							<option value=""><?php _e('All') ?></option>
							<option disabled>──────</option>
							<?php foreach ($param_values['categories'] as $category => $count) { ?>
								<option value="<?php echo $category ?>" <?php echo $param_category == $category ? 'selected="selected"' : '' ?>>
									<?php echo $category . ' (' . $count . ')' ?>
								</option>
							<?php } ?>
						</select>
					</div>
				<?php } ?>
				<div class="col-auto my-auto">
					<button type="submit" class="btn btn-black ml-sm-2">
						<?php _e('Apply') ?>
					</button>
				</div>
			</div>
		</form>
	<?php } ?>

	<div class="mt-4">
		<?php if ($events->have_posts()) : ?>
			<div class="row">
				<?php while ($events->have_posts()) : $events->the_post(); ?>
					<?php
					$event_date = get_post_meta(get_the_ID(), 'event_start_date', true);
					if ($event_date != '') {
						$event_date = strtotime($event_date);
					}
					$event_address = get_post_meta(get_the_ID(), 'venue_city', true);
					$venue_address = get_post_meta(get_the_ID(), 'venue_address', true);
					if ($event_address != '' && $venue_address != '') {
						$event_address .= ' - ' . $venue_address;
					} elseif ($venue_address != '') {
						$event_address = $venue_address;
					}

					$term_obj_list = get_the_terms(get_the_ID(), 'meetup_category'); //get the categories
					if ($term_obj_list) {
						$event_categories = join(', ', wp_list_pluck($term_obj_list, 'name')); //turn the categories into a nice string
					} else {
						$event_categories = '';
					}

					$image_url =  array();
					if ('' !== get_the_post_thumbnail()) {
						$image_url =  wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'full');
					} else {
						$image_date = date_i18n('F+d', $event_date);
						$image_url[] =  "http://placehold.it/420x150?text=" . $image_date;
					}
					?>
					<div class="col-md-6 my-3">
						<a href="<?php echo esc_url(get_permalink()) ?>">
							<div class="<?php echo $css_class ?? ''; ?> archive-event">
								<div class="ime_event">
									<div class="img_placeholder" style=" background: url('<?php echo $image_url[0]; ?>') no-repeat left top;"></div>
									<div class="event_details">
										<div class="event_date">
											<span class="month"><?php echo date_i18n('M', $event_date); ?></span>
											<span class="date"> <?php echo date_i18n('d', $event_date); ?> </span>
										</div>
										<div class="event_desc">
											<a href="<?php echo esc_url(get_permalink()) ?>" rel="bookmark">
												<?php the_title('<div class="event_title">', '</div>'); ?>
											</a>
											<?php if ($event_address || $event_categories) { ?>
												<div class="event_info">
													<?php if ($event_categories) { ?>
														<?php echo $event_categories; ?> -
													<?php }	?>
													<?php if ($event_address) { ?>
														<i class="fa fa-map-marker"></i> <?php echo $event_address; ?>
													<?php }	?>
												</div>
											<?php } ?>
										</div>
										<div style="clear: both"></div>
									</div>
								</div>
							</div>
						</a>
					</div>
				<?php endwhile; ?>
			</div>
			<nav class="pages my-5">
				<?php
				// Pagination
				$big = 999999999;
				$pagination = paginate_links(array(
					'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
					'current' => max(1, $paged),
					'total' => $events->max_num_pages,
					'type' => 'array',
					'prev_text' => '&laquo; ' . translate('Previous'),
					'next_text' => translate('Next') . ' &raquo;',
				));
				?>
				<?php if (!empty($pagination)) : ?>
					<ul class="pagination justify-content-center">
						<?php foreach ($pagination as $key => $page_link) : ?>
							<li class="page-item <?php if (strpos($page_link, 'current') !== false) {
														echo ' active';
													} ?>">
								<?php echo $page_link ?>
							</li>
						<?php endforeach ?>
					</ul>
				<?php endif ?>
			</nav>
		<?php else : ?>
			<div class="text-center mt-5 pt-5">
				<p>
					<?php _e('Looks like there are no events, try clearing the filters.', 'theme-xrnl'); ?>
				</p>
				<a class="btn btn-black" href="<?php the_permalink(apply_filters('wpml_object_id', 548, 'page', true)) ?>"><?php _e('Clear filters', 'theme-xrnl'); ?></a>
			</div>
		<?php endif; ?>
	</div>
</div>

<?php get_footer(); ?>
