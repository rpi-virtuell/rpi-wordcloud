<?php

/**
 * Plugin Name:       rpi Word-Cloud
 * Plugin URI:        https://github.com/rpi-virtuell/rpi-wordcloud
 * Description:       Provides a Gutenberg block for generating graphical word clouds based on any text. Allows page visitors to manipulate the appearance and the text based focus of the word cloud. rpi WordCloud ist a fork from Niki Reinerts WP-Word-Cloud Plugin.
 * Version:           1.2.0
 * Author:            Joachim Happel
 * License:           MIT
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       rpi-wordcloud
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/rpi-virtuell/rpi-wordcloud
 * Requires at least: 5.6
 * Requires PHP:      7.2
 */

// if this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

	require_once( 'class/WordCloudShortcode.php' );

	require_once( 'class/WordCloudSettings.php' );

	require_once( 'class/WordCloudBlock.php' );

