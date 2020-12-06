<?php
/**
 * Shortcodes init
 * 
 * Init main shortcodes
 *
 * Copyright (c) 2011, cheshirewebsolutions.com, Ian Kennerley (info@cheshirewebsolutions.com).
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

include_once('shortcode-photostream.php');		// Displays the users Photostream
// include_once('pro/shortcode-photoset.php');		// Display the users Photoset specified by id
include_once('shortcode-favourites.php');		// Display the users Favourited photos
include_once('shortcode-galleries.php');		// Display the users Galleries
include_once('shortcode-albums.php');			// Display the users Albums

/**
 * Shortcode creation
 **/
add_shortcode( 'cws_fgp_photostream', 'cws_fgp_shortcode_photostream' );
// add_shortcode( 'cws_fgp_photoset', 'cws_fgp_shortcode_photoset' );		// display photoset by specified id
add_shortcode( 'cws_fgp_favourites', 'cws_fgp_shortcode_favourites' );  // display favourites
add_shortcode( 'cws_fgp_galleries', 'cws_fgp_shortcode_galleries' );	// display galleries
add_shortcode( 'cws_fgp_albums', 'cws_fgp_shortcode_albums' );			// display albums