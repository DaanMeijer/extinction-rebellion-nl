<?php
/*
* Template name: Community groups
*/

get_header(); ?>

<?php
  $groups = new WP_Query([
    'order' => 'DESC',
    'orderby' => 'ID',
    'post_type' => 'community_group',
    'posts_per_page' => -1
  ]);
  $default_img_url = 'https://extinctionrebellion.nl/app/uploads/2020/04/community-group.png';
?>

<div id="community" class="container py-5">
    <h1 class="mt-5"><?php the_title(); ?></h1>
    <p><?php the_content(); ?></p>

    <div class="row">
      <?php while($groups->have_posts()):$groups->the_post(); ?>
        <?php $show_link_to_group_page = !empty(get_the_content()); // Show link if group has content text ?>

        <div class="col-8 col-sm-6 col-md-4 col-xl-4 mt-4 group-column">
          <div id="group-card-<?php echo($groups->current_post); ?>" class="card group-card h-100 border-0">

            <?php $img_url = get_field('group_cover_image_url') ?: $default_img_url;  // Use default pic if cover image is missing ?>
            <img src="<?php echo($img_url); ?>" class="card-img-top" alt="XRNL Community group">

             <?php if($show_link_to_group_page): ?>
               <a href="<?php the_permalink(); ?>"><h5 class="card-header font-xr text-uppercase bg-yellow border-0"><?php the_title(); ?></h5></a>
             <?php else :?>
              <h5 class="card-header font-xr text-uppercase bg-yellow border-0"><?php the_title(); ?></h5>
             <?php endif; ?>

              <div class="card-body bg-light text-center">
               <?php if($show_link_to_group_page): ?>
                 <a href="<?php the_permalink(); ?> "class="btn btn-black"><?php _e('Learn more', 'theme-xrnl'); ?></a>
               <?php endif; ?>
              </div>

              <div class="card-footer bg-light border-0 text-center">
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
