{"version":3,"sources":["popupcomponentsmaker.bundle.js"],"names":["this","BX","exports","main_popup","main_core_events","main_core","main_loader","_templateObject","PopupComponentsMakerItem","_EventEmitter","babelHelpers","inherits","_this","options","arguments","length","undefined","classCallCheck","possibleConstructorReturn","getPrototypeOf","call","html","Type","isDomNode","awaitContent","isBoolean","flex","isNumber","withoutBackground","backgroundColor","isString","marginBottom","disabled","overflow","displayBlock","layout","container","createClass","key","value","getLoader","loader","Loader","target","getContainer","size","_await","classList","add","showLoader","stopAwait","remove","hideLoader","show","hide","getContent","updateContent","node","Dom","clean","appendChild","setBackgroundColor","color","style","Tag","render","taggedTemplateLiteral","EventEmitter","_templateObject$1","_templateObject2","_templateObject3","PopupComponentsMaker","_ref","id","content","width","cacheable","isElementNode","contentWrapper","popup","items","getItems","getItem","item","indexOf","push","getPopup","popupWidth","popupId","Popup","className","contentBackground","angle","offset","offsetLeft","offsetWidth","autoHide","closeByEsc","padding","animation","getContentWrapper","getContentContainer","overflowX","isShown","map","_item$html","sectionNode","getSection","isArray","innerSection","itemObj","_itemObj$html","then","adjustPromise","itemInner","isFunction","_item$html2","close","UI","Main","Event"],"mappings":"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","file":"popupcomponentsmaker.bundle.map.js"}