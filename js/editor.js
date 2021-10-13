

wp.hooks.addAction( 'lzb.components.PreviewServerCallback.onChange', 'rpi.blocks.wordcloud', function ( props ) {

	if(props.block != 'lazyblock/wordcloud'){
		return;
	}

	var settingsScript = document.getElementById('word-cloud-settings-'+props.attributes.blockId);

	if(! settingsScript) return;

	var wpWordCloudSettings_row = settingsScript.innerHTML;

	var container = document.getElementById('word-cloud-container-'+props.attributes.blockId);

	var wpWordCloudSettings = JSON.parse(wpWordCloudSettings_row);
	wpWordCloudSettings.data = props.attributes.source;
	wpWordCloudSettings.colors = props.attributes.color;

	window[container.getAttribute('settings')] = wpWordCloudSettings;


	wpWordCloudSettings = getWordCloudSettings(container);



	container.setAttribute('blockeditor','active');

	jQuery('#'+container.id).append('<input style="display:none;" checked type="checkbox" id="word-cloud-activate-black-list-'+wpWordCloudSettings.id+'" name="word-cloud-activate-black-list-'+wpWordCloudSettings.id+'">');


	wpWordCloudSettings.customBlackList = wpWordCloudBlock.blacklist(wpWordCloudSettings);

	wpWordCloudSettings.list = wpWordCloudBlock.count(wpWordCloudSettings);

	wpWordCloudSettings.maxWeight = wpWordCloudBlock.maxcount(wpWordCloudSettings);

	wpWordCloudSettings.colors = wpWordCloudSettings.color;

	wpWordCloudSettings = wpWordCloudBlock.setcolor(wpWordCloudSettings);


	var canvasId = 'word-cloud-'+wpWordCloudSettings.id;

	console.log(wpWordCloudSettings);

	jQuery('#'+container.id).append('<canvas id="'+canvasId+'" class="word-cloud" style="width: 100%" height="'+wpWordCloudSettings.canvasHeight+'" width="'+wpWordCloudSettings.canvasWidth+'"></canvas>');


	WordCloud(document.getElementById('word-cloud-' + wpWordCloudSettings.id),wpWordCloudSettings);

	document.getElementById('word-cloud-settings-'+props.attributes.blockId).remove();



} );
