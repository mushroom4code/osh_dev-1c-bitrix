/* See additional vars in sources: \WD\Antirutin\Plugin::printJs() */

// Check fields on start
wdaOnStartHandler(id, function(id, div, title){
	if(!$('select[data-role="stores"] option:not([value=""]):selected', div).length){
		return $('input[data-role="error_no_stores"]', div).val();
	}
	return true;
});
