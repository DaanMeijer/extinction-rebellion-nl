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
       style="background: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.45)), url('<?php the_field('join_cover_image_url'); ?>') no-repeat;">
      <h1 class="display-2 text-uppercase font-xr"><?php the_title(); ?></h1>
      <div class="container">
        <div class="col-lg-8 mx-auto">
          <?php the_content(); ?>
            <!--     On click, scroll to the progress bar section. We can later make it smoothly with a JS library/plugin       -->
            <a class="btn btn-blue my-2 btn-lg" href="#sign"><?php _e('SIGN THE PETITION', 'theme-xrnl'); ?></a>
        </div>
      </div>
  </div>


    <?php $section = getSection('about_section'); ?>
    <?php if ($section->enabled) : ?>
        <section class="campaign-section container-fluid text-center">
            <div class="row">
                <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-6 mx-auto">
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


    <section class="progress-section container-fluid bg-yellow py-sm-5 py-4"  id="sign">
        <a name="sign"></a>
        <div class="row py-5">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-6 mx-auto">
                <div class="sign-step">
                    <h2 class="text-uppercase font-xr">
                        <span class="display-3" id="total-submissions"><?= $total_submissions ?></span> van <span id="max-submissions"><?= $max_submissions ?></span> handtekeningen
                    </h2>

                    <div class="progress" style="height: 20px;border-radius: 5px; margin-bottom: 20px;">
                        <div class="progress-bar" role="progressbar" style="width: <?= $percent ?>%; background: black" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>

                    <?= do_shortcode(get_field('actionnetwork_shortcode')) ?>
                </div>

                <div class="donate-step">
                    <h1><?= _e("THANKS! YOU'RE AWESOME", 'theme-xrnl'); ?></h1>
                    <div class="text-center">
                        <?= get_field('donate_step_text') ?>
                    </div>

                    <?= do_shortcode(get_field('whydonate_shortcode')) ?>
                    <a class="btn my-2 btn-lg" href="#sign">
                        <?= _e('NO THANKS', 'theme-xrnl'); ?>
                    </a>
                </div>

                <div class="share-step text-center">
                    <h1><?= _e('ONE LAST THING', 'theme-xrnl'); ?></h1>
                    <div class="text-center">
                        <?= get_field('share_step_text') ?>
                    </div>
                    <div class="share-links">
                        <a class="btn btn-black my-2"><?= _e('SHARE ON WHATSAPP', 'theme-xrnl'); ?></a>
                        <a class="btn btn-black my-2"><?= _e('SHARE ON FACEBOOK', 'theme-xrnl'); ?></a>
                        <a class="btn btn-black my-2"><?= _e('SHARE ON TWITTER', 'theme-xrnl'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <?php $section = getSection('why_are_we_rebelling_section'); ?>
    <?php if ($section->enabled) : ?>
        <section class="why-are-we-rebelling-section container-fluid text-center">
            <div class="row">
                <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-6 mx-auto">
                    <h2><?php echo($section->heading); ?></h2>
                    <?php echo($section->content); ?>

                    <?php if ($section->show_demands) : ?>
                    <a class="btn btn-black btn-lg" data-toggle="collapse" href="#demands" role="button" aria-expanded="false" aria-controls="demands">
                        <?php _e('OUR DEMANDS', 'theme-xrnl'); ?>
                        <i class="fas fa-chevron-down"></i>
                    </a>
                    <div class="text-left collapse collapse" id="demands">
                         <!-- This is some horrible hardcoded code. Must be refactored into an ACF options page, so that the demands can be edit from wordpress and imported in different places (about us, petition...) -->
                        <?php if (ICL_LANGUAGE_CODE=='nl') : ?>
                        <div class="mt-4">Wij eisen van de Nederlandse overheid:</div>
                        <ol class="pl-3 counter mt-3">
                            <li class="pl-4"><span class="text-green font-xr">WEES EERLIJK</span> over de klimaatcrisis en de ecologische ramp die ons voortbestaan bedreigen. Maak mensen bewust van de noodzaak voor grootschalige verandering.</li>
                            <li class="pl-4"><span class="text-green font-xr">DOE WAT NODIG IS</span> om biodiversiteitsverlies te stoppen en verminder de uitstoot van broeikasgassen naar netto nul in 2025. Doe dit op een rechtvaardige manier.</li>
                            <li class="pl-4"><span class="text-green font-xr">LAAT BURGERS BESLISSEN</span> over een rechtvaardige transitie door het oprichten van een Burgerberaad dat een leidende rol speelt in de besluitvorming.</li>
                        </ol>
                        <div class="pt-3 text-center"><a href="/demands">Lees meer</a> over onze eisen</div>
                        <? else: ?>
                        <div class="mt-4">We demand from the Dutch government:</div>
                        <ol class="pl-3 counter mt-3">
                          <li class="pl-4"><span class="text-green font-xr">TELL THE TRUTH</span> about the climate and ecological crisis that threatens our existence and communicate the urgency for change.</li>
                          <li class="pl-4"><span class="text-green font-xr">ACT NOW</span> to halt biodiversity loss and reduce greenhouse gas emissions to net zero by 2025 in a just and fair manner.</li>
                          <li class="pl-4"><span class="text-green font-xr">LET CITIZENS DECIDE</span> by establishing a Citizen’s Assembly which takes the lead on climate and ecological justice.</li>
                        </ol>
                        <div class="pt-3 text-center"><a href="/en/demands">Learn more</a> about our demands</div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php $section = getSection('who_is_extinction_rebellion_section');
    ?>
    <?php if ($section->enabled) : ?>
        <section class="who-is-extinction-rebellion-section text-white container-fluid text-center"
                 style="background: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.45)), url('<?= $section->background_image ?>') no-repeat;">
            <div class="row">
                <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-6 mx-auto">
                    <h2><?php echo($section->heading); ?></h2>
                    <?php echo($section->content); ?>
                    <a class="btn btn-blue btn-lg" href="<?= insertURL(94) ?>">
                        <?php _e('ABOUT US', 'theme-xrnl'); ?>
                    </a>
                </div>
            </div>
        </section>
    <?php endif; ?>
</div>

<script type="text/javascript">
  jQuery(document).ready(function() {
    jQuery("a[href='#sign']").click(function(e) {
      jQuery([document.documentElement, document.body]).animate({
          scrollTop: jQuery("a[name='sign']").offset().top
      }, 500);
      e.preventDefault();
    });
  });
</script>

<?php get_footer(); ?>
