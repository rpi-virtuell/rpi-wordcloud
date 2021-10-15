<?php
/**
 * Copyright (c) 2018 nickyreinert
 * Released under the MIT license
 *
 * @author Niki Reinert
 * @see https://github.com/nickyreinert/wordCloud-for-Wordpress/blob/wordCloud-for-wordPress-2/php/initSettings.php
 * recoded by Joachim Happel
 */
class rpiWordCloudSettings{

    static $prefix = 'rpiwcloud_';
    static $hide_settings = array();

    function __construct(){

	    add_action( 'admin_init', ['rpiWordCloudSettings','wp_word_cloud_register_settings'] );
	    add_action('admin_menu', [$this,'wp_word_cloud_register_options_page']);
	    add_shortcode('rpi-wordcloud',[new WordCloudShortcode, 'initWordCloud']);

    }
	/**
	 * Return object of global settings, default values and
	 * and description
	 *
	 */

	static function wp_word_cloud_get_global_settings() {

        $noise ="ab aber abgesehen alle allein aller alles als am an andere anderen anderenfalls anderer anderes anstatt auch auf aus aussen außen ausser außer ausserdem außerdem außerhalb ausserhalb behalten bei beide beiden beider beides beinahe bevor bin bis bist bitte da daher danach dann darueber darüber darueberhinaus darüberhinaus darum das dass daß dem den der des deshalb die diese diesem diesen dieser dieses dort duerfte duerften duerftest duerftet dürfte dürften dürftest dürftet durch durfte durften durftest durftet ein eine einem einen einer eines einige einiger einiges entgegen entweder erscheinen es etwas fast fertig fort fuer für gegen gegenueber gegenüber gehalten geht gemacht gemaess gemäß genug getan getrennt gewesen gruendlich gründlich habe haben habt haeufig häufig hast hat hatte hatten hattest hattet hier hindurch hintendran hinter hinunter ich ihm ihnen ihr ihre ihrem ihren ihrer ihres ihrige ihrigen ihriges immer in indem innerhalb innerlich irgendetwas irgendwelche irgendwenn irgendwo irgendwohin ist jede jedem jeden jeder jedes jedoch jemals jemand jemandem jemanden jemandes jene jung junge jungem jungen junger junges kann kannst kaum koennen koennt koennte koennten koenntest koenntet können könnt könnte könnten könntest könntet konnte konnten konntest konntet machen macht machte mehr mehrere mein meine meinem meinen meiner meines meistens mich mir mit muessen müssen muesst müßt muß muss musst mußt nach nachdem naechste nächste nebenan nein nichts niemand niemandem niemanden niemandes nirgendwo nur oben obwohl oder oft ohne pro sagte sagten sagtest sagtet scheinen sehr sei seid seien seiest seiet sein seine seinem seinen seiner seines seit selbst sich sie sind so sogar solche solchem solchen solcher solches sollte sollten solltest solltet sondern statt stets tatsächlich tatsaechlich tief tun tut ueber über ueberall überall um und uns unser unsere unserem unseren unserer unseres unten unter unterhalb usw viel viele vielleicht von vor vorbei vorher vorueber vorüber waehrend während wann war waren warst wart was weder wegen weil weit weiter weitere weiterem weiteren weiterer weiteres welche welchem welchen welcher welches wem wen wenige wenn wer werde werden werdet wessen wie wieder wir wird wirklich wirst wo wohin wuerde wuerden wuerdest wuerdet würde würden würdest würdet wurde wurden wurdest wurdet ziemlich zu zum zur zusammen zwischen";

		return [
			'source-type'		=> ['label'=>'Datenquelle',                                 'default' => 'url',              'valid' => ['inline', 'url', 'sql', 'custom-field', 'id', 'tags'], 'hidden' => false, 'description' => 'Woher kommt die Liste gezählter Wörter? Möglich sind url, inline, sql, post oder page id sowie custom-field'],
			'count-words'	    => ['label'=>'Wörter zählen?',                              'default' => false,              'valid' => 'bool',   'hidden' => false, 'description' => 'Enthält die Quelle Text und müssen die Wörter erst gezählt werden?'],
			'enable-frontend-edit' => ['label'=>'Erlaube Bearbeiten im Frontend',           'default' => 0,                  'valid' => 'bool','hidden' => false, 'description' => 'Zeigt im Frontend ein Textfeld an, damit der Besucher die WordCloud selber bearbeiten kann.'],
			'frontend-settings'	=> ['label'=>'Erlaube Frontend Einstellungen',              'default' => false,              'valid' => 'bool', 'hidden' => false, 'description' => 'Erlaubt das Anpassen von einigen Parametern direkt im Frontend.'],
			'enable-ocr'        => ['label'=>'Elaube OCR',                                  'default' => 0,                  'valid' => 'bool','hidden' => false, 'description' => 'Ermögliche das Hinzufügen von Texten direkt von der Kamera des Gerätes.'],
			'style'	            => ['label'=>'Darstellung',                                 'default' => 'canvas',           'valid' => ['canvas', 'html'], 'hidden' => false, 'description' => 'Du kannst eine WordCloud als Canvas (also Bild) oder mit HTML-Tags erstellen.'],
			'ocr-language'      => ['label'=>'OCR Spache',                                  'default' => 'deu',              'hidden' => false, 'description' => 'Eine Liste unterstützter Sprachen und ihr Kürzel findest du hier: https://tesseract-ocr.github.io/tessdoc/Data-Files#data-files-for-version-400-november-29-2016. Du kannst mehrere Sprache mit Plus getrennt angeben (deu+eng).'],
			'ocr-local-libraries' => ['label'=>'Lokale OCR Bibliotheken',                   'default' => 0,                  'valid' => 'bool', 'hidden' => false, 'description' => 'Du kannst alle benötigten JavaScript-Dateien auch von deinem Server aus anbieten (siehe dazu die Doku).'],
			'max-image-size'    => ['label'=>'Max Bildgröße in px',                         'default' => '1024',             'hidden' => false, 'description' => 'Die maximale Höhe bzw. Breite des Bildes (je nachdem, was überschritten wird).'],
			'min-word-length'	=> ['label'=>'Min. Wort länge',                             'default' => 2,                  'hidden' => false, 'description' => 'Wie lange muss ein Wort mindestens sein, um gezählt zu werden?'],
			'min-word-occurence'=> ['label'=>'Min. Wordvorkommen',                          'default' => 2,                  'hidden' => false, 'description' => 'Wie oft muss ein Wort mindestens vorkommen, um in der Word Cloud gezeichnet zu werden?'],
			'black-list'     	=> ['label'=>'Stopwörter(Ignorierte Worte)',                'default' => $noise,             'valid' => 'text','hidden' => false, 'description' => 'Wörter (z.B. Funktionswörter), die beim Zählen ignoriert werden sollen. Die Wörter werden hier mit Leerzeichen getrennt angegeben.'],
			'enable-black-list'	=> ['label'=>'Stopwörter verwenden',                        'default' => 1,                  'valid' => 'bool','hidden' => false, 'description' => 'Nutze die Blacklist.'],
			'enable-custom-black-list'	=> ['label'=>'Benutzern erlauben, Worte zu ignorieren', 'default' => 1,              'valid' => 'bool', 'hidden' => false, 'description' => 'Soll der Nutzer Wörter per Klick aus der Wortcloud entfernen können?'],
			'persistent-custom-black-list'	=> ['label'=>'Ignorierte Worte merken',         'default' => 1,                  'valid' => 'bool','hidden' => false, 'description' => 'Bleibt die benutzerdefinierte Blacklist erhalten, wenn der Nutzer einen neuen Text hinzufügt?'],
			'ignore-chars'		=> ['label'=>'Ignrierte Zeichen (regulärer Ausdruck)',      'default' => '\(\)\[\]\,\.;',    'hidden' => false, 'description' => 'Regulärer Ausdruck um bestimmte Zeichen beim Zählen von Wörtern zu ignorieren.'],
			'text-transform'	=> ['label'=>'Groß- / Kleinschreibung?',                    'default' => 'uppercase',        'valid' => ['uppercase', 'lowercase', 'none'], 'hidden' => false, 'description' => 'Sollen alle Wörter groß- oder kleingeschrieben werden?'],
			'min-alpha'			=> ['label'=>'Min. Alpha Wert (Transparenz)',               'default' => 0.1,                'hidden' => false, 'description' => 'Der Mindestwert für die Transparenz der Wörter. Setze den Wert auf 1 für gar keine Transparenz.'],
			'size-factor'		=> ['label'=>'Multiplikator für Schriftgröße',              'default' => 100,                'hidden' => false, 'description' => 'Mit diesem Wert kannst du die Größe der Wörter beeinflussen. Je kleiner der Wert, desto größer die Wörter in der WordCloud.'],
			'canvas-width'		=> ['label'=>'Canvas-Breite',                               'default' => '1024px',           'hidden' => false, 'description' => 'Lege die Breite des Canvas fest.'],
			'canvas-height'		=> ['label'=>'Canvas-Höhe',                                 'default' => '800px',            'hidden' => false, 'description' => 'Lege die Höhe des Canvas fest.'],
			'background-color'	=> ['label'=>'Hintergrundfarbe',                            'default' => 'rgba(255,255,255,0)', 'hidden' => false, 'description' => 'Der Hintergrund des Canvas. Nutze entweder die rgba() oder Hex-Angabe.'],
			'color'	            => ['label'=>'Farbschema',                                  'default' => 'black',            'valid' => ['black', 'red', 'green', 'blue', 'orange', 'random-dark', 'turkey', 'violett', 'random-light'], 'hidden' => false, 'description' => 'Farbe der Wörter als CSS. Alternativ random-dark oder random-light für zufällige Farben.'],
			'grid-size'			=> ['label'=>'Wortabstand',                                 'default' => 1,                  'hidden' => false, 'description' => 'Hiermit kannst du die Abstände zwischen den Wörtern erhöhen.'],
			'font-family'		=> ['label'=>'Schriftart',                                  'default' => 'Arial, sans-serif','hidden' => false, 'description' => 'Die CSS-Angabe für die verwendete Schriftart.'],
			'min-size'			=> ['label'=>'Min. Schriftgröße',                           'default' => 1,                  'hidden' => false, 'description' => 'Wie groß muss ein Wort sein, um in der WordCloud angezeigt zu werden?'],
			'font-weight'		=> ['label'=>'Schriftstärke',                               'default' => 'bold',             'hidden' => false, 'description' => 'Das Gewicht der Wörter (bold, normal oder z.B. als Ziffer: 100)'],
			'min-rotation'		=> ['label'=>'Min. Wort Drehung (Grad)',                    'default' => 0,                  'hidden' => false, 'description' => 'Um wieviel Rad sollen die Wörter mindestens gedreht werden?'],
			'max-rotation'		=> ['label'=>'Max. Wort Drehung (Grad)',                    'default' => 0,                  'hidden' => false, 'description' => 'Um wieviel Rad sollen die Wörter höchstens gedreht werden?'],
			'rotate-ratio'		=> ['label'=>'Verhältnis gedrehte Worte',                   'default' => 0,                  'hidden' => false, 'description' => 'Mit welcher Wahrscheinlichkeit sollen Wörter gedreht werden? (1 - alle, 0 - keine Wörter werden gedreht)'],
			'shape'				=> ['label'=>'Wolkenform',                                  'default' => 'circle',           'valid' => ['circle', 'cardioid', 'diamond', 'triangle', 'pentagon', 'star', 'square', 'triangle-forward'], 'hidden' => false, 'description' => 'Welche Form soll die WordCloud haben?'],
			'draw-out-of-bound'	=> ['label'=>'Erlaube über den Rand zu zeichnen',           'default' => 1,                  'valid' => 'bool','hidden' => false, 'description' => 'Sollen auch Wörter dargestellt werden, die nicht mehr auf die Zeichenfläche passen?'],
			'shrink-to-fit'	    => ['label'=>'In Zeichenfläche zwingen',                    'default' => 0,                  'valid' => 'bool','hidden' => false, 'description' => 'Verkleinere das Wort, damit es auf die Zeichenfläche passt?'],
			'shuffle'			=> ['label'=>'Zufallspositionen',                           'default' => 1,                  'valid' => 'bool','hidden' => false, 'description' => 'Soll die Position der Wörter bei jedem Durchlauf neu durchgemischt werden?'],
			'ellipticity'		=> ['label'=>'Elliptisches Verhältnis',                     'default' => 1,                  'hidden' => false, 'description' => 'Wie elliptisch soll die WordCloud sein (0 - flach, 1 - kreisförmig)'],
			'clear-canvas'		=> ['label'=>'Stets neu zeichenen?',                        'default' => 1,                  'valid' => 'bool','hidden' => false, 'description' => 'Soll die Zeichenfläche vor jedem Durchlauf neu gezeichnet werden?'],
			'debug'     		=> ['label'=>'Debugmodus',                      'default' => 0,                  'valid' => 'bool','hidden' => false, 'description' => 'Wenn du Probleme mit dem Plugin hast, kannst du hier die Ausgabe von zusätzlichen Informationen in der Konsole des Browsers aktivieren.'],

			'id'				=> ['label'=>'',                        'default' => "1",                   'hidden' => true, 'description' => 'Id die für die Word Cloud verwendet wird. Muss auf Seitenebene eindeutig sein.'],
			'list'				=> ['label'=>'',                        'default' => [],                    'hidden' => true, 'description' => 'Enthält die Liste gezählter Wörter.'],
			'data'				=> ['label'=>'',                        'default' => NULL,                  'hidden' => true, 'description' => 'Text oder gezählte Wörter.']

		];

	}
	static function wp_word_cloud_register_settings() {

		foreach (self::wp_word_cloud_get_global_settings() as $name => $setting) {
			$value = apply_filters('rpi_word_cloud_settings',$setting['default'],$name);
            add_option(self::$prefix.$name, $value);
			register_setting( 'rpi_word_cloud_settings', self::$prefix.$name);
		}


	}
	function wp_word_cloud_register_options_page() {

		add_options_page('rpi Word-Cloud', 'rpi Word-Cloud', 'manage_options', 'rpi-wordcloud', ['rpiWordCloudSettings','wp_word_cloud_options_page']);

	}

