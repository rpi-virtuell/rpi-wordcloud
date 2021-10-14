<?php
/**
 * @author Joachim Happel
 */

Class  WPWordCloudBlock{

	public function __construct() {

		add_action('init', array('WPWordCloudBlock','init_word_cloud_block'));
		add_action( 'enqueue_block_assets',array('WPWordCloudBlock','wordcloud_blockeditor_js') );
		add_filter( 'lzb/prepare_block_attribute', array('WPWordCloudBlock', 'wordcloud_lzb_prepare_block_attribute'), 10, 5 );
		add_filter( 'lazyblock/wordcloud/frontend_callback', array($this,'frontend'), 10, 2 );
		add_filter( 'lazyblock/wordcloud/editor_callback', array($this,'editor'), 10, 2 );
		add_filter( 'rpi_word_cloud_settings', array($this,'filter_settings'), 10, 2 );

		$LZB_DIR = WP_PLUGIN_DIR .'/lazy-blocks/';

		if(file_exists($LZB_DIR . 'lazy-blocks.php')){
			include_once $LZB_DIR . 'lazy-blocks.php';
		}


	}


	/**
	 * fix settings
	 */
	public function filter_settings( $value, $name ){

		$option = array();
		$option['source-type']= 'inline';
		$option['frontend-settings']= true;
		$option['style']= 'canvas';
		$option['enable-black-list']= true;
		$option['enable-custom-black-list']= true;
		$option['persistent-custom-black-list']= true;
		$option['draw-out-of-bound']= false;
		$option['enable-ocr']= false;
		$option['ocr-local-libraries']= false;
		$option['frontend-settings']= true;
		$option['min-size']= 8;
		$option['count-words']= true;
		$option['ellipticity']= 1;
		$option['ignore-chars']= "[\(\)\[\]\,\.\;\:\?\!]";
		$option['ocr-language']= 'deu';

		rpiWordCloudSettings::$hide_settings = array_keys($option);

		$value = isset($option[$name])? $option[$name] : $value;

		return $value;
	}



	function frontend($output, $attributes){

		$arr = array();
		$arr[]= 'id="' .$attributes['blockId'].'"';
		$attributes['background-color']=preg_replace('/var\([^,]*,\s?([^\)]*)\)/','$1',$attributes['background-color']);
		foreach ($attributes as $key=>$value){
			if($key!= 'blockId' && $key!= 'source' ){
				$arr[]= $key .'="' .$value.'"';
			}

		}
		$attr = implode(' ', $arr);
		$code = '[rpi-wordcloud '.$attr.']'.$attributes['source'].'[/rpi-wordcloud]';
		return do_shortcode($code) ;

	}
	function editor($output, $attributes){

		$attributes['background-color']=preg_replace('/var\([^,]*,\s?([^\)]*)\)/','$1',$attributes['background-color']);
		$wc = new rpiWordCloud();
		$attributes['id'] = $attributes['blockId'];
		$wc->initWordCloudBlock( $attributes, $attributes['source'] );

		$output = $wc->the_block();
		return $output;
	}

	/*
	load scripts in editor
	*/
	static function wordcloud_blockeditor_js(){

		if (!is_admin()) return;

		$plugin_folder = basename(dirname(__DIR__));
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
		wp_enqueue_script(
			'word-cloud-block',
			plugin_dir_url( __DIR__ ) . 'js/editor.js',
			array( 'word-cloud-settings' )
		);

	}
	/*
	Filter lzb/prepare_block_attribute
	*/
	static function wordcloud_lzb_prepare_block_attribute( $attribute_data, $control, $controls, $k, $block ) {
		// Change default value for custom control "blacklist"
		if($control && isset( $control['type'] ) && $control['type']=='textarea' && $control['name']=='black-list' && $block['slug']=='lazyblock/wordcloud'){

			$attribute_data['default'] = get_option(rpiWordCloudSettings::$prefix.'black-list');
		}

		return $attribute_data;
	}

	/*
	required plugin: lazy-blocks  (muss installiert aber nicht aktiv sein)
	*/
	function init_word_cloud_block(){

		if ( function_exists( 'lazyblocks' ) ) :

			lazyblocks()->add_block( array(
				'id' => 247,
				'title' => 'Wordcloud',
				'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M9.93 13.5h4.14L12 7.98zM20 2H4c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-4.05 16.5l-1.14-3H9.17l-1.12 3H5.96l5.11-13h1.86l5.11 13h-2.09z" /></svg>',
				'keywords' => array(
				),
				'slug' => 'lazyblock/wordcloud',
				'description' => '',
				'category' => 'text',
				'category_label' => 'text',
				'supports' => array(
					'customClassName' => true,
					'anchor' => false,
					'align' => array(
						0 => 'wide',
						1 => 'full',
					),
					'html' => false,
					'multiple' => true,
					'inserter' => true,
				),
				'ghostkit' => array(
					'supports' => array(
						'spacings' => false,
						'display' => false,
						'scrollReveal' => false,
						'frame' => false,
						'customCSS' => false,
					),
				),
				'controls' => array(
					'control_0989e2468b' => array(
						'type' => 'textarea',
						'name' => 'source',
						'default' => '',
						'label' => 'Textquelle',
						'help' => '',
						'child_of' => '',
						'placement' => 'content',
						'width' => '100',
						'hide_if_not_selected' => 'true',
						'save_in_meta' => 'false',
						'save_in_meta_name' => '',
						'required' => 'false',
						'placeholder' => 'Text eingeben',
						'characters_limit' => '',
					),
					'control_72e81843ec' => array(
						'type' => 'select',
						'name' => 'color',
						'default' => 'random-dark',
						'label' => 'Farbschschema',
						'help' => '',
						'child_of' => '',
						'placement' => 'inspector',
						'width' => '100',
						'hide_if_not_selected' => 'false',
						'save_in_meta' => 'false',
						'save_in_meta_name' => '',
						'required' => 'false',
						'choices' => array(
							array(
								'label' => 'Zufällig helle Farben',
								'value' => 'random-light',
							),
							array(
								'label' => 'Zufällig dunkle Farben',
								'value' => 'random-dark',
							),
							array(
								'label' => 'Graustufen',
								'value' => 'black',
							),
							array(
								'label' => 'Rot',
								'value' => 'red',
							),
							array(
								'label' => 'Orange',
								'value' => 'orange',
							),
							array(
								'label' => 'Grün',
								'value' => 'green',
							),
							array(
								'label' => 'Blau',
								'value' => 'blue',
							),
							array(
								'label' => 'Violett',
								'value' => 'violett',
							),
							array(
								'label' => 'Türkies',
								'value' => 'turkey',
							),
						),
						'allow_null' => 'false',
						'multiple' => 'false',
						'output_format' => '',
						'placeholder' => '',
						'characters_limit' => '',
					),
					'control_8f1b8748ee' => array(
						'type' => 'range',
						'name' => 'min-word-occurence',
						'default' => '1',
						'label' => 'Mindestvorkommen',
						'help' => '',
						'child_of' => '',
						'placement' => 'inspector',
						'width' => '100',
						'hide_if_not_selected' => 'false',
						'save_in_meta' => 'false',
						'save_in_meta_name' => '',
						'required' => 'false',
						'min' => '1',
						'max' => '10',
						'step' => '1',
						'placeholder' => '',
						'characters_limit' => '',
					),
					'control_8a7b7f4e89' => array(
						'type' => 'range',
						'name' => 'size-factor',
						'default' => '100',
						'label' => 'Wortgröße',
						'help' => '',
						'child_of' => '',
						'placement' => 'inspector',
						'width' => '100',
						'hide_if_not_selected' => 'false',
						'save_in_meta' => 'false',
						'save_in_meta_name' => '',
						'required' => 'false',
						'min' => '10',
						'max' => '300',
						'step' => '1',
						'placeholder' => '',
						'characters_limit' => '',
					),
					'control_dc09a342cb' => array(
						'type' => 'range',
						'name' => 'grid-size',
						'default' => '10',
						'label' => 'Textabstand',
						'help' => '',
						'child_of' => '',
						'placement' => 'inspector',
						'width' => '100',
						'hide_if_not_selected' => 'false',
						'save_in_meta' => 'false',
						'save_in_meta_name' => '',
						'required' => 'false',
						'min' => '1',
						'max' => '50',
						'step' => '1',
						'placeholder' => '',
						'characters_limit' => '',
					),
					'control_6969a642fd' => array(
						'type' => 'range',
						'name' => 'min-alpha',
						'default' => '0.3',
						'label' => 'Min. Alpha-Wert',
						'help' => '',
						'child_of' => '',
						'placement' => 'inspector',
						'width' => '100',
						'hide_if_not_selected' => 'false',
						'save_in_meta' => 'false',
						'save_in_meta_name' => '',
						'required' => 'false',
						'min' => '0.1',
						'max' => '1',
						'step' => '0.1',
						'placeholder' => '',
						'characters_limit' => '',
					),
					'control_9778554f56' => array(
						'type' => 'select',
						'name' => 'shape',
						'default' => '',
						'label' => 'Form der Wordcloud',
						'help' => '',
						'child_of' => '',
						'placement' => 'inspector',
						'width' => '100',
						'hide_if_not_selected' => 'false',
						'save_in_meta' => 'false',
						'save_in_meta_name' => '',
						'required' => 'false',
						'choices' => array(
							array(
								'label' => 'Rechteck',
								'value' => 'square',
							),
							array(
								'label' => 'Kreis',
								'value' => 'circle',
							),
							array(
								'label' => 'Pentagon',
								'value' => 'pentagon',
							),
							array(
								'label' => 'Stern',
								'value' => 'star',
							),
							array(
								'label' => 'Pfeil (Play)',
								'value' => 'triangle-forward',
							),
						),
						'allow_null' => 'false',
						'multiple' => 'false',
						'output_format' => '',
						'placeholder' => '',
						'characters_limit' => '',
					),
					'control_0cd917433d' => array(
						'type' => 'range',
						'name' => 'canvas-height',
						'default' => '800',
						'label' => 'Bildhöhe der Wordcloud',
						'help' => '',
						'child_of' => '',
						'placement' => 'inspector',
						'width' => '100',
						'hide_if_not_selected' => 'false',
						'save_in_meta' => 'false',
						'save_in_meta_name' => '',
						'required' => 'false',
						'min' => '300',
						'max' => '1024',
						'step' => '5',
						'placeholder' => '',
						'characters_limit' => '',
					),
					'control_0cd917433c' => array(
						'type' => 'color',
						'name' => 'background-color',
						'default' => 'rgba(255,255,255,0.2)',
						'label' => 'Hintergundfarbe',
						'help' => '',
						'child_of' => '',
						'placement' => 'inspector',
						'width' => '100',
						'hide_if_not_selected' => 'false',
						'save_in_meta' => 'false',
						'save_in_meta_name' => '',
						'required' => 'false',
						'min' => '300',
						'max' => '1024',
						'step' => '5',
						'placeholder' => '',
						'characters_limit' => '',
					),
					'control_b8fbcd4531' => array(
						'type' => 'range',
						'name' => 'rotate-ratio',
						'default' => '0.1',
						'label' => 'Häufigkeit der Rotation',
						'help' => '',
						'child_of' => '',
						'placement' => 'inspector',
						'width' => '100',
						'hide_if_not_selected' => 'false',
						'save_in_meta' => 'false',
						'save_in_meta_name' => '',
						'required' => 'false',
						'min' => '0',
						'max' => '1',
						'step' => '0.1',
						'placeholder' => '',
						'characters_limit' => '',
					),
					'control_159be4495a' => array(
						'type' => 'toggle',
						'name' => 'shrink-to-fit',
						'default' => '',
						'label' => 'Größe auf Seite einpassen',
						'help' => '',
						'child_of' => '',
						'placement' => 'inspector',
						'width' => '100',
						'hide_if_not_selected' => 'false',
						'save_in_meta' => 'false',
						'save_in_meta_name' => '',
						'required' => 'false',
						'checked' => 'true',
						'alongside_text' => '',
						'placeholder' => '',
						'characters_limit' => '',
					),
					'control_8209a340ee' => array(
						'type' => 'textarea',
						'name' => 'black-list',
						'default' => 'der die das den dem des um zu so in im',
						'label' => 'Ignorierte Worte',
						'help' => '',
						'child_of' => '',
						'placement' => 'inspector',
						'width' => '100',
						'hide_if_not_selected' => 'false',
						'save_in_meta' => 'false',
						'save_in_meta_name' => '',
						'required' => 'false',
						'placeholder' => '',
						'characters_limit' => '',
					),
					'control_3428b247ce' => array(
						'type' => 'toggle',
						'name' => 'enable-frontend-edit',
						'default' => '',
						'label' => 'Änderungsoptionen im Frontend erlauben',
						'help' => '',
						'child_of' => '',
						'placement' => 'inspector',
						'width' => '100',
						'hide_if_not_selected' => 'false',
						'save_in_meta' => 'false',
						'save_in_meta_name' => '',
						'required' => 'false',
						'checked' => 'false',
						'alongside_text' => '',
						'placeholder' => '',
						'characters_limit' => '',
					),
				),
				'code' => array(
					'output_method' => 'php',
					'editor_html' => '',
					'editor_callback' => '',
					'editor_css' => '',
					'frontend_html' => '',
					'frontend_callback'=>'',
					'frontend_css' => '',
					'show_preview' => 'always',
					'single_output' => false,
				),
				'condition' => array(
				),
			) );

		endif;
	}

}

new WPWordCloudBlock();
