/*
This script contains some settings for the tab controls and image gallery on
single local group pages (single-xrnl_local_group.php).
*/

const sections = ['about', 'demands', 'actions', 'events', 'positions', 'pictures'];

function checkSectionExists(section) {
  if (! jQuery('#' + section).length) {
    jQuery('#lg-navigation')
    .find('a[href=\'#' + section + '\']')
    .parent()
    .remove();
  }
}

jQuery(document).ready(function($){

  // Remove optional sections from the navigation if they don't exist;
  // If there is only one nav-item left, remove that too.
  sections.forEach(checkSectionExists);
  if ($('#lg-navigation .nav .nav-item').length < 2){
    $('#lg-navigation .nav .nav-item').remove();
  };

  // See if a specific tab was requested in the URL
  let URLParam = window.location.hash.substring(1);
  if (sections.includes(URLParam)) {
    var navID = ['#', URLParam, '-nav'].join('');
  }

  // On page load, show the requested tab if it exists
  // If no specific tab was requested, show the About-us section if it exists
  // Otherwise, show the Contact tab as default
  if ($(navID).length) {
    $(navID).tab('show');
  } else if ($('#about-nav').length) {
    $('#about-nav').tab('show');
  } else {
    $('#contact-nav').tab('show');
  }

  // Initiate the carousel
  if ($('#pictures').length) {
    $('.carousel-item:first').addClass('active');
    $('.carousel-indicators > li:first').addClass('active');
  };

});
