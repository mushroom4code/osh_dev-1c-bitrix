{"version":3,"file":"script.map.js","names":["exports","main_core","main_core_events","ui_notification","_templateObject","_templateObject2","_templateObject3","_templateObject4","_templateObject5","_templateObject6","_templateObject7","_createForOfIteratorHelper","o","allowArrayLike","it","Symbol","iterator","Array","isArray","_unsupportedIterableToArray","length","i","F","s","n","done","value","e","_e","f","TypeError","normalCompletion","didErr","err","call","step","next","_e2","minLen","_arrayLikeToArray","Object","prototype","toString","slice","constructor","name","from","test","arr","len","arr2","_classPrivateMethodInitSpec","obj","privateSet","_checkPrivateRedeclaration","add","privateCollection","has","_classPrivateMethodGet","receiver","fn","namespace","Reflection","toJsonString","data","JSON","stringify","v","_getRobotsTemplate","WeakSet","_activateSection","_validateScriptName","_validateConstants","ScriptEditComponent","options","babelHelpers","classCallCheck","this","defineProperty","Type","isPlainObject","baseNode","leftMenuNode","saveButtonNode","formNode","documentType","signedParameters","saveCallback","automationDesigner","BX","Bizproc","Automation","Designer","getInstance","component","createClass","key","init","_this","Event","bind","saveHandler","initMenu","scriptNameNode","elements","NAME","isStringFilled","Dom","addClass","closest","removeClass","EventEmitter","subscribe","configsMenuItem","addNoticeIcon","_this2","form","FormData","scriptFields","_iterator","entries","_step","field","_validateScriptName2","robotsTemplate","_getRobotsTemplate2","setTemplateValues","_validateConstants2","getConstants","collectUsages","Constant","ajax","runComponentAction","analyticsLabel","ID","script","serialize","then","result","status","isArrayFilled","errors","markModified","isFunction","_this3","querySelectorAll","forEach","el","menuActivateHandler","getAttribute","UI","DropdownMenuItem","getItemByNode","page","_this4","hasClass","showConfigsHandler","configsNode","_this5","clean","constants","parameters","getParameters","robotNodes","robots","robot","node","renderRobotConfigBlock","push","append","Tag","render","taggedTemplateLiteral","Loc","getMessage","_this6","usages","itemNodes","size","headPushed","constId","constant","find","c","Id","renderPropertyBlock","constantPrefix","Parameter","_headPushed","paramId","parameter","p","parameterPrefix","Text","encode","getTitle","property","prefix","control","FieldType","renderControlPublic","document","getRawType","Default","Name","Description","changePropertyDescription","event","_this7","element","currentTarget","wrapper","previousElementSibling","hide","inputElement","focus","applyNewDescription","text","trim","textContent","show","updateConstant","updateParameter","keyCode","unbind","template","_this8","querySelector","setConstantValue","get","param","setParameterValue","templateManager","templates","_activateSection2","section","menuItem","concat","setActiveHandler","Notification","Center","notify","content","usedConstants","window"],"sources":["script.js"],"mappings":"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"}