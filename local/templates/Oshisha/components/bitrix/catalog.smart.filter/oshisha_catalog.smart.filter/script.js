function JCSmartFilter(ajaxURL, viewMode, params)
{
	this.ajaxURL = ajaxURL;
	this.form = null;
	this.timer = null;
	this.cacheKey = '';
	this.cache = [];
	this.popups = [];
	this.viewMode = viewMode;
	this.countCheckboxFilter = 0;
	this.sectionCode = null;
	this.allFilterShowHideContent = '<div class="osh-circle d-inline-block"></div>' +
		' <div class="osh-circle d-inline-block"></div>' +
		' <div class="osh-circle d-inline-block"></div>';
	if (params && params.SEF_SET_FILTER_URL)
	{
		this.sef = true;
	}
}

JCSmartFilter.prototype.keyup = function(input)
{
	if(!!this.timer)
	{
		clearTimeout(this.timer);
	}
	this.timer = setTimeout(BX.delegate(function(){
		this.reload(input);
	}, this), 500);
};

JCSmartFilter.prototype.click = function(checkbox)
{
	if(BX(checkbox).id == 'hide_not_available_id') {
		if(BX(checkbox).checked) {
			BX(checkbox).value = 'Y';
		} else {
			BX(checkbox).value = 'L';
		}
	} else {
		if ($(checkbox).hasClass('brends_checkbox')) {
			if ($('input.check_input.brends_checkbox:checked').length > 0) {
				$('div.model_kalyana.smart-filter-parameters-box').removeClass('d-none');
			} else {
				$('div.model_kalyana.smart-filter-parameters-box').removeClass('d-none');
			}
		}
		if(BX(checkbox).checked) {
			this.addHorizontalFilter(checkbox);
		} else {
			this.removeHorizontalFilter(checkbox);
		}
	}

	if(!!this.timer)
	{
		clearTimeout(this.timer);
	}

	this.timer = setTimeout(BX.delegate(function(){
		this.reload(checkbox);
	}, this), 500);
};

JCSmartFilter.prototype.reload = function(input)
{
	if (this.cacheKey !== '')
	{
		//Postprone backend query
		if(!!this.timer)
		{
			clearTimeout(this.timer);
		}
		this.timer = setTimeout(BX.delegate(function(){
			this.reload(input);
		}, this), 1000);
		return;
	}
	this.cacheKey = '|';

	this.position = BX.pos(input, true);
	this.form = BX.findParent(input, {'tag':'form'});
	if (this.form)
	{
		var values = [];
		values[0] = {name: 'ajax', value: 'y'};
		this.gatherInputsValues(values, BX.findChildren(this.form, {'tag': new RegExp('^(input|select)$', 'i')}, true));
		for (var i = 0; i < values.length; i++)
			this.cacheKey += values[i].name + ':' + values[i].value + '|';
		var url = new URL(window.location.href);
		if (url.searchParams.get('sort_by'))
			values.push({name: 'sort_by', value:  url.searchParams.get('sort_by')});
		if (url.searchParams.get('sort_order'))
			values.push({name: 'sort_order', value:  url.searchParams.get('sort_order')});

		url.search = '';
		values.forEach((value, key) => {
			if (key !== 0 && value['name'] !== 'PAGEN_1') {
				url.searchParams.set(value['name'], value['value']);
			}
		});
		window.history.replaceState(null, null, url);
		if (this.cache[this.cacheKey])
		{
			this.curFilterinput = input;
			this.postHandler(this.cache[this.cacheKey], true);
		}
		else
		{
			this.curFilterinput = input;
			BX.ajax.loadJSON(
				this.ajaxURL,
				this.values2post(values),
				BX.delegate(this.postHandler, this)
			);
		}

		this.proxy()
	}
};

JCSmartFilter.prototype.clickSubCat = function (a) {
	this.sectionCode = BX(a).getAttribute('data-osh-url');
	this.proxy();
}

JCSmartFilter.prototype.allFilterShowHide = function ()
{
	const stateFilter = $('#osh-filter-horizontal-item').data('osh-filter-state'),
		filterCount = this.countCheckboxFilter - 3

	if (stateFilter == 'hide') {
		$('#osh-filter-horizontal-item').data('osh-filter-state', 'show')
		$('#osh-filter-horizontal-item-count').html('Скрыть')
	} else {
		$('#osh-filter-horizontal-item').data('osh-filter-state', 'hide')
		$('#osh-filter-horizontal-item-count').html(`${this.allFilterShowHideContent} + ${filterCount}`)
	}

	this.updateHorizontalFilter();
};

