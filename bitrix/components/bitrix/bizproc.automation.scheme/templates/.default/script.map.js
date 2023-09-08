{"version":3,"sources":["script.js"],"names":["exports","main_core","bizproc_automation","main_popup","_templateObject","_templateObject2","_templateObject3","_templateObject4","namespace","Reflection","Scheme","options","babelHelpers","classCallCheck","this","defineProperty","Type","isPlainObject","scheme","TemplatesScheme","signedParameters","steps","action","executeButton","errorsContainer","stepContentTypeContainer","stepsContentContainers","stepContentCategoryContainer","stepContentStatusContainer","createClass","key","value","init","renderStepContents","Event","bind","onExecuteButtonClick","_this","selectedType","onTypeSelectorClick","selectedCategory","onCategorySelectorClick","selectedStatus","onStatusSelectorClick","completedSteps","forEach","_ref","_ref2","slicedToArray","selected","container","onclick","text","isNil","BX","message","Name","renderDropdownStepContent","getTypeCategories","length","renderTextStepContent","stepTo","target","Dom","clean","dropdownNode","create","attrs","class","events","click","children","Tag","render","taggedTemplateLiteral","Text","encode","appendChild","event","_this2","preventDefault","showError","concat","removeClass","ajax","runComponentAction","mode","data","dstScope","DocumentType","Category","Status","then","response","isSlider","sliderData","SidePanel","Instance","getTopSlider","getData","Object","entries","_ref3","_ref4","set","documentType","category","status","close","errors","error","location","href","toString","indexOf","index","elem","i","addClass","self","adjustDropdown","getDocumentTypes","map","type","id","categories","Id","statuses","getTypeStatuses","items","popupMenu","Menu","autoHide","bindElement","width","offsetWidth","closeByEsc","show","errorNode","props","className","append","window","Bizproc","Automation","Main"],"mappings":"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","file":"script.map.js"}