	static function wp_word_cloud_options_page() {
		// TODO: Pretify Settings Page
		?>
        <div>
			<?php screen_icon(); ?>
            <h2>WordCloud</h2>
            <p>Hier kannst du die Standardeinstellungen anpassen.</p>
            <form method="post" action="options.php">
				<?php settings_fields( 'rpi_word_cloud_settings' ); ?>
                <h3>Einstellungen</h3>
                <table>
					<?php



					foreach (self::wp_word_cloud_get_global_settings() as $name => $value) {

                        if(in_array($name,self::$hide_settings)){
                            continue;
                        }

						//echo '<tr><td>'.is_bool(get_option(self::$prefix.$name)).'</td></tr>';
						if ($value['hidden'] === FALSE) {

							echo '<tr valign="top"><td scope="row">';
							echo '<label for="'.$name.'"><strong>'.$value['label'].'</strong></label>';
							echo '</td><td>';

							// add select input if it's a limited option

							if (isset($value['valid'])) {

								if (is_array($value['valid'])) {

									echo '<select name="'.self::$prefix.$name.'" size=1>';
									foreach ($value['valid'] as $key => $option) {
										echo '<option value="'.$option.'" '.selected(get_option(self::$prefix.$name), $option).'>'.$option.'</option>';

									}
									echo '</select>';

								} else if ($value['valid'] == 'text') {
									echo '<textarea id="'.$name.'" name="'.self::$prefix.$name.'">'.get_option(self::$prefix.$name).'</textarea>';

									// add checkbox if it is a true/false option
								} else if ($value['valid'] == 'bool') {

									echo '<input type="checkbox" id="'.$name.'" value=1 name="'.self::$prefix.$name.'" '.checked(1, get_option(self::$prefix.$name), false).'>';

								}

								// if no valid-option is given, just handle this as a text option
							} else {

								echo '<input type="text" id="'.$name.'" name="'.self::$prefix.$name.'" value="'.esc_attr(get_option(self::$prefix.$name)).'"';

							}

							echo '</td><td>';
							echo $value['description'].' ('.$name.')';
							echo '</td></tr>';

						}

					}
					?>
                </table>
				<?php  submit_button(); ?>
            </form>
        </div>
		<?php
	}

}
new rpiWordCloudSettings();