JCSmartFilter.prototype.updateHorizontalFilter = function()
{
	const allFilter = $('#osh-filter-horizontal-item div'),
		stateFilter = $('#osh-filter-horizontal-item').data('osh-filter-state'),
		filterRemoveAllOne = this.countCheckboxFilter == 0 ? 'd-none' : 'd-inline-block',
		filterRemoveAllTwo = this.countCheckboxFilter == 0 ? 'd-inline-block' : 'd-none',
		filterCount = this.countCheckboxFilter > 3 ? this.countCheckboxFilter - 3 : 0,
		filterCountAddClass = this.countCheckboxFilter > 3 ? 'd-inline-block' : 'd-none',
		filterCountRemoveClass = this.countCheckboxFilter > 3 ? 'd-none' : 'd-inline-block'

	if (stateFilter == 'show') {
		for (let i=0; i<allFilter.length; i++) {
			$(allFilter[i]).removeClass('d-none').addClass('d-inline-block')
		}
	} else {
		for (let i=0; i<allFilter.length; i++) {
			if(i > 2) {
				$(allFilter[i]).removeClass('d-inline-block').addClass('d-none')
			} else {
				$(allFilter[i]).removeClass('d-none').addClass('d-inline-block')
			}
		}
		$('#osh-filter-horizontal-item-count').html(
			`
				${this.allFilterShowHideContent}
				+ ${filterCount}
			`
		)
	}

	$('#osh-filter-horizontal-item-count').removeClass(filterCountRemoveClass).addClass(filterCountAddClass)

	$('#osh-filter-horizontal-item-remove').removeClass(filterRemoveAllTwo).addClass(filterRemoveAllOne)

};

JCSmartFilter.prototype.removeHorizontalFilterAll = function ()
{
	$('#osh-filter-horizontal-item > *').remove();

	const checkbox = $('form[name="_form"] input[type="checkbox"]:checked');
	const disabled = $('form[name="_form"] input[type="checkbox"]:disabled');

	$.each(disabled,function(i,val){
		$(val).removeAttr('disabled');
	});

	for (let i=0; i < checkbox.length; i++) {
		BX(checkbox[i]).checked = false;
		this.countCheckboxFilter--;
	}
	this.updateHorizontalFilter();
	this.proxy(true);
};

JCSmartFilter.prototype.removeHorizontalFilter = function(checkbox)
{
	const idFilter = BX(checkbox).getAttribute('id'),
		idFilterItem = `item-${idFilter}`
	BX.remove(BX(idFilterItem));
	this.countCheckboxFilter--
	this.updateHorizontalFilter()
	this.proxy();
};

JCSmartFilter.prototype.addHorizontalFilter = function(checkbox)
{
	const mainBlock = BX('osh-filter-horizontal-item'),
		idFilter = BX(checkbox).getAttribute('id'),
		labelsPropClass = idFilter.match(/[a-zA-Z]+_(\d)+/),
		idFilterItem = `item-${idFilter}`,
		nameFilter = BX.findChild(BX.findParent(checkbox, {'tag':'form'}), {
			attribute: {
				'for': idFilter
			}
		}, true).innerHTML;

	this.countCheckboxFilter++
	let currPropLabel = document.querySelectorAll('#' + idFilterItem);
	if (currPropLabel.length) {
		return;
	}

	BX.append(
		BX.create('div', {
			props: {
				id: idFilterItem,
				className: `osh-filter-item osh-filter-horizontal-border ${labelsPropClass[0]}`
			},
			html: nameFilter + `<span class='d-inline-block osh-filter-horizontal-remove'></span>`
		}),
		mainBlock
	);

	this.updateHorizontalFilter()

	BX.bind(BX(idFilterItem).querySelector('span'), 'click', (e) =>
	{
		BX.remove(BX(idFilterItem));
		BX(checkbox).checked = false;
		this.countCheckboxFilter--
		this.updateHorizontalFilter()
		this.reload(BX(checkbox));
		this.proxy();
		return BX.PreventDefault(e);
	});
};

