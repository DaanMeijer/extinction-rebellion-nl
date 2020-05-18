<?php
/*
Template name: Volunteer positions
*/

get_header(); ?>

<?php the_post(); ?>
<div class="row p-5 m-2 bg-yellow background-icon-container">
  <img src="<?php the_field('background_icon'); ?>" class="background-icon">
  <h1 class="text-white"><?php the_title(); ?></h1>
<div class="col-12 col-xl-8 p-0">
  <?php the_content(); ?>
</div>

</div>

<?php
  $param_working_group = get_query_var('working_group');
  $param_local_group = get_query_var('local_group');

  $vacancies = new WP_Query([
    'post_type' => 'volunteer_vacancy',
    'posts_per_page' => -1
  ]);

  $vacancy_groups = vacancy_groups($vacancies);
?>

<div class="row p-5 m-2 bg-blue">
<form class="form-inline" method="get">
  <label class="mr-2" for="working_group">Working group</label>
  <select name="working_group" class="custom-select my-1" id="working_group">
    <option value=""><?php _e('All') ?></option>
    <option disabled>──────</option>
    <?php foreach($vacancy_groups['working_groups'] as $working_group) { ?>
      <option value="<?php echo $working_group ?>" <?php echo $param_working_group == $working_group ? 'selected="selected"' : '' ?>>
        <?php echo $working_group ?>
      </option>
    <?php } ?>
  </select>
  <label class="mr-2" for="local_group">Local group</label>
  <select name="local_group" class="custom-select my-1" id="local_group">
    <option value=""><?php _e('All') ?></option>
    <option disabled>──────</option>
    <?php foreach($vacancy_groups['local_groups'] as $local_group) { ?>
      <option value="<?php echo $local_group ?>" <?php echo $param_local_group == $local_group ? 'selected="selected"' : '' ?>>
        <?php echo $local_group ?>
      </option>
    <?php } ?>
  </select>
  <button type="submit" class="btn btn-black ml-2">
    <?php _e('Apply') ?>
  </button>
</form>
</div>

<?php
$vacancies = new WP_Query([
  'post_type' => 'volunteer_vacancy',
  'posts_per_page' => -1
]);
?>

<div class="d-flex flex-wrap mt-3">        

<?php 
while($vacancies->have_posts()){
  $vacancies->the_post(); 
  $role = json_decode(get_the_content()); 
  if (
    (
      $param_working_group and ($role->workingGroup != $param_working_group)
    ) or (
      $param_local_group and ($role->localGroup != $param_local_group)
    )
) {continue;}
?>
    <div class="card role-card" style="width: 300px; height: 200px; cursor: pointer;" onclick="window.location='<?php the_permalink(); ?>'">
    <div class="card-body d-flex flex-column">
      <h5 class="card-title font-xr text-uppercase">
        <?php the_title(); ?>
      </h5>
      <h6 class="card-subtitle text-muted mt-1"><?php echo $role->localGroup ?></h6>
      <h6 class="card-subtitle text-muted mt-1"><?php echo $role->workingGroup ?></h6>
      <h6 class="card-subtitle text-muted mt-1">
        <?php echo $role->timeCommitment->min ?> - <?php echo $role->timeCommitment->max ?> 
        <?php _e('hours / week', 'theme-xrnl'); ?>
      </h6>
    </div>
    <a href="<?php the_permalink(); ?>" class="btn btn-blue"><?php _e('Learn more', 'theme-xrnl'); ?></a>
    </div>
  <?php } wp_reset_query(); ?>
</div>
</div>
</div>

<?php get_footer();
