<?php
/**
 * Template name: Petition
 */


get_header(); ?>

<?php function getSection($section_id) {
  return (object) get_field($section_id);
} ?>

<?php function insertURL($page_id) {
  echo get_permalink(apply_filters('wpml_object_id', $page_id, 'page', true));
} ?>

<div class="petition">
  <div class="bg-blue text-center text-white petition-cover-image py-5"
       style="background: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.45)), url('<?php the_field('join_cover_image_url'); ?>') no-repeat center center / cover;">
      <h1 class="display-2 text-uppercase font-xr"><?php the_title(); ?></h1>
      <div class="container">
        <div class="col-lg-8 mx-auto">
          <?php the_content(); ?>
            <!--     On click, scroll to the progress bar section. We can later make it smoothly with a JS library/plugin       -->
            <a class="btn btn-yellow my-2 btn-lg" href="#sign"><?php _e('SIGN THE PETITION', 'theme-xrnl'); ?></a>
        </div>
      </div>
  </div>


    <?php $section = getSection('about_section'); ?>
    <?php if ($section->enabled) : ?>
        <section class="campaign-section container-fluid text-center">
            <div class="row">
                <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-6 mx-auto px-4">
                    <h2><?php echo($section->heading); ?></h2>
                    <?php echo($section->content); ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php
        function roundUpToNearest($n, $x=500) {
            return ceil( $n / $x ) * $x;
        }

        $actionnetwork_api_key = get_field('actionnetwork_api_key', 'option');

        $response_en = wp_remote_get(get_field('api_endpoint_english_form'), [
            'headers' => [
                'OSDI-API-Token'=> $actionnetwork_api_key
            ]
        ]);

        $data_en = json_decode($response_en['body'], true);
        $total_submissions_en = $data_en['total_submissions'];

        $response_nl = wp_remote_get(get_field('api_endpoint_dutch_form'), [
            'headers' => [
                'OSDI-API-Token'=> $actionnetwork_api_key
            ]
        ]);

        $data_nl = json_decode($response_nl['body'], true);
        $total_submissions_nl = $data_nl['total_submissions'];

        $total_submissions = $total_submissions_en + $total_submissions_nl;
        $max_submissions = roundUpToNearest($total_submissions, 500);
        $percent = $total_submissions/$max_submissions*100;
    ?>

    <section class="progress-section container-fluid bg-yellow py-sm-5 py-4 px-3"  id="sign">
        <a name="sign"></a>
        <div class="row py-5">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-6 mx-auto px-2">
                <h2 class="text-uppercase font-xr">
                    <span class="display-3" id="total-submissions"><?= $total_submissions ?></span> <?php _e('of', 'theme-xrnl'); ?> <span id="max-submissions"><?= $max_submissions ?></span> <?php _e('signatures', 'theme-xrnl'); ?>
                </h2>

                <div class="progress" style="height: 20px;border-radius: 5px; margin-bottom: 20px;">
                    <div class="progress-bar" role="progressbar" style="width: <?= $percent ?>%; background: black" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                </div>

                <?= do_shortcode(get_field('actionnetwork_shortcode')) ?>
                <? 
                /* #submission-cta-1 is shown after the form is submitted. */
                /* #submission-cta-2 is shown after the user clicks on a button ins #submission-cta-1 */
                ?>

                <?php $section = getSection('petition_details'); ?>
                <p class="text-center font-italic" id="form-subtext">
                <?php echo($section->subtext); ?>
                </p>
                <div id="submission-cta-1" class="isInvisible">
                  <div class="text-center">
                    <h1><?php echo($section->donate_title); ?></h1>
                    <p>
                      <?php echo($section->donate_description); ?>
                    </p>
                    <a href="<?php echo insertURL(308) ?>" class="btn btn-black btn-lg button-block-centered" target="_blank" id="form-donate"><?php _e('donate', 'theme-xrnl'); ?></a>
                    <a class="btn my-2 btn-lg" id="form-no-donate" href="#form-share">
                        <?= _e('NO THANKS', 'theme-xrnl'); ?>
                    </a>
                  </div>
                </div>
                <div id="submission-cta-2" class="isInvisible">
                  <div class="share-step text-center">
                      <h1><?php echo($section->share_title); ?></h1>
                      <div class="text-center">
                        <?php echo($section->share_description); ?>
                      </div>
                      <div class="share-links">
                        <?php if (is_array($section->sharing_links)) : ?>
                        <div>
                          <?php foreach ($section->sharing_links as $link) : ?>
                            <a class="btn btn-black btn-lg button-block-centered my-2" href="<?php echo($link['link']); ?>"><?php echo($link['label']); ?></a>
                          <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                      </div>
                  </div>
                </div>
              </div>
        </div>
    </section>

    <?php $section = getSection('why_are_we_rebelling_section'); ?>
    <?php if ($section->enabled) : ?>
        <section class="why-are-we-rebelling-section container-fluid text-center">
            <div class="row">
                <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-6 mx-auto px-4">
                    <h2><?php echo($section->heading); ?></h2>
                    <?php echo($section->content); ?>
              <?php if($section->demands): ?>
                  <a class="btn btn-black btn-lg mt-3" data-toggle="collapse" href="#demands" role="button" aria-expanded="false" aria-controls="demands">
                    <?php _e('OUR DEMANDS', 'theme-xrnl'); ?>
                    <i class="fas fa-chevron-down"></i>
                  </a>
                  <div class="text-left collapse" id="demands">
                    <?php echo($section->demands); ?>
                  </div>
                </div>
              <?php endif; ?>

            </div>
        </section>
    <?php endif; ?>

    <?php $section = getSection('who_is_extinction_rebellion_section');
    ?>
    <?php if ($section->enabled) : ?>
        <section class="who-is-extinction-rebellion-section text-white container-fluid text-center"
                 style="background: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.45)), url('<?= $section->background_image ?>') no-repeat center center / cover;">
            <div class="row">
                <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-6 mx-auto px-4">
                    <h2><?php echo($section->heading); ?></h2>
                    <?php echo($section->content); ?>
                    <a class="btn btn-yellow btn-lg" href="<?php echo($section->button_url); ?>">
                        <?php echo($section->button_text); ?>
                    </a>
                </div>
            </div>
        </section>
    <?php endif; ?>
</div>

<script type="text/javascript">
function increaseSignatures() {
  var submissionsField = jQuery('#total-submissions');
  var nSubmissions = parseInt(submissionsField.text())
    submissionsField.text(nSubmissions + 1);
}

function showCTA1() {
    jQuery('#submission-cta-1').attr("class","isVisible");
    jQuery('#form-subtext').attr("class","isInvisible");
}

function showCTA2() {
    jQuery('#submission-cta-1').attr("class","isInvisible");
    jQuery('#submission-cta-2').attr("class","isVisible");
}

jQuery(document).ready(function() {
  // enable smooth scrolling
  jQuery("a[href='#sign']").click(function(e) {
    jQuery([document.documentElement, document.body]).animate({
        scrollTop: jQuery("a[name='sign']").offset().top
    }, 500);
    e.preventDefault();
  });

  // show first call to action when form is submitted
  jQuery(document).on('can_embed_submitted', function() {
    increaseSignatures();
    showCTA1();
  });

  // show second call to action when user interacts with first call to action
  
  jQuery("#form-donate").click(function(e) {
    showCTA2();
  })

  jQuery("#form-no-donate").click(function(e) {
    showCTA2();
  })
});
</script>

<?php get_footer(); ?>
