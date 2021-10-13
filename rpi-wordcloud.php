<?php

/**
 * Plugin Name:       rpi Word-Cloud
 * Plugin URI:        https://github.com/rpi-virtuell/rpi-wordcloud
 * Description:       Provides a Gutenberg block for generating graphical word clouds based on any text. Allows page visitors to manipulate the appearance and the text based focus of the word cloud. rpi WordCloud ist a fork from Niki Reinerts WP-Word-Cloud Plugin.
 * Version:           1.0.0
 * Author:            Niki Reinert/Joachim Happel
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

