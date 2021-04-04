<?php
/*
* Template name: Community groups
*/

get_header(); ?>

<?php
  $groups = new WP_Query([
    'order' => 'ASC',
    'orderby' => 'ID',
    'post_type' => 'community_group',
    'posts_per_page' => -1
  ]);
  $default_img_url = 'https://extinctionrebellion.nl/app/uploads/2020/04/community-group.png';
?>

<div id="community" class="container py-5">
    <div class="row text-center">
      <div class="col col-sm-11 col-md-10 col-lg-8 mx-auto">
        <h1 class="display-3"><?php the_title(); ?></h1>
        <p><?php the_content(); ?></p>
      </div>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3">
      <?php while($groups->have_posts()):$groups->the_post(); ?>
      <div class="col mb-4 mt-4">

          <div id="group-card-<?php echo($groups->current_post); ?>" class="card group-card border-0">

            <?php $img_url = get_field('group_cover_image_url') ?: $default_img_url;  // Use default pic if cover image is missing ?>
            <a href="<?php the_permalink(); ?>">
              <img src="<?php echo($img_url); ?>" class="card-img-top" alt="<?php the_title(); ?>">
            </a>

               <a href="<?php the_permalink(); ?>"><h5 class="card-header text-center font-xr text-uppercase bg-yellow border-0"><?php the_title(); ?></h5></a>
               <div class="card-footer bg-xr-yellow border-0 text-center">
               <?php if(get_field('group_twitter_url')): ?>
                 <a href="<?php the_field('group_twitter_url'); ?>"  target="_blank" rel="noreferrer noopener" class="btn twitter" aria-label="twitter"><i class="fab fa-2x fa-twitter"></i></a>
               <?php endif; ?>

               <?php if(get_field('group_facebook_url')): ?>
                 <a href="<?php the_field('group_facebook_url'); ?>"  target="_blank" rel="noreferrer noopener" class="btn facebook" aria-label="facebook"><i class="fab fa-2x fa-facebook-f"></i></a>
               <?php endif; ?>

               <?php if(get_field('group_instagram_url')): ?>
                 <a href="<?php the_field('group_instagram_url'); ?>"  target="_blank" rel="noreferrer noopener" class="btn instagram" aria-label="instagram"><i class="fab fa-2x fa-instagram"></i></a>
               <?php endif; ?>

               <?php if(get_field('group_youtube_url')): ?>
                 <a href="<?php the_field('group_youtube_url'); ?>"  target="_blank" rel="noreferrer noopener" class="btn youtube" aria-label="youtube"><i class="fab fa-2x fa-youtube"></i></a>
               <?php endif; ?>

               <?php if(get_field('group_contact_email')): ?>
                 <a href="mailto:<?php the_field('group_contact_email'); ?>" class="btn email" aria-label="email"><i class="fas fa-2x fa-envelope"></i></a>
               <?php endif; ?>
              </div>

          </div>
      </div>
      <?php endwhile; wp_reset_query(); ?>
    </div>

</div>

<?php get_footer();
