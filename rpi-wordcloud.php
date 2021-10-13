<?php

/**
 * Plugin Name:       rpi Word-Cloud
 * Plugin URI:        https://github.com/rpi-virtuell/rpi-wordcloud
 * Description:       Gutenberg Block to draw word clouds based on text based on the rpi-wordcloud Plugin from Niki Reinert
 * Version:           1.0.0
 * Author:            Joachim Happel
 * Author URI:        https://rpi-virtuell.de
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       rpi-wordcloud
 * Domain Path:       /languages
 */

// if this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Adding the shortcode
 * @since    1.0.0
 */

	require_once('php/renderShortcode.php');

	require_once('php/initSettings.php');

	require_once( 'php/WordCloudBlock.php' );

	add_shortcode('rpi-wordcloud',[new WPWordCloud, 'initWordCloud']);

/**
*	log function to send debug information to browser console
*
*/

function debug_wp_word_cloud($message = NULL, $individual_settings= NULL ){

	// on settings page, debug level will be defined
	// MAX_DEBUG_PRIORITY = 0 - no messages at all
	// MAX_DEBUG_PRIORITY = 1 - errors & warnings only
	// MAX_DEBUG_PRIORITY = 2 - every piece of information

	$debug = isset($individual_settings['debug']) ? $individual_settings['debug'] : FALSE;

	if (($debug == TRUE OR $debug == 1 OR $debug == '1') AND is_admin() == FALSE) {

		$message = json_encode($message, JSON_PRETTY_PRINT);

		echo "<script>console.log('WORDCLOUD|DEBUG: ' + ".$message.");</script>";

	}

}
