/**
 * Copyright (c) 2018 nickyreinert
 * Released under the MIT license
 * @author Niki Reinert
 * @see https://github.com/nickyreinert/wordCloud-for-Wordpress/blob/wordCloud-for-wordPress-2/js/wpWordCloud.js
 * recoded by Joachim Happel
 */

(function ($) {

	'use strict';


	// init
	// go through all word cloud containers
	// to receive word cloud settings
	$(".word-cloud-container").each(function () {

		var wpWordCloudSettings = getWordCloudSettings(this);

		wpWordCloudSettings.counted_words=[];

		wpwc(wpWordCloudSettings, "Read settings");


		if (wpWordCloudSettings.data == null && wpWordCloudSettings.list == null) {

			wpwc(wpWordCloudSettings, "Error: No text found.");

			wpWordCloudSettings.data = 'Kein Text übermittelt. Bitte prüfe die Einstellungen im Backend.';

			wpWordCloudSettings.countWords = 0;

		}

		if (wpWordCloudSettings.list == null && wpWordCloudSettings.countWords != 1) {

			wpWordCloudSettings.countWords = 1;

			wpwc(wpWordCloudSettings, "%cAchtung: count-words ist nicht aktiviert und wird automatisch auf 1 gesetzt.", 1);

		}

		// add canvas and / or html
		$(this).append('<div class="word-cloud-controller"></div>');

		if (wpWordCloudSettings.style == 'html') {

			$(this).append('<div class="word-cloud" style="position: relative; height: '+wpWordCloudSettings.canvasHeight+'; width: '+wpWordCloudSettings.canvasWidth+';" id="word-cloud-html-'+wpWordCloudSettings.id+'"></div>');
			$(this).append('<canvas class="word-cloud" style="width: 100%; display: none;" height="'+wpWordCloudSettings.canvasHeight+'" width="'+wpWordCloudSettings.canvasWidth+'" id="word-cloud-'+wpWordCloudSettings.id+'"></canvas>');

		} else {
			$(this).append('<canvas class="word-cloud" style="width: 100%; max-width:100vh; max-height: 100vh;" height="'+wpWordCloudSettings.canvasHeight+'" width="'+wpWordCloudSettings.canvasWidth+'" id="word-cloud-'+wpWordCloudSettings.id+'"></canvas>');

		}

		wpwc(wpWordCloudSettings, "Added canvas");

		// add container where user can edit settings from frontend
		if (wpWordCloudSettings.enableFrontendEdit == true) {

			$(this).append('<table class="borderless" style="min-height: fit-content"><tr><td>'+
				'<label for="word-cloud-setting-min-word-occurence" class="word-cloud-setting-min-word-occurence-label">'+
				'Wortvorkommen:'+
				'</label>'+
				'<input type="range" max="10" min="1" step="1" value="' + wpWordCloudSettings.minWordOccurence + '" '+
					'class="change-word-cloud word-cloud-setting-min-word-occurence" dataid="'+wpWordCloudSettings.id+'" id="word-cloud-setting-min-word-occurence-'+wpWordCloudSettings.id+'" '+
					'name="word-cloud-setting-min-word-occurence-'+wpWordCloudSettings.id+'"></input>'+
				'</td><td>'+
				'<label for="word-cloud-setting-size-factor" class="word-cloud-setting-size-factor-label">Schriftgröße:</label>'+
				'<input type="range" max="300" min="30" step="1" value="' + wpWordCloudSettings.sizeFactor + '" '+
					'class="change-word-cloud word-cloud-setting-size-factor-" dataid="'+wpWordCloudSettings.id+'"  id="word-cloud-setting-size-factor-'+wpWordCloudSettings.id+'" '+
					'name="word-cloud-setting-size-factor-'+wpWordCloudSettings.id+'"></input>'+
				'</td></tr></table>');

			//$(this).append('<label for="word-cloud-setting-min-word-occurence" 	class="word-cloud-setting-min-word-occurence-label"	>min-word-occurence:</label> 	<input type="text" value="' + wpWordCloudSettings.minWordOccurence + '" class="word-cloud-setting-min-word-occurence" id="word-cloud-setting-min-word-occurence-'+wpWordCloudSettings.id+'" name="word-cloud-setting-min-word-occurence-'+wpWordCloudSettings.id+'"></input>');
			//$(this).append('<label for="word-cloud-setting-size-factor" 		class="word-cloud-setting-size-factor-label"		>size-factor:</label> 			<input type="text" value="' + wpWordCloudSettings.sizeFactor + '" class="word-cloud-setting-size-factor" id="word-cloud-setting-size-factor-'+wpWordCloudSettings.id+'" name="word-cloud-setting-size-factor-'+wpWordCloudSettings.id+'"></input>');

		}

		// add hover container
		// hiden on init
		$(this).append('<div class="word-cloud-tooltip" id="word-cloud-tooltip-'+wpWordCloudSettings.id+'"></div>');

		// force tooltop to disappear when mouse cursor leaves canvas
		$('#word-cloud-' + wpWordCloudSettings.id).mouseleave(function(){

			$('#word-cloud-tooltip-' + wpWordCloudSettings.id).hide();

		})


		if (wpWordCloudSettings.enableFrontendEdit == 1 || wpWordCloudSettings.enableOcr == 1) {

			$(this).append('<textarea class="word-cloud-text" id="word-cloud-text-'+wpWordCloudSettings.id+'" style="min-height:80px;"></textarea>');
			$('#word-cloud-text-'+wpWordCloudSettings.id).text(wpWordCloudSettings.data);



			wpwc(wpWordCloudSettings, "Added edit field");

		}
		// contains words clicked by user
		if (wpWordCloudSettings.enableCustomBlackList == 1) {

			$(this).append('<p id="word-cloud-black-list-' + wpWordCloudSettings.id + '"></p>');
			wpwc(wpWordCloudSettings, "Added black list container");
			if (wpWordCloudSettings.enableFrontendEdit == 1){
				$(this).append('<input checked type="checkbox" class="activate-black-list" id="word-cloud-activate-black-list-' + wpWordCloudSettings.id + '" name="word-cloud-activate-black-list-' + wpWordCloudSettings.id + '">');
				$(this).append('<label for="word-cloud-activate-black-list-' + wpWordCloudSettings.id + '">Ignorierte Worte ausblenden</label>');
			}else{
				$(this).append('<input checked type="checkbox" style="display:none;" id="word-cloud-activate-black-list-' + wpWordCloudSettings.id + '">');
			}

		}
		if (wpWordCloudSettings.enableFrontendEdit == 1 || wpWordCloudSettings.enableOcr == 1) {
			$(this).append('<button class="render-word-cloud button" id="render-word-cloud-'+wpWordCloudSettings.id+'">Wortwolke aktualisieren</button>');

		}
		if (wpWordCloudSettings.countWords == 1) {

			wpWordCloudSettings.list = countWords(wpWordCloudSettings);
			wpwc(wpWordCloudSettings, "Counted words");
		}

		if (wpWordCloudSettings.debug == 1) {

			console.log({"WP WordCloud Words" : wpWordCloudSettings.list});

		}


		wpWordCloudSettings.maxWeight = getMaxWeight(wpWordCloudSettings);

		wpWordCloudSettings.colors=wpWordCloudSettings.color;

		wpWordCloudSettings = setWcCallbacks(wpWordCloudSettings);

		if (wpWordCloudSettings.style == 'html') {


			WordCloud(
				[$('#word-cloud-' + wpWordCloudSettings.id)[0],
				$('#word-cloud-html-' + wpWordCloudSettings.id)[0]],
				wpWordCloudSettings);

		} else {
			wpwc(wpWordCloudSettings, 'start WordCloud');
			WordCloud(
				$('#word-cloud-' + wpWordCloudSettings.id)[0],
				wpWordCloudSettings);

		}


	});

	$('.activate-black-list').click(function() {

		var wpWordCloudSettings = getWordCloudSettings($(this).parent()[0]);

		if (wpWordCloudSettings.persistentCustomBlackList == 0) {

			$('#word-cloud-black-list-' + wpWordCloudSettings.id).children().remove();

		}

		wpWordCloudSettings.customBlackList = getCustomBlackList(wpWordCloudSettings);

		if (wpWordCloudSettings.enableFrontendEdit == 1) {

			wpWordCloudSettings.data = $('#word-cloud-text-'+wpWordCloudSettings.id).val();

		}

		wpWordCloudSettings.list = countWords(wpWordCloudSettings);

		wpWordCloudSettings.maxWeight = getMaxWeight(wpWordCloudSettings);

		wpWordCloudSettings.colors=wpWordCloudSettings.color;

		wpWordCloudSettings = setWcCallbacks(wpWordCloudSettings);

		WordCloud($('#word-cloud-' + wpWordCloudSettings.id)[0], wpWordCloudSettings);

	})

	$('.render-word-cloud').click(function(e) {

		var wpWordCloudSettings = getWordCloudSettings(e.target.parentNode);

		if (wpWordCloudSettings.persistentCustomBlackList == 0) {

			$('#word-cloud-black-list-' + wpWordCloudSettings.id).children().remove();

		}

		wpWordCloudSettings.customBlackList = getCustomBlackList(wpWordCloudSettings);

		wpWordCloudSettings.data = $('#word-cloud-text-'+wpWordCloudSettings.id).val();

		wpWordCloudSettings.list = countWords(wpWordCloudSettings);

		wpWordCloudSettings.maxWeight = getMaxWeight(wpWordCloudSettings);

		wpWordCloudSettings.colors=wpWordCloudSettings.color;

		wpWordCloudSettings = setWcCallbacks(wpWordCloudSettings);

		WordCloud($('#word-cloud-' + wpWordCloudSettings.id)[0], wpWordCloudSettings);

	});
	$('.change-word-cloud').change(function(e) {

		var elem = document.getElementById('word-cloud-container-'+e.target.getAttribute('dataid'));

		var wpWordCloudSettings = getWordCloudSettings(elem);

		if (wpWordCloudSettings.persistentCustomBlackList == 0) {

			$('#word-cloud-black-list-' + wpWordCloudSettings.id).children().remove();

		}

		wpWordCloudSettings.customBlackList = getCustomBlackList(wpWordCloudSettings);

		wpWordCloudSettings.data = $('#word-cloud-text-'+wpWordCloudSettings.id).val();

		wpWordCloudSettings.list = countWords(wpWordCloudSettings);

		wpWordCloudSettings.maxWeight = getMaxWeight(wpWordCloudSettings);

		wpWordCloudSettings.colors=wpWordCloudSettings.color;

		wpWordCloudSettings = setWcCallbacks(wpWordCloudSettings);

		WordCloud($('#word-cloud-' + wpWordCloudSettings.id)[0], wpWordCloudSettings);

	});

	function addWordToBlackList(item, settings) {


		wpwc(settings, "User added word to ingore list.");


		// add word to black list below the word cloud
		$('#word-cloud-black-list-'+settings.id).append('<span count='+item[1]+' class="black-list-item"><span class="black-list-word">' + item[0] + '</span><span class="black-list-word-removal">&#x2A2F;</span></span>');

		settings.customBlackList = getCustomBlackList(settings);

		settings.list = countWords(settings);

		settings.maxWeight = getMaxWeight(settings);

		settings.color = settings.colors;

		settings = setWcCallbacks(settings);

		WordCloud($('#word-cloud-' + settings.id)[0], settings);

	}

	// add trigger so user can remove words from black list
	$(document).on("click", "span.black-list-item" , function(e) {

		// if user clicks on word below word cloud canvas
		// it will be removed from black list

		var settings = getWordCloudSettings( $(e.currentTarget).parent().parent()[0] );

		$(e.currentTarget).remove();

		settings.customBlackList = getCustomBlackList(settings);

		settings.list = countWords(settings);

		settings.maxWeight = getMaxWeight(settings);

		settings.colors = settings.color;

		settings = setWcCallbacks(settings);

		WordCloud($('#word-cloud-' + settings.id)[0], settings);

	});


	function getCustomBlackList(settings) {

		var blackList = {};

		if ($("#word-cloud-activate-black-list-"+settings.id).is(":checked")) {

			$('#word-cloud-black-list-' + settings.id).children().each(function(){

				var count = $(this).attr('count');
				var word = $(this).find('.black-list-word').html();

				blackList[word] = count;

			})

		}

		return blackList;

	}

	function countWords(settings) {



		var cleanText = settings.data.replace(new RegExp(settings.ignoreChars, 'gim'), '');

		cleanText = (settings.textTransform == 'uppercase')
			? cleanText.toUpperCase()
			: cleanText.toLowerCase();

		var textArray = cleanText.split(' ');



		settings.list = {};

		var purgedTextArray = []

		// first remove stopp words
		$.each(textArray, function(index, word) {

			if ((
				typeof(settings.customBlackList[word]) === 'undefined' &&
				!settings.blackList.includes(word)
				) ||
				!$("#word-cloud-activate-black-list-"+settings.id).is(":checked")

			) {

				purgedTextArray.push(word);

			}

		})

		var wordList = {};

		var wordCount = Object.keys(purgedTextArray).length;


		// second: count words
		$.each(purgedTextArray, function(index, word) {

			if (word.length >= settings.minWordLength) {
				if (word in wordList) {
					wordList[word]['abs'] += 1
				} else {

					wordList[word] = {
						'abs' : 1,
					};
				}
			}
		});


		// third: send values to result

		$.each(wordList, function(index, word) {

			settings.list[index] = word['abs'];

		})

		settings.counted_words = wordList;


		return prepareWordList(settings);

	}

	function getMaxWeight(settings) {

		var maxWeight = 0;

		$.each(settings.list, function(index, wordCount){

			if (wordCount[1] > maxWeight) {

				maxWeight = wordCount[1];

			}
		});

		return maxWeight;

	}

	function setWcCallbacks(settings) {

		if(settings.color != "random-dark" && settings.color != "random-light"){




			// pass function to color option, based on the weight of the word
			settings.color = function (word, weight, fontSize, radius, theta) {


				var alpha = weight / settings.maxWeight
				if (alpha < settings.minAlpha) {
					alpha = settings.minAlpha;
				}


				var colors = [0,0,0];
				switch(settings.colors){
					case 'red':
						colors = [200,0,0];
						break;
					case 'green':
						colors = [0,200,0];
						break;
					case 'blue':
						colors = [0,0,200];
						break;
					case 'orange':
						colors = [200,100,0];
						break;
					case 'random-dark':
						colors = [100,50,100];
						break;
					case 'random-light':
						colors = [255,255,255];
						break;
					case 'turkey':
						colors = [0,150,200];
						break;
					case 'violett':
						colors = [100,0,100];
						break;

				}

				var r = Math.floor(Math.random() * colors[0]);
				var g = Math.floor(Math.random() * colors[1]);
				var b = Math.floor(Math.random() * colors[2]);

				return "rgba("+r+","+g+","+b+"," + alpha + ")";

			};

		}

		settings.weightFactor = function (size) {

			// if size factor is not set (=0), then use the canvas width

			if (settings.sizeFactor == 0) {

				return (

					($('#word-cloud-'+settings.id).width() * 0.1) * (size / (1 + size))

				)

			} else {

				return settings.sizeFactor * (size / (size+2));

			}

		};


		// if user clicks a word, it will be removed from the list and added to
		// an ignore list
		settings.click = function (item, dimension, event) {

			if ($("#word-cloud-activate-black-list-"+settings.id).is(":checked")) {

				if('active' != $("#word-cloud-container-"+settings.id).attr('blockeditor')){

					addWordToBlackList(item, settings);
				}
			}

		};

		settings.hover = function (item, dimension, event) {

			if (item != undefined) {

				let counts = settings.counted_words[item[0]].abs;

				$('#word-cloud-tooltip-' + settings.id).text(counts);

				$('#word-cloud-tooltip-' + settings.id).toggle();

				$('#word-cloud-tooltip-' + settings.id).css({left: event.pageX - 10 - $('#word-details-' + settings.id).width(), top: event.pageY - $('#word-cloud-tooltip-' + settings.id).height()});

			}

		};

		return settings

	}

	function prepareWordList(settings) {

		var preparedWordList = [];

		$.each(settings.list, function(word, count){

			if (count >= settings.minWordOccurence) {

				preparedWordList.push([word, count]);

			}

		});

		// in order to start with the most important word in the center, sort the array
		// thanks to https://stackoverflow.com/a/5200010
		//loop hangs on minWordOccurence = 1

		preparedWordList.sort(function(a, b) {
    			a = a[1];
    			b = b[1];

    			return a > b ? -1 : (a < b ? 1 : 0);
		});

		return preparedWordList;

	}

	// log function
	function wpwc(wpWordCloudSettings, message, error = 0){

		if (wpWordCloudSettings.debug == 1) {

			console.log("[WP WordCloud] " + message);

		}

		if (error > 0) {

			console.warn("[WP WordCloud] " + message, 'color: red;');

		}

	}

	window['wpWordCloudBlock']=
	{
		count: countWords,
		maxcount: getMaxWeight,
		setcolor: setWcCallbacks,
		blacklist: getCustomBlackList
	};

})(jQuery);



