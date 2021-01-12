/*
This script contains some settings for the tab controls and image gallery on
single local group pages (single-xrnl_local_group.php).
*/

const sections = ['about', 'demands', 'actions', 'events', 'positions', 'pictures'];

function checkSectionExists(section) {
  if (! jQuery('#' + section).length) {
    jQuery('#lg-navigation').find('a[href=\'#' + section + '\']').parent().remove();
  }
}

jQuery(document).ready(function(){

  // Remove optional sections from the navigation if they don't exist;
  // If there is only one nav-item left, remove that too.
  sections.forEach(checkSectionExists);
  if (jQuery('#lg-navigation .nav .nav-item').length < 2){
    jQuery('#lg-navigation .nav .nav-item').remove();
  };

  // If the Over Ons section exists, make it active on page load (instead of Contact).
  if (jQuery('#about-nav').length) {
    jQuery('#about-nav').tab('show');
  }

  // Settings for the picture gallery
  jQuery(".owl-carousel").owlCarousel({
    loop:true,
    margin:5,
    nav:true,
    items:1,
    autoHeight:true
  });

});