JCSmartFilter.prototype.updateItem = function (PID, arItem, result)
{

	if (arItem['CODE'] == 'MODEL_KALYANA') {
		var brend_checked = false;
		var brends_id;
		Object.values(result.ITEMS).forEach(function(value, key) {
			if (value["CODE"] == "BREND") {
				brends_id = value["ID"];
			}
		});
		Object.values(result.ITEMS[brends_id]['VALUES']).forEach(function(value, key) {
			if (typeof value['CHECKED'] !== 'undefined') {
				brend_checked = true;
			}
		} );
	}
	if (arItem.PROPERTY_TYPE === 'N' || arItem.PRICE)
	{
		var trackBar = window['trackBar' + PID];
		if (!trackBar && arItem.ENCODED_ID)
			trackBar = window['trackBar' + arItem.ENCODED_ID];

		if (trackBar && arItem.VALUES)
		{
			if (arItem.VALUES.MIN)
			{
				if (arItem.VALUES.MIN.FILTERED_VALUE)
					trackBar.setMinFilteredValue(arItem.VALUES.MIN.FILTERED_VALUE);
				else
					trackBar.setMinFilteredValue(arItem.VALUES.MIN.VALUE);
			}

			if (arItem.VALUES.MAX)
			{
				if (arItem.VALUES.MAX.FILTERED_VALUE)
					trackBar.setMaxFilteredValue(arItem.VALUES.MAX.FILTERED_VALUE);
				else
					trackBar.setMaxFilteredValue(arItem.VALUES.MAX.VALUE);
			}
		}
	}
	else if (arItem.VALUES)
	{
		for (var i in arItem.VALUES)
		{
			if (arItem.VALUES.hasOwnProperty(i))
			{
				var value = arItem.VALUES[i];
				var control = BX(value.CONTROL_ID);
				if (!!control)
				{
					var label = document.querySelector('[data-role="label_'+value.CONTROL_ID+'"]');
					if (value.DISABLED)
					{

					}
					else
					{
						BX.adjust(control, {props: {disabled: false}});
						if (label){
							if (arItem['CODE'] == 'MODEL_KALYANA') {
								if (brend_checked == true) {
									$(control).closest('.smart-filter-parameters-box').removeClass('d-none');
									BX.removeClass(control.parentNode, 'd-none');
									BX.removeClass(label, 'd-none');
								} else {
									$(control).closest('.smart-filter-parameters-box').addClass('d-none');
									BX.addClass(control.parentNode, 'd-none');
									BX.addClass(label, 'd-none');
								}
							} else {
								$(control).closest('.smart-filter-parameters-box').removeClass('d-none');
								BX.removeClass(control.parentNode, 'd-none');
								BX.removeClass(label, 'd-none');
							}
						}
						else {
							if (arItem['CODE'] == 'MODEL_KALYANA') {
								if (brend_checked == true) {
									$(control).closest('.smart-filter-parameters-box').removeClass('d-none');
									BX.removeClass(control.parentNode, 'd-none');
								}
							} else {
								$(control).closest('.smart-filter-parameters-box').removeClass('d-none');
								BX.removeClass(control.parentNode, 'd-none');
							}
						}
					}

					if (value.hasOwnProperty('ELEMENT_COUNT'))
					{
						label = document.querySelector('[data-role="count_'+value.CONTROL_ID+'"]');
						if (label)
							label.innerHTML = value.ELEMENT_COUNT;
					}
				}
			}
		}
	}
};

JCSmartFilter.prototype.postHandler = function (result, fromCache)
{
	var hrefFILTER, url, curProp;
	if (!!result && !!result.ITEMS)
	{
		for(var popupId in this.popups)
		{
			if (this.popups.hasOwnProperty(popupId))
			{
				this.popups[popupId].destroy();
			}
		}
		this.popups = [];
		for(var PID in result.ITEMS)
		{
			if (result.ITEMS.hasOwnProperty(PID))
			{
				this.updateItem(PID, result.ITEMS[PID], result);
				if (Object.values(result.ITEMS[PID].VALUES).length) {
					if (!result.ITEMS[PID].VALUES.MIN) {
						var first_element_id = Object.values(result.ITEMS[PID].VALUES).filter(element => typeof element!==undefined).shift()['CONTROL_ID'];
						if ($('#'+first_element_id).closest('div.smart-filter-parameters-box').hasClass('bx-active')) {
							var filter_block = $('#'+first_element_id).closest('div.smart-filter-parameters-box').find("[data-role='bx_filter_block']");
							filter_block.css({'display':'block','height':'auto','overflow':''});
							filter_block.css('height', filter_block[0].offsetHeight+'px');
						}
					}
				}
			}
		}
	}


	if (!fromCache && this.cacheKey !== '')
	{
		this.cache[this.cacheKey] = result;
	}
	this.cacheKey = '';
};

