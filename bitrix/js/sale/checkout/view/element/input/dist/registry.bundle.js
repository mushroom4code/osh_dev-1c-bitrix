this.BX = this.BX || {};
this.BX.Sale = this.BX.Sale || {};
this.BX.Sale.Checkout = this.BX.Sale.Checkout || {};
this.BX.Sale.Checkout.View = this.BX.Sale.Checkout.View || {};
this.BX.Sale.Checkout.View.Element = this.BX.Sale.Checkout.View.Element || {};
(function (exports,ui_type,ui_vue,main_core,main_core_events,sale_checkout_const,ui_entitySelector) {
	'use strict';

	ui_vue.BitrixVue.component('sale-checkout-view-element-input-product_item_quantity', {
	  props: ['item', 'index'],
	  data: function data() {
	    return {
	      quantity: this.item.quantity
	    };
	  },
	  methods: {
	    validate: function validate() {
	      main_core_events.EventEmitter.emit(sale_checkout_const.EventType.basket.inputChangeQuantityProduct, {
	        index: this.index
	      });
	    },
	    onKeyDown: function onKeyDown(e) {
	      if (['Enter'].indexOf(e.key) >= 0) {
	        this.$refs.container.blur();
	      }
	    }
	  },
	  computed: {
	    checkedClassObject: function checkedClassObject() {
	      return {
	        'checkout-item-quantity-field': true
	      };
	    }
	  },
	  // language=Vue
	  template: "\n      <input :class=\"checkedClassObject\" \n\t\t\t type=\"text\" \n\t\t\t inputmode=\"numeric\" \n             @blur=\"validate\"\n\t\t\t @keydown=\"onKeyDown\"\n             v-model=\"item.quantity\"\n             ref=\"container\"\n\t  />\n\t"
	});

	ui_vue.BitrixVue.component('sale-checkout-view-element-input-property-phone', {
	  props: ['item', 'index'],
	  methods: {
	    validate: function validate() {
	      main_core_events.EventEmitter.emit(sale_checkout_const.EventType.property.validate, {
	        index: this.index
	      });
	    },
	    onKeyDown: function onKeyDown(e) {
	      var value = e.key;
	      if (ui_type.PhoneFilter.replace(value) !== '') {
	        return;
	      }
	      if (['Esc', 'Delete', 'Backspace', 'Tab'].indexOf(e.key) >= 0) {
	        return;
	      }
	      if (e.ctrlKey || e.metaKey) {
	        return;
	      }
	      e.preventDefault();
	    },
	    onInput: function onInput() {
	      var value = ui_type.PhoneFormatter.formatValue(this.value);
	      if (this.value !== value) {
	        this.value = value;
	      }
	    }
	  },
	  computed: {
	    checkedClassObject: function checkedClassObject() {
	      return this.item.validated === sale_checkout_const.Property.validate.unvalidated ? {} : {
	        'is-invalid': this.item.validated === sale_checkout_const.Property.validate.failure,
	        'is-valid': this.item.validated === sale_checkout_const.Property.validate.successful
	      };
	    },
	    value: {
	      get: function get() {
	        return this.item.value;
	      },
	      set: function set(newValue) {
	        this.item.value = newValue;
	      }
	    }
	  },
	  // language=Vue
	  template: "\n      <input class=\"form-control form-control-lg\" :class=\"checkedClassObject\"\n             @blur=\"validate\"\n             @input=\"onInput\"\n\t\t\t @keydown=\"onKeyDown\"\n             v-model=\"value\"\n             autocomplete=\"tel\"\n\t\t\t :placeholder=\"item.name\"\n      />\n\t"
	});

	ui_vue.BitrixVue.component('sale-checkout-view-element-input-property-text', {
	  props: ['item', 'index', 'autocomplete'],
	  methods: {
	    validate: function validate() {
	      main_core_events.EventEmitter.emit(sale_checkout_const.EventType.property.validate, {
	        index: this.index
	      });
	    }
	  },
	  computed: {
	    checkedClassObject: function checkedClassObject() {
	      return this.item.validated === sale_checkout_const.Property.validate.unvalidated ? {} : {
	        'is-invalid': this.item.validated === sale_checkout_const.Property.validate.failure,
	        'is-valid': this.item.validated === sale_checkout_const.Property.validate.successful
	      };
	    }
	  },
	  // language=Vue
	  template: "\n        <input class=\"form-control form-control-lg\" :class=\"checkedClassObject\"\n            @blur=\"validate\"\n            type=\"text\" \n            :placeholder=\"item.name\"\n            :autocomplete=\"autocomplete\"\n            v-model=\"item.value\"\n        />\n\t"
	});

	ui_vue.BitrixVue.component('sale-checkout-view-element-input-property-number', {
	  props: ['item', 'index'],
	  methods: {
	    validate: function validate() {
	      main_core_events.EventEmitter.emit(sale_checkout_const.EventType.property.validate, {
	        index: this.index
	      });
	    },
	    onKeyDown: function onKeyDown(e) {
	      if (!isNaN(Number(e.key)) && e.key !== ' ' || ['Esc', 'Tab', 'Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', '.'].indexOf(e.key) >= 0) {
	        return;
	      }
	      if (e.ctrlKey || e.metaKey) {
	        return;
	      }
	      e.preventDefault();
	    },
	    onPaste: function onPaste(e) {
	      e.preventDefault();
	      var pastedText = e.clipboardData.getData('Text');
	      if (!isNaN(Number(pastedText))) {
	        this.item.value = pastedText.trim();
	      }
	    }
	  },
	  computed: {
	    checkedClassObject: function checkedClassObject() {
	      return {
	        'is-invalid': this.item.validated === sale_checkout_const.Property.validate.failure,
	        'is-valid': this.item.validated === sale_checkout_const.Property.validate.successful
	      };
	    }
	  },
	  // language=Vue
	  template: "\n\t\t<input\n\t\t\tclass=\"form-control form-control-lg\"\n\t\t\t:class=\"checkedClassObject\"\n\t\t\t@blur=\"validate\"\n\t\t\t@keydown=\"onKeyDown\"\n\t\t\t@paste=\"onPaste\"\n\t\t\ttype=\"text\"\n\t\t\tinputmode=\"numeric\"\n\t\t\t:placeholder=\"item.name\"\n\t\t\tv-model=\"item.value\"\n\t\t/>\n\t"
	});

	ui_vue.BitrixVue.component('sale-checkout-view-element-input-property-checkbox', {
	  props: ['item', 'index'],
	  data: function data() {
	    return {
	      'showValue': this.item.value === 'Y'
	    };
	  },
	  methods: {
	    validate: function validate() {
	      main_core_events.EventEmitter.emit(sale_checkout_const.EventType.property.validate, {
	        index: this.index
	      });
	    }
	  },
	  computed: {
	    checkedClassObject: function checkedClassObject() {
	      return {
	        'is-invalid': this.item.validated === sale_checkout_const.Property.validate.failure,
	        'is-valid': this.item.validated === sale_checkout_const.Property.validate.successful
	      };
	    },
	    switchValue: {
	      get: function get() {
	        this.showValue = this.item.value === 'Y';
	        return this.item.value === 'Y';
	      },
	      set: function set(value) {
	        if (value) {
	          this.item.value = 'Y';
	          this.showValue = true;
	        } else {
	          this.item.value = 'N';
	          this.showValue = false;
	        }
	      }
	    }
	  },
	  // language=Vue
	  template: "\n\t\t<div class=\"form-control form-control-lg border-0 pl-0\" :class=\"checkedClassObject\">\n\t\t\t<input\n\t\t\t\t@blur=\"validate\"\n\t\t\t\ttype=\"checkbox\"\n\t\t\t\t:id=\"item.name\"\n\t\t\t\t:value=\"showValue\"\n\t\t\t\tv-model=\"switchValue\"\n\t\t\t/>\n\t\t\t<label :for=\"item.name\" class=\"ml-2\">{{item.name}}</label>\n\t\t</div>\n\t"
	});

	ui_vue.BitrixVue.component('sale-checkout-view-element-input-property-date', {
	  props: ['item', 'index', 'autocomplete', 'isDateTime'],
	  methods: {
	    onClick: function onClick() {
	      this.showCalendar();
	      this.focusOnInput();
	    },
	    focusOnInput: function focusOnInput() {
	      var element = this.$el;
	      element.focus();
	    },
	    showCalendar: function showCalendar() {
	      var _this = this;
	      BX.calendar({
	        node: this.item.name,
	        field: this.item.name,
	        bTime: this.isDateTime,
	        bUseSecond: false,
	        callback_after: function callback_after(data) {
	          return _this.handleDate(data);
	        }
	      });
	    },
	    handleDate: function handleDate(date) {
	      var dateString = this.prepareDate(date);
	      this.changeValue(dateString);
	    },
	    prepareDate: function prepareDate(date) {
	      if (this.isDateTime === true) {
	        return date.toLocaleString([], {
	          day: '2-digit',
	          month: '2-digit',
	          year: 'numeric',
	          hour: 'numeric',
	          minute: 'numeric'
	        }).replace(',', '');
	      } else {
	        return date.toLocaleDateString().replace(',', '');
	      }
	    },
	    blur: function blur() {
	      this.changeValue(this.item.value);
	    },
	    changeValue: function changeValue(value) {
	      this.validate();
	      var changeValue = '';
	      if (main_core.Type.isStringFilled(value)) {
	        changeValue = this.validateDate(value) ? this.prepareDate(BX.parseDate(value)) : this.previousValue;
	      }
	      this.setDate(changeValue);
	    },
	    validateDate: function validateDate(value) {
	      var date = BX.parseDate(value);
	      return date && date.toLocaleDateString() !== 'Invalid Date';
	    },
	    validate: function validate() {
	      main_core_events.EventEmitter.emit(sale_checkout_const.EventType.property.validate, {
	        index: this.index
	      });
	    },
	    setDate: function setDate(date) {
	      this.item.value = date;
	      this.previousValue = date;
	    }
	  },
	  computed: {
	    checkedClassObject: function checkedClassObject() {
	      return {
	        'is-invalid': this.item.validated === sale_checkout_const.Property.validate.failure,
	        'is-valid': this.item.validated === sale_checkout_const.Property.validate.successful
	      };
	    }
	  },
	  // language=Vue
	  template: "\n\t\t<input \n\t\t\tclass=\"form-control form-control-lg\" \n\t\t\t:class=\"checkedClassObject\"\n\t\t\t@blur=\"blur\"\n\t\t\ttype=\"text\"\n\t\t\tinputmode=\"numeric\"\n\t\t\t:name=\"item.name\"\n\t\t\t@click=\"onClick\"\n\t\t\t@drop=\"(e) => e.preventDefault()\"\n\t\t\t@dragstart=\"(e) => e.preventDefault()\"\n\t\t\t@paste=\"(e) => e.preventDefault()\"\n\t\t\t:autocomplete=\"autocomplete\"\n\t\t\t:placeholder=\"item.name\"\n\t\t\tv-model=\"item.value\"\n\t\t/>\n\t"
	});

	function _templateObject() {
	  var data = babelHelpers.taggedTemplateLiteral(["\n\t\t\t\t\t<span onclick=\"", "\" class=\"ui-selector-footer-link\">\n\t\t\t\t\t", "\n\t\t\t\t\t</span>"]);

	  _templateObject = function _templateObject() {
	    return data;
	  };

	  return data;
	}
	ui_vue.BitrixVue.component('sale-checkout-view-element-input-property-enum', {
	  props: ['item', 'index', 'variants'],
	  mounted: function mounted() {
	    this.createDialog();
	  },
	  methods: {
	    createDialog: function createDialog() {
	      this.popupMenu = new ui_entitySelector.Dialog({
	        targetNode: this.$el,
	        dropdownMode: true,
	        showAvatars: false,
	        compactView: true,
	        focusOnFirst: false,
	        multiple: false,
	        items: this.getMenuItems(),
	        events: {
	          'Item:onSelect': this.onSelect,
	          'Item:onDeselect': this.onDeselect
	        },
	        footer: this.item.required === 'Y' ? '' : this.getFooter()
	      });
	      main_core.Dom.style(this.popupMenu.getContainer(), 'width', "".concat(this.$el.clientWidth, "px"));
	      main_core.Dom.style(this.popupMenu.getContainer(), 'height', '100%');
	      window.addEventListener('resize', this.onResize.bind(this));
	    },
	    getMenuItems: function getMenuItems() {
	      var items = [];
	      for (var index in this.variants) {
	        var variant = this.variants[index];
	        items.push({
	          id: variant.id,
	          entityId: 'item',
	          tabs: 'recents',
	          title: variant.name,
	          selected: this.item.value === variant.value,
	          customData: {
	            value: variant.value
	          }
	        });
	      }
	      return items;
	    },
	    deselectAll: function deselectAll() {
	      this.item.value = '';
	      this.popupMenu.deselectAll();
	      this.popupMenu.hide();
	    },
	    onSelect: function onSelect(e) {
	      var selectedItem = e.getData().item.getDialog().getSelectedItems()[0];
	      this.$el.value = selectedItem.getTitle();
	      var customData = Object.fromEntries(selectedItem.getCustomData());
	      this.item.value = customData.value;
	    },
	    onDeselect: function onDeselect() {
	      this.item.value = '';
	      this.popupMenu.hide();
	    },
	    getFooter: function getFooter() {
	      return BX.Tag.render(_templateObject(), this.deselectAll, this.localize.CHECKOUT_VIEW_PROPERTY_LIST_ENUM_RESET_CHOICE);
	    },
	    validate: function validate() {
	      main_core_events.EventEmitter.emit(sale_checkout_const.EventType.property.validate, {
	        index: this.index
	      });
	    },
	    onKeyDown: function onKeyDown(e) {
	      if (['Esc', 'Tab'].indexOf(e.key) >= 0) {
	        return;
	      }
	      e.preventDefault();
	    },
	    render: function render() {
	      this.popupMenu.show();
	    },
	    onResize: function onResize() {
	      main_core.Dom.style(this.popupMenu.getContainer(), 'width', "".concat(this.$el.clientWidth, "px"));
	    }
	  },
	  computed: {
	    localize: function localize() {
	      return Object.freeze(ui_vue.BitrixVue.getFilteredPhrases('CHECKOUT_VIEW_PROPERTY_LIST_'));
	    },
	    checkedClassObject: function checkedClassObject() {
	      return {
	        'is-invalid': this.item.validated === sale_checkout_const.Property.validate.failure,
	        'is-valid': this.item.validated === sale_checkout_const.Property.validate.successful
	      };
	    },
	    getObjectClass: function getObjectClass() {
	      var classes = {
	        'form-control': true,
	        'form-control-lg': true,
	        'ui-ctl': true,
	        'p-0': true,
	        'border-0': this.item.validated === sale_checkout_const.Property.validate.unvalidated
	      };
	      return Object.assign(classes, this.checkedClassObject);
	    },
	    getSelectClass: function getSelectClass() {
	      return {
	        'property-enum-desktop': true,
	        'form-control': true,
	        'form-control-lg': true,
	        'ui-ctl-element': true,
	        'bg-transparent': true,
	        'border-0': this.item.validated !== sale_checkout_const.Property.validate.unvalidated
	      };
	    },
	    defaultValue: function defaultValue() {
	      var _this = this;
	      if (this.item.value !== '') {
	        return this.variants.find(function (e) {
	          return e.value === _this.item.value;
	        }).name;
	      }
	      return '';
	    },
	    isValidated: function isValidated() {
	      return this.item.validated === sale_checkout_const.Property.validate.unvalidated;
	    }
	  },
	  // language=Vue
	  template: "\n\t\t<div \n\t\t\t:class=\"getObjectClass\"\n\t\t\t@blur=\"validate\"\n\t\t>\n\t\t\t<div \n\t\t\t\tv-if=\"isValidated\"\n\t\t\t\tclass=\"ui-ctl-after ui-ctl-icon-angle\"\n\t\t\t></div>\n\t\t\t<input\n\t\t\t\treadonly\n\t\t\t\t@click=\"render\"\n\t\t\t\t@keydown=\"onKeyDown\"\n\t\t\t\t:class=\"getSelectClass\"\n\t\t\t\t:placeholder=\"item.name\"\n\t\t\t\t:value=\"defaultValue\"\n\t\t\t>\n\t\t</div>\n\t"
	});

}((this.BX.Sale.Checkout.View.Element.Input = this.BX.Sale.Checkout.View.Element.Input || {}),BX.Ui,BX,BX,BX.Event,BX.Sale.Checkout.Const,BX.UI.EntitySelector));
//# sourceMappingURL=registry.bundle.js.map
