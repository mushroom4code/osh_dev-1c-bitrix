/* See additional vars in sources: \WD\Antirutin\Plugin::printJs() */

// Plugin loaded
$(document).delegate(hid, 'pluginload', function(e){
	$('select', div).not('[data-no-style="Y"]').not('[data-select2-id]').each(function(){
		wdaSelect2(this);
	});
});

// Field loaded (on page loaded && on ajax refresh)
$(document).delegate(hid, 'fieldload', function(e){
	$('div[data-role="field_value"] select', this).each(function(){
		wdaSelect2(this);
	});
});

// Add value
$(document).delegate(hid+'_field_list div[data-role="field_add"] input[type=button]', 'click', function(e){
	e.preventDefault();
	var list = $(this).closest('div[data-role="field_list"]'),
		items = list.children('table[data-role="field_items"]').children('tbody').first(),
		item = items.children('tr[data-role="field_item"]').first(),
		newItem = item.clone(),
		newItemHtml = newItem.get(0).outerHTML;
	newItem.remove();
	//
	newItemHtml = newItemHtml.replace(/rand_id_([A-z0-9]{32})/g, 'rand_id_'+wdaGenerateString());
	newItem = $(newItemHtml);
	//
	$('select', newItem).each(function(){
		$(this).removeAttr('data-select2-id tabindex aria-hidden class').next('.select2').remove();
		$(this).find('option').prop('selected', false).removeAttr('selected').removeAttr('data-select2-id');
		$(this).replaceWith($(this).clone().wrap('<p>').parent().html());
	});
	//
	$('input, textarea', newItem).not('[type=button],[type=submit],[type=reset],[type=checkbox],[type=radio],[type=hidden]')
		.val('').trigger('change');
	$('textarea', newItem).val('').trigger('change');
	$('input[type=checkbox]', newItem).removeAttr('checked').trigger('change');
	// Select2
	$('select', newItem).each(function(){
		wdaSelect2(this);
	});
	//
	$('div,span', newItem).filter('[data-clear="Y"]').text('');
	//
	items.append(newItem);
	//
	BX.ready();
});

// Delete value
$(document).delegate(hid+'_field_list td[data-role="field_delete"] input[type=button]', 'click', function(e){
	e.preventDefault();
	$(this).closest('tr').remove();
});

// Change field
$(document).delegate(hid+'_select select', 'change', function(e){
	e.preventDefault();
	var field = $(this).val(),
		data = {
			plugin: getPlugin(id),
			plugin_id: id,
			field: field
		};
	wdaAjax(['custom_ajax', 'load_field_input'], data, function(jqXHR, textStatus, arJson){
		if(arJson.Success){
			var div = $(hid);
			$('div[data-role="field_value"]', div).html(arJson['Html']);
			div.trigger('fieldload');
			wdaReplaceCheckboxes(div);
		}
		else{
			wdaPopupError.Open(jqXHR);
		}
	}, function(jqXHR, textStatus, errorThrown){
		wdaPopupError.Open(jqXHR);
	}, false);
});

// Refresh field
$(document).delegate(hid+' [data-role="plugin_form_refresh"]', 'click', function(e){
	e.preventDefault();
	$(this).parent().find('select').trigger('change');
});

// Replace text, for prevent elements titles like a 'Element name &quot;1&quot;'
$(document).delegate(hid+' [data-role="element_link_value"]', 'change', function(e){
	var input = this;
	setTimeout(function(){
		var span = $(input).parent().find('[data-role="element_link_title"]'),
			text = span.text(),
			title = $('<textarea/>').html(text).text();
		span.text(title);
	}, 10);
});

// Check fields on start
wdaOnStartHandler(id, function(id, div, title){
	if(!$('select[data-role="field"]', div).val()){
		return $('input[data-role="error_no_field"]', div).val();
	}
	return true;
});
