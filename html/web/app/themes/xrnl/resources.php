<?php
/*
Template name: Resources
*/

get_header(); ?>

<script type="text/javascript" src="/app/themes/xrnl/assets/js/iframeResizer.min.js">
</script>




<style type="text/css">
  iframe {
    border: none;
    width: 100%;
  }
</style>

<div style="overflow:auto;-webkit-overflow-scrolling:touch">
  <iframe id="xr-academy"
          src="https://resources.extinctionrebellion.nl"
          scrolling="no"
          allowfullscreen
          width="100%"
          frameBorder="0"
          >
  </iframe>
</div>

<script>
  jQuery(document).ready(function(){
    jQuery("#xr-academy").attr("src", get_localized_url(window.location.pathname));
      function get_localized_url(path) {
        if (path.match("/en")) {
          return "https://resources.extinctionrebellion.nl/en";
        } else {
          return "https://resources.extinctionrebellion.nl/nl";
        }
      }
  });
  jQuery("#xr-academy").on("load", function() {
      window.scroll(0, 0);
      iFrameResize({
        log: false,
        checkOrigin: true,
        heightCalculationMethod: 'bodyScroll'
      }, '#xr-academy'
      );
      this.contentWindow.postMessage({"in_iframe": true}, 'https://resources.extinctionrebellion.nl');
  });

</script>

<?php get_footer(); ?>