JCSmartFilter.prototype.proxy = function(clearFilterAll = false)
{
	BX.cleanNode(window.JCCatalogSectionComponentThis.container);

	// data['action'] = 'initialLoad';
	var data = {};
	data['action'] = 'showMore';
	var values = [];
	values[0] = {name: 'ajax', value: 'y'};
	values[1] = {name: 'ajax_filter', value: 'y'};
	this.gatherInputsValues(values, BX.findChildren(this.form, {'tag': new RegExp('^(input|select)$', 'i')}, true));
	var url = new URL(window.location.href);
	if (url.searchParams.get('sort_by'))
		values.push({name: 'sort_by', value:  url.searchParams.get('sort_by')});
	if (url.searchParams.get('sort_order'))
		values.push({name: 'sort_order', value:  url.searchParams.get('sort_order')});
	if (clearFilterAll === true) {
		url.search = '';
		values.forEach(function callback(value, key) {
			if (key != 0 && key != 1) {
				if(value['name'] == 'PAGEN_1') {

				} else {
					url.searchParams.set(value['name'], value['value']);
				}
			}
		});
		window.history.replaceState(null, null, url);
	}
	for (var i = 0; i < values.length; i++)
		data[values[i].name] = values[i].value;
	data['PAGEN_' + 1] = 1;
	if (this.sectionCode) {
		data['subcat'] = this.sectionCode;
	}
	window.JCCatalogSectionComponent.prototype.sendRequestRefreshCatalog.call(window.JCCatalogSectionComponentThis, data);
	return false;
};

JCSmartFilter.prototype.bindUrlToButton = function (buttonId, url)
{
	var button = BX(buttonId);
	if (button)
	{
		if (button.type == 'submit')
			button.type = 'button';

		BX.bind(button, 'click', BX.proxy(this.proxy, this));
	}
};

JCSmartFilter.prototype.gatherInputsValues = function (values, elements)
{
	if(elements)
	{
		for(var i = 0; i < elements.length; i++)
		{
			var el = elements[i];
			if (el.disabled || !el.type)
				continue;

			switch(el.type.toLowerCase())
			{
				case 'text':
				case 'textarea':
				case 'password':
				case 'hidden':
				case 'number':
				case 'phone':
				case 'email':
				case 'select-one':
					if(el.value.length)
						values[values.length] = {name : el.name, value : el.value};
					break;
				case 'radio':
				case 'checkbox':
					if(el.checked)
						values[values.length] = {name : el.name, value : el.value};
					break;
				case 'select-multiple':
					for (var j = 0; j < el.options.length; j++)
					{
						if (el.options[j].selected)
							values[values.length] = {name : el.name, value : el.options[j].value};
					}
					break;
				default:
					break;
			}
		}
	}
};

JCSmartFilter.prototype.values2post = function (values)
{
	var post = [];
	var current = post;
	var i = 0;

	while(i < values.length)
	{
		var p = values[i].name.indexOf('[');
		if(p == -1)
		{
			current[values[i].name] = values[i].value;
			current = post;
			i++;
		}
		else
		{
			var name = values[i].name.substring(0, p);
			var rest = values[i].name.substring(p+1);
			if(!current[name])
				current[name] = [];

			var pp = rest.indexOf(']');
			if(pp == -1)
			{
				//Error - not balanced brackets
				current = post;
				i++;
			}
			else if(pp == 0)
			{
				//No index specified - so take the next integer
				current = current[name];
				values[i].name = '' + current.length;
			}
			else
			{
				//Now index name becomes and name and we go deeper into the array
				current = current[name];
				values[i].name = rest.substring(0, pp) + rest.substring(pp+1);
			}
		}
	}
	return post;
};

