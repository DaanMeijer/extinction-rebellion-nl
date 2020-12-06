<!DOCTYPE html>
<html>
<head>
    <meta charSet="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <meta name="google-site-verification" content="Oc-GUQaXHiPF-oVpMLzShjKqQTDGGZ3caVsE9t1Y5Kg" />
    <link rel="icon" href="<?php echo get_theme_file_uri("assets/images/favicon.png") ?>" type="image/x-icon" />

    <link href="https://fonts.googleapis.com/css?family=Crimson+Text|Montserrat" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo get_theme_file_uri("dist/fonts/fucxed.css"); ?>" />
    <link rel="stylesheet" href="<?php echo get_theme_file_uri("dist/css/app.css".date("?Ymd")."?v2"); ?>" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous" />
    <!-- Matomo -->
    <script type="text/javascript">
      var _paq = window._paq || [];
      /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
      _paq.push(['trackPageView']);
      _paq.push(['enableLinkTracking']);
      (function() {
        var u="//extinctionrebellion.nl/matomo/";
        _paq.push(['setTrackerUrl', u+'matomo.php']);
        _paq.push(['setSiteId', '1']);
        var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
        g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
      })();
    </script>


    <!-- End Matomo Code -->
    <?php wp_head(); ?>
</head>
<body>
    <header class="bg-green">

        <nav class="navbar navbar-light navbar-expand-xl" role="navigation">
            <a href="<?php echo (ICL_LANGUAGE_CODE === "nl") ? "" : "/en"; ?>/" class="navbar-brand">
                <?PHP echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'full', false, array( "class" => "img-fluid hide-xxs", "width" => "150" )); ?>
                <?PHP echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'full', false, array( "class" => "img-fluid show-xxs", "width" => "80" )); ?>
            </a>

            <div>
              <?php
              $donatePage = apply_filters('wpml_object_id', 308, 'page', true); // 308 is page ID
              $donatePageURL = get_permalink( $donatePage );

              $joinPage = apply_filters('wpml_object_id', 7587, 'page', true); // 7587 is page ID
              $joinPageUrl = get_permalink( $joinPage );
              ?>
              <a href="<?php echo $donatePageURL ?>" class="btn btn-black-r-invert hide-xl" target="_blank"><?php _e('donate', 'theme-xrnl'); ?></a>
              <a href="<?php echo $joinPageUrl ?>" class="btn btn-black-r hide-xl"><?php _e('join us', 'theme-xrnl'); ?></a>
              <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-nav" aria-controls="main-nav" aria-expanded="false" aria-label="Toggle navigation">
                  <span class="navbar-toggler-icon"></span>
              </button>
            </div>

            <div class="collapse navbar-collapse" id="main-nav">
                <?php wp_nav_menu( [
                    'theme_location' => 'primary',
                    'depth'	          => 2,
                    'container'       => '',
                    'menu_class'      => 'navbar-nav',
                    'fallback_cb'     => 'WP_Bootstrap_Navwalker::fallback',
                    'walker'          => new WP_Bootstrap_Navwalker(),
                ] ); ?>



                <ul class="list-unstyled d-flex my-3 my-xl-0 align-items-center ml-auto">
                    <li class="mx-3 mx-lg-2">
                        <a href="https://www.facebook.com/ExtinctionRebellionNL/" target="_blank" class="facebook" aria-label="facebook"><i class="fab text-black fa-facebook-f"></i></a></li>
                    <li class="mx-3 mx-lg-2">
                        <a href="https://twitter.com/nlrebellion" class="twitter" target="_blank" aria-label="twitter"><i class="fab text-black fa-twitter"></i></a></li>
                    <li class="mx-3 mx-lg-2">
                        <a href="https://www.instagram.com/extinctionrebellionnl/?hl=nl" target="_blank" class="insta" aria-label="instagram"><i class="fab text-black fa-instagram"></i></a></li>
                      <li class="mx-3 mx-lg-2 show-xl">
                        <?php wp_nav_menu( [
                            'theme_location' => 'language',
                            'container'       => '',
                            'fallback_cb'     => 'WP_Bootstrap_Navwalker::fallback',
                            'menu_class'      => 'navbar-nav language-menu',
                            'walker'          => new WP_Bootstrap_Navwalker(),
                        ] ); ?>
                      </li>
                    <li class="mx-3 mx-lg-2 show-xl">
                      <a href="<?php echo $donatePageURL ?>" class="btn btn-black-r-invert" target="_blank"><?php _e('donate', 'theme-xrnl'); ?></a>
                      <a href="<?php echo $joinPageUrl ?>" class="btn btn-black-r"><?php _e('join us', 'theme-xrnl'); ?></a>
                    </li>
                </ul>

                <div class="d-flex">
                  <div class="hide-xl">
                      <?php wp_nav_menu( [
                          'theme_location' => 'language',
                          'container'       => '',
                          'fallback_cb'     => 'WP_Bootstrap_Navwalker::fallback',
                          'menu_class'      => 'navbar-nav language-menu',
                          'walker'          => new WP_Bootstrap_Navwalker(),
                      ] ); ?>
                  </div>
              </div>
            </div>
        </nav>
    </header>
    <main>
