<?php
/*
Template name: Volunteer vacancy index
*/

get_header(); ?>

<div class="container">
  <div class="row mt-5">
    <div class="col-12">
      <?php the_post(); ?>
      <h1><?php the_title(); ?></h1>
      <?php the_content(); ?>

      <?php
        $vacancies = new WP_Query([
          'post_type' => 'volunteer_vacancy',
          'posts_per_page' => -1
        ]);
        ?>
    </div>
  </div>
        <div class="mt-5 roles-grid">
          <?php while($vacancies->have_posts()){
            $vacancies->the_post(); ?>
            <div>
              <a class="font-xr text-uppercase text-decoration-none" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
              <?php $role = json_decode(get_the_content()); ?>
              <p><?php echo $role->title ?></p>
            </div>
          <?php } wp_reset_query(); ?>
        </div>

    </div>
  </div>

  <div class="row my-4">
    <div class="col-12 col-lg-8">
      <?php the_field('how_to_volunteer') ?>
    </div>
  </div>

</div>

<?php get_footer();