JCSmartFilter.prototype.hideFilterProps = function(element)
{
	var obj = element.parentNode,
		filterBlock = obj.querySelector("[data-role='bx_filter_block']"),
		propAngle = obj.querySelector("[data-role='prop_angle']");

	if(BX.hasClass(obj, "bx-active"))
	{
		filterBlock.style.overflow = "hidden";
		new BX.easing({
			duration : 300,
			start : { opacity: 100,  height: filterBlock.offsetHeight },
			finish : { opacity: 0, height:0 },
			transition : BX.easing.transitions.quart,
			step : function(state){
				filterBlock.style.opacity = state.opacity / 100;
				filterBlock.style.height = state.height + "px";
			},
			complete : function() {
				filterBlock.setAttribute("style", "");
				BX.removeClass(obj, "bx-active");
				BX.addClass(propAngle, "smart-filter-angle-down");
				BX.removeClass(propAngle, "smart-filter-angle-up");
			}
		}).animate();

	}
	else
	{
		filterBlock.style.display = "block";
		filterBlock.style.opacity = 0;
		filterBlock.style.height = "auto";
		filterBlock.style.overflow = "hidden";

		var obj_children_height = filterBlock.offsetHeight;
		filterBlock.style.height = 0;

		new BX.easing({
			duration : 300,
			start : { opacity: 0,  height: 0 },
			finish : { opacity: 100, height: obj_children_height },
			transition : BX.easing.transitions.quart,
			step : function(state){
				filterBlock.style.opacity = state.opacity / 100;
				filterBlock.style.height = state.height + "px";
			},
			complete : function() {
				filterBlock.style.overflow = "";
				BX.addClass(obj, "bx-active");
				BX.removeClass(propAngle, "smart-filter-angle-down");
				BX.addClass(propAngle, "smart-filter-angle-up");
			}
		}).animate();

	}
};

JCSmartFilter.prototype.showDropDownPopup = function(element, popupId)
{
	var contentNode = element.querySelector('[data-role="dropdownContent"]');
	this.popups["smartFilterDropDown"+popupId] = BX.PopupWindowManager.create("smartFilterDropDown"+popupId, element, {
		autoHide: true,
		offsetLeft: 0,
		offsetTop: 3,
		overlay : false,
		draggable: {restrict:true},
		closeByEsc: true,
		content: BX.clone(contentNode)
	});
	this.popups["smartFilterDropDown"+popupId].show();
};

JCSmartFilter.prototype.selectDropDownItem = function(element, controlId)
{
	this.keyup(BX(controlId));

	var wrapContainer = BX.findParent(BX(controlId), {className:"smart-filter-input-group-dropdown"}, false);

	var currentOption = wrapContainer.querySelector('[data-role="currentOption"]');
	currentOption.innerHTML = element.innerHTML;
	BX.PopupWindowManager.getCurrentPopup().close();
};

