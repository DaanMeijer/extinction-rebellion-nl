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
            <a class="btn btn-yellow my-2 btn-lg" href="#progress-section">SIGN THE PETITION</a>
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

        Env::init();
        $actionnetwork_api_key = env('ACTION_NETWORK_API_KEY');

        $response = wp_remote_get(get_field('api_endpoint_english_form'), [
            'headers' => [
                'OSDI-API-Token'=> $actionnetwork_api_key
            ]
        ]);

        $data = json_decode($response['body'], true);
        $total_submissions = $data['total_submissions'];
        $max_submissions = roundUpToNearest($total_submissions, 500);
        $percent = $total_submissions/$max_submissions*100;
    ?>


    <section class="progress-section container-fluid bg-yellow" style="padding: 100px 20px;" id="progress-section">
        <div class="row">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-6 mx-auto">
                <h2 class="text-uppercase font-xr">
                    <span class="display-3" id="total-submissions"><?= $total_submissions ?></span> van <span id="max-submissions"><?= $max_submissions ?></span> handtekeningen
                </h2>

                <div class="progress" style="height: 20px;border-radius: 5px; margin-bottom: 20px;">
                    <div class="progress-bar" role="progressbar" style="width: <?= $percent ?>%; background: black" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                </div>

                <?= do_shortcode(get_field('actionnetwork_shortcode')) ?>
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
                    <a class="btn btn-yellow btn-lg" data-toggle="collapse" href="#demands" role="button" aria-expanded="false" aria-controls="demands">
                        <?php _e('OUR DEMANDS', 'theme-xrnl'); ?>
                        <i class="fas fa-chevron-down"></i>
                    </a>
                    <div class="text-left collapse collapse" id="demands">
                        <div class="mt-4">Wij eisen van de Nederlandse overheid:</div>
                        <ol class="pl-3 counter mt-3">
                            <li class="pl-4"><span class="text-green font-xr">WEES EERLIJK</span> over de klimaatcrisis en de ecologische ramp die ons voortbestaan bedreigen. Maak mensen bewust van de noodzaak voor grootschalige verandering.</li>
                            <li class="pl-4"><span class="text-green font-xr">DOE WAT NODIG IS</span> om biodiversiteitsverlies te stoppen en verminder de uitstoot van broeikasgassen naar netto nul in 2025. Doe dit op een rechtvaardige manier.</li>
                            <li class="pl-4"><span class="text-green font-xr">LAAT BURGERS BESLISSEN</span> over een rechtvaardige transitie door het oprichten van een Burgerberaad dat een leidende rol speelt in de besluitvorming.</li>
                        </ol>
                        <div class="pt-3 text-center"><a href="/demands">Lees meer</a> over onze eisen</div>
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
                    <a class="btn btn-blue btn-lg" href="<?= get_permalink(94) ?>">
                        <?php _e('ABOUT US', 'theme-xrnl'); ?>
                    </a>
                </div>
            </div>
        </section>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
