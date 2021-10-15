<?php
/**
 * Copyright (c) 2018 nickyreinert
 * Released under the MIT license
 *
 * @author Niki Reinert
 * @see (origin) https://github.com/nickyreinert/wordCloud-for-Wordpress/blob/wordCloud-for-wordPress-2/php/renderShortcode.php
 * recoded by Joachim Happel
 */

final class WordCloudShortcode {

	private $error = NULL;

    public function __construct() {}

    /**
	 * Get global settings from settings page and
	 * overwrite with individual settings from shortcode
	 *
	 * @param {array} individual_settings Objects containing settings from shortcode
	 *
	 */

	private function getSettings($individual_settings) {


		$global_settings = [];

		foreach (rpiWordCloudSettings::wp_word_cloud_get_global_settings() as $name => $value) {

			$value = get_option(rpiWordCloudSettings::$prefix.$name, $value['default']);
			$value = apply_filters('rpi_word_cloud_settings',$value,$name);

			// if global setting is a public one
			// get it's name and the value, which user defines on settings page
			if ($value['hidden'] === FALSE) {

				$global_settings[$name] = $value;

			// otherwise set this value to NULL
			} else {
				$global_settings[$name] = $value;
				//$global_settings[$name] = NULL;

			}

		}

		// overwrite global settings with given individual settings from shortcode
		$this->settings = shortcode_atts(

			($global_settings), ($individual_settings)

		);

		switch ($this->settings['text-transform']) {

			case 'uppercase':
				$this->settings['black-list'] = mb_strtoupper($this->settings['black-list']);
				break;

			case 'lowercase':
				$this->settings['black-list'] = mb_strtolower($this->settings['black-list']);
				break;

		}
		// check if required mandatory setting is given in shortcode
		// id needs to be unique, as you can use the shortcode multiple times
		if ($this->settings['id'] == NULL) {

			$this->error = 'No unique id given. Please use parameter `id`.';

		}

		$this->settings['plugin-path'] = plugin_dir_url( __DIR__ );

		$this->debug_wp_word_cloud(json_encode($individual_settings, JSON_PRETTY_PRINT), $individual_settings);

	}

	private function createDomData() {

		// put settings object into java script object and send it to frontend
		wp_localize_script( "word-cloud", "word_cloud_settings_".$this->settings['id'], $this->settings );

		// send canvas to frontend containing address of the object
		$result = "<div class='word-cloud-container' settings='word_cloud_settings_".$this->settings['id']."' id='word-cloud-container-".$this->settings['id']."'></div>";

		return $result;

	}

	private function enqueueDepencies() {

		wp_enqueue_style(
			$this->pluginName,
			plugin_dir_url( __DIR__ ) . 'css/rpiWordCloud.css',
			array(),
			$this->version,
			'all' );

		wp_register_script(
			 'word-cloud-renderer',
			 plugin_dir_url( __DIR__ ) . 'lib/wordcloud2.js',
			 array( 'jquery' )
		 );

		wp_enqueue_script(
			 'word-cloud-settings',
			 plugin_dir_url( __DIR__ ) . 'js/getWordCloudSettings.js',
			 array( 'word-cloud-renderer' )
		);

		wp_enqueue_script(
			 'word-cloud',
			 plugin_dir_url( __DIR__ ) . 'js/rpiWordCloud.js',
			 array( 'word-cloud-settings' )
		);


	}

	private function getDataFromSource($source) {
		switch ($this->settings['source-type']) {
			case 'inline':
			default:
				$this->settings['data'] = $source;
				break;
		}

	}

    public function initWordCloud($individual_settings, $content = NULL) {

		$this->getSettings($individual_settings);

		$this->enqueueDepencies();

		$this->getDataFromSource($content);

		if ($this->error != NULL) {

			return '<p class="word-cloud-warning">'.$this->error.'</p>';

		}
		return $this->createDomData();


    }
	public function initWordCloudBlock($individual_settings, $content = NULL) {

		$this->getSettings($individual_settings);

		$this->enqueueDepencies();

		$this->getDataFromSource($content);

		if ($this->error != NULL) {

			return '<p class="word-cloud-warning">'.$this->error.'</p>';

		}

		return $this->settings;

    }

	public function the_block(){

		$output =  $this->get_settings();
		$output .="<div class='word-cloud-container' settings='word_cloud_settings_".$this->settings['id']."' id='word-cloud-container-".$this->settings['id']."'></div>";
		return $output;
	}
	public function get_settings(){
		return '<script id="word-cloud-settings-'.$this->settings['id'].'">'.json_encode($this->settings).'</script>';
	}
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
}


