<?php


final class WPWordCloud {

	private $pluginName = 'rpi-wordcloud';
	private $version = '1.0.0';

	private $error = NULL;

    public function __construct() {

		//?
		add_filter( 'get_word_cloud_instance', [ $this, 'get_instance' ] );

	}

    public function get_instance() {

		return $this; // return the object

    }


	/**
	 * Get global settings from settings page and
	 * overwrite with individual settings from shortcode
	 *
	 * @param {array} individual_settings Objects containing settings from shortcode
	 *
	 */

	private function getSettings($individual_settings) {


		$global_settings = [];

		foreach (wp_word_cloud_get_global_settings() as $name => $value) {

			// if global setting is a public one
			// get it's name and the value, which user defines on settings page
			if ($value['hidden'] === FALSE) {

				$global_settings[$name] = get_option($name, $value['default']);

			// otherwise set this value to NULL
			} else {

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

		debug_wp_word_cloud(json_encode($individual_settings, JSON_PRETTY_PRINT), $individual_settings);

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
			plugin_dir_url( __DIR__ ) . 'css/wpWordCloud.css',
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
			 plugin_dir_url( __DIR__ ) . 'js/wpWordCloud.js',
			 array( 'word-cloud-settings' )
		);

		if ($this->settings['enable-ocr'] == 1) {

			wp_enqueue_script(
				'tesseract-library',
				plugin_dir_url( __DIR__ ) . 'lib/tesseract.min.js',
				array( 'jquery' )
		   );

		   wp_enqueue_script(
			'init-ocr',
			plugin_dir_url( __DIR__ ) . 'js/initOcr.js',
			array( 'word-cloud' )

	   		);

		}
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
		$option = array();

		/*get_option('wp_word_cloud_settings');

		foreach(wp_word_cloud_get_global_settings() as $name=>$value){
			$option[$name]= get_option($name);
		}*/
		return '<script id="word-cloud-settings-'.$this->settings['id'].'">'.json_encode($this->settings).'</script>';



	}
}