BX.namespace("BX.Iblock.SmartFilter");
BX.Iblock.SmartFilter = (function()
{
	/** @param {{
			leftSlider: string,
			rightSlider: string,
			tracker: string,
			trackerWrap: string,
			minInputId: string,
			maxInputId: string,
			minPrice: float|int|string,
			maxPrice: float|int|string,
			curMinPrice: float|int|string,
			curMaxPrice: float|int|string,
			fltMinPrice: float|int|string|null,
			fltMaxPrice: float|int|string|null,
			precision: int|null,
			colorUnavailableActive: string,
			colorAvailableActive: string,
			colorAvailableInactive: string
		}} arParams
	 */
	var SmartFilter = function(arParams)
	{
		if (typeof arParams === 'object')
		{
			this.leftSlider = BX(arParams.leftSlider);
			this.rightSlider = BX(arParams.rightSlider);
			this.tracker = BX(arParams.tracker);
			this.trackerWrap = BX(arParams.trackerWrap);

			this.minInput = BX(arParams.minInputId);
			this.maxInput = BX(arParams.maxInputId);

			this.minPrice = parseFloat(arParams.minPrice);
			this.maxPrice = parseFloat(arParams.maxPrice);

			this.curMinPrice = parseFloat(arParams.curMinPrice);
			this.curMaxPrice = parseFloat(arParams.curMaxPrice);

			this.fltMinPrice = arParams.fltMinPrice ? parseFloat(arParams.fltMinPrice) : parseFloat(arParams.curMinPrice);
			this.fltMaxPrice = arParams.fltMaxPrice ? parseFloat(arParams.fltMaxPrice) : parseFloat(arParams.curMaxPrice);

			this.precision = arParams.precision || 0;

			this.priceDiff = this.maxPrice - this.minPrice;

			this.leftPercent = 0;
			this.rightPercent = 0;

			this.fltMinPercent = 0;
			this.fltMaxPercent = 0;

			this.colorUnavailableActive = BX(arParams.colorUnavailableActive);//gray
			this.colorAvailableActive = BX(arParams.colorAvailableActive);//blue
			this.colorAvailableInactive = BX(arParams.colorAvailableInactive);//light blue

			this.isTouch = false;

			this.init();

			if ('ontouchstart' in document.documentElement)
			{
				this.isTouch = true;

				BX.bind(this.leftSlider, "touchstart", BX.proxy(function(event){
					this.onMoveLeftSlider(event)
				}, this));

				BX.bind(this.rightSlider, "touchstart", BX.proxy(function(event){
					this.onMoveRightSlider(event)
				}, this));
			}
			else
			{
				BX.bind(this.leftSlider, "mousedown", BX.proxy(function(event){
					this.onMoveLeftSlider(event)
				}, this));

				BX.bind(this.rightSlider, "mousedown", BX.proxy(function(event){
					this.onMoveRightSlider(event)
				}, this));
			}

			BX.bind(this.minInput, "keyup", BX.proxy(function(event){
				this.onInputChange();
			}, this));

			BX.bind(this.maxInput, "keyup", BX.proxy(function(event){
				this.onInputChange();
			}, this));
		}
	};

	SmartFilter.prototype.init = function()
	{
		var priceDiff;

		if (this.curMinPrice > this.minPrice)
		{
			priceDiff = this.curMinPrice - this.minPrice;
			this.leftPercent = (priceDiff*100)/this.priceDiff;

			this.leftSlider.style.left = this.leftPercent + "%";
			this.colorUnavailableActive.style.left = this.leftPercent + "%";
		}

		this.setMinFilteredValue(this.fltMinPrice);

		if (this.curMaxPrice < this.maxPrice)
		{
			priceDiff = this.maxPrice - this.curMaxPrice;
			this.rightPercent = (priceDiff*100)/this.priceDiff;

			this.rightSlider.style.right = this.rightPercent + "%";
			this.colorUnavailableActive.style.right = this.rightPercent + "%";
		}

		this.setMaxFilteredValue(this.fltMaxPrice);
	};

	SmartFilter.prototype.setMinFilteredValue = function (fltMinPrice)
	{
		this.fltMinPrice = parseFloat(fltMinPrice);
		if (this.fltMinPrice >= this.minPrice)
		{
			var priceDiff = this.fltMinPrice - this.minPrice;
			this.fltMinPercent = (priceDiff*100)/this.priceDiff;

			if (this.leftPercent > this.fltMinPercent)
				this.colorAvailableActive.style.left = this.leftPercent + "%";
			else
				this.colorAvailableActive.style.left = this.fltMinPercent + "%";

			this.colorAvailableInactive.style.left = this.fltMinPercent + "%";
		}
		else
		{
			this.colorAvailableActive.style.left = "0%";
			this.colorAvailableInactive.style.left = "0%";
		}
	};

	SmartFilter.prototype.setMaxFilteredValue = function (fltMaxPrice)
	{
		this.fltMaxPrice = parseFloat(fltMaxPrice);
		if (this.fltMaxPrice <= this.maxPrice)
		{
			var priceDiff = this.maxPrice - this.fltMaxPrice;
			this.fltMaxPercent = (priceDiff*100)/this.priceDiff;

			if (this.rightPercent > this.fltMaxPercent)
				this.colorAvailableActive.style.right = this.rightPercent + "%";
			else
				this.colorAvailableActive.style.right = this.fltMaxPercent + "%";

			this.colorAvailableInactive.style.right = this.fltMaxPercent + "%";
		}
		else
		{
			this.colorAvailableActive.style.right = "0%";
			this.colorAvailableInactive.style.right = "0%";
		}
	};

	SmartFilter.prototype.getXCoord = function(elem)
	{
		var box = elem.getBoundingClientRect();
		var body = document.body;
		var docElem = document.documentElement;

		var scrollLeft = window.pageXOffset || docElem.scrollLeft || body.scrollLeft;
		var clientLeft = docElem.clientLeft || body.clientLeft || 0;
		var left = box.left + scrollLeft - clientLeft;

		return Math.round(left);
	};

	SmartFilter.prototype.getPageX = function(e)
	{
		e = e || window.event;
		var pageX = null;

		if (this.isTouch && event.targetTouches[0] != null)
		{
			pageX = e.targetTouches[0].pageX;
		}
		else if (e.pageX != null)
		{
			pageX = e.pageX;
		}
		else if (e.clientX != null)
		{
			var html = document.documentElement;
			var body = document.body;

			pageX = e.clientX + (html.scrollLeft || body && body.scrollLeft || 0);
			pageX -= html.clientLeft || 0;
		}

		return pageX;
	};

	SmartFilter.prototype.recountMinPrice = function()
	{
		var newMinPrice = (this.priceDiff*this.leftPercent)/100;
		newMinPrice = (this.minPrice + newMinPrice).toFixed(this.precision);

		if (newMinPrice != this.minPrice)
			this.minInput.value = newMinPrice;
		else
			this.minInput.value = "";
		/** @global JCSmartFilter smartFilter */
		smartFilter.keyup(this.minInput);
	};

	SmartFilter.prototype.recountMaxPrice = function()
	{
		var newMaxPrice = (this.priceDiff*this.rightPercent)/100;
		newMaxPrice = (this.maxPrice - newMaxPrice).toFixed(this.precision);

		if (newMaxPrice != this.maxPrice)
			this.maxInput.value = newMaxPrice;
		else
			this.maxInput.value = "";
		/** @global JCSmartFilter smartFilter */
		smartFilter.keyup(this.maxInput);
	};

	SmartFilter.prototype.onInputChange = function ()
	{
		var priceDiff;
		if (this.minInput.value)
		{
			var leftInputValue = this.minInput.value;
			if (leftInputValue < this.minPrice)
				leftInputValue = this.minPrice;

			if (leftInputValue > this.maxPrice)
				leftInputValue = this.maxPrice;

			priceDiff = leftInputValue - this.minPrice;
			this.leftPercent = (priceDiff*100)/this.priceDiff;

			this.makeLeftSliderMove(false);
		}

		if (this.maxInput.value)
		{
			var rightInputValue = this.maxInput.value;
			if (rightInputValue < this.minPrice)
				rightInputValue = this.minPrice;

			if (rightInputValue > this.maxPrice)
				rightInputValue = this.maxPrice;

			priceDiff = this.maxPrice - rightInputValue;
			this.rightPercent = (priceDiff*100)/this.priceDiff;

			this.makeRightSliderMove(false);
		}
	};

	SmartFilter.prototype.makeLeftSliderMove = function(recountPrice)
	{
		recountPrice = (recountPrice !== false);

		this.leftSlider.style.left = this.leftPercent + "%";
		this.colorUnavailableActive.style.left = this.leftPercent + "%";

		var areBothSlidersMoving = false;
		if (this.leftPercent + this.rightPercent >= 100)
		{
			areBothSlidersMoving = true;
			this.rightPercent = 100 - this.leftPercent;
			this.rightSlider.style.right = this.rightPercent + "%";
			this.colorUnavailableActive.style.right = this.rightPercent + "%";
		}

		if (this.leftPercent >= this.fltMinPercent && this.leftPercent <= (100-this.fltMaxPercent))
		{
			this.colorAvailableActive.style.left = this.leftPercent + "%";
			if (areBothSlidersMoving)
			{
				this.colorAvailableActive.style.right = 100 - this.leftPercent + "%";
			}
		}
		else if(this.leftPercent <= this.fltMinPercent)
		{
			this.colorAvailableActive.style.left = this.fltMinPercent + "%";
			if (areBothSlidersMoving)
			{
				this.colorAvailableActive.style.right = 100 - this.fltMinPercent + "%";
			}
		}
		else if(this.leftPercent >= this.fltMaxPercent)
		{
			this.colorAvailableActive.style.left = 100-this.fltMaxPercent + "%";
			if (areBothSlidersMoving)
			{
				this.colorAvailableActive.style.right = this.fltMaxPercent + "%";
			}
		}

		if (recountPrice)
		{
			this.recountMinPrice();
			if (areBothSlidersMoving)
				this.recountMaxPrice();
		}
	};

	SmartFilter.prototype.countNewLeft = function(event)
	{
		var pageX = this.getPageX(event);

		var trackerXCoord = this.getXCoord(this.trackerWrap);
		var rightEdge = this.trackerWrap.offsetWidth;

		var newLeft = pageX - trackerXCoord;

		if (newLeft < 0)
			newLeft = 0;
		else if (newLeft > rightEdge)
			newLeft = rightEdge;

		return newLeft;
	};

	SmartFilter.prototype.onMoveLeftSlider = function(e)
	{
		if (!this.isTouch)
		{
			this.leftSlider.ondragstart = function() {
				return false;
			};
		}

		if (!this.isTouch)
		{
			document.onmousemove = BX.proxy(function(event) {
				this.leftPercent = ((this.countNewLeft(event)*100)/this.trackerWrap.offsetWidth);
				this.makeLeftSliderMove();
			}, this);

			document.onmouseup = function() {
				document.onmousemove = document.onmouseup = null;
			};
		}
		else
		{
			document.ontouchmove = BX.proxy(function(event) {
				this.leftPercent = ((this.countNewLeft(event)*100)/this.trackerWrap.offsetWidth);
				this.makeLeftSliderMove();
			}, this);

			document.ontouchend = function() {
				document.ontouchmove = document.touchend = null;
			};
		}

		return false;
	};

	SmartFilter.prototype.makeRightSliderMove = function(recountPrice)
	{
		recountPrice = (recountPrice !== false);

		this.rightSlider.style.right = this.rightPercent + "%";
		this.colorUnavailableActive.style.right = this.rightPercent + "%";

		var areBothSlidersMoving = false;
		if (this.leftPercent + this.rightPercent >= 100)
		{
			areBothSlidersMoving = true;
			this.leftPercent = 100 - this.rightPercent;
			this.leftSlider.style.left = this.leftPercent + "%";
			this.colorUnavailableActive.style.left = this.leftPercent + "%";
		}

		if ((100-this.rightPercent) >= this.fltMinPercent && this.rightPercent >= this.fltMaxPercent)
		{
			this.colorAvailableActive.style.right = this.rightPercent + "%";
			if (areBothSlidersMoving)
			{
				this.colorAvailableActive.style.left = 100 - this.rightPercent + "%";
			}
		}
		else if(this.rightPercent <= this.fltMaxPercent)
		{
			this.colorAvailableActive.style.right = this.fltMaxPercent + "%";
			if (areBothSlidersMoving)
			{
				this.colorAvailableActive.style.left = 100 - this.fltMaxPercent + "%";
			}
		}
		else if((100-this.rightPercent) <= this.fltMinPercent)
		{
			this.colorAvailableActive.style.right = 100-this.fltMinPercent + "%";
			if (areBothSlidersMoving)
			{
				this.colorAvailableActive.style.left = this.fltMinPercent + "%";
			}
		}

		if (recountPrice)
		{
			this.recountMaxPrice();
			if (areBothSlidersMoving)
				this.recountMinPrice();
		}
	};

	SmartFilter.prototype.onMoveRightSlider = function(e)
	{

		if (!this.isTouch)
		{
			this.rightSlider.ondragstart = function() {
				return false;
			};
		}

		if (!this.isTouch)
		{
			document.onmousemove = BX.proxy(function(event) {
				this.rightPercent = 100-(((this.countNewLeft(event))*100)/(this.trackerWrap.offsetWidth));
				this.makeRightSliderMove();
			}, this);

			document.onmouseup = function() {
				document.onmousemove = document.onmouseup = null;
			};
		}
		else
		{
			document.ontouchmove = BX.proxy(function(event) {
				this.rightPercent = 100-(((this.countNewLeft(event))*100)/(this.trackerWrap.offsetWidth));
				this.makeRightSliderMove();
			}, this);

			document.ontouchend = function() {
				document.ontouchmove = document.ontouchend = null;
			};
		}

		return false;
	};

	return SmartFilter;
})();
