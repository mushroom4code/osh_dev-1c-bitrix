{"version":3,"file":"script.map.js","names":["window","BX","namespace","Sender","Mail","Editor","Helper","changeDisplay","node","isShow","style","display","prototype","init","params","this","id","input","inputId","placeHolders","mess","context","containerId","blockNode","querySelector","plainNode","inputNode","addCustomEvent","onEditorInitedBefore","bind","onEditorInitedAfter","Template","Selector","selector","events","templateSelect","onTemplateSelect","template","isOnDemand","messageFields","some","field","code","onDemand","uri","getTemplateRequestingUri","setTemplateUri","forEach","setContent","value","isTargetEditor","editor","indexOf","components","SetComponentIcludeMethod","extend","PlaceHolderSelectorButton","BXHtmlEditor","Button","Controls","buildPrototypes","onPlaceHolderSelectorButtonCreate","isSupportedTemplateUri","isShowedBlock","confirmTemplateChange","BlockEditorManager","get","load","switchView","confirm","changeTemplate","isShowBlock","resultNode","content","wrap","superclass","constructor","apply","arguments","className","activeClassName","disabledClassName","On","disabledForTextarea","Create","_this","PersonalizationSelector","button","pCont","fields","onItemClick","event","preventDefault","getData","item","getCustomData","action","IsSupported","Exec","buildPlaceHolders","DoNothing","Check","GetValue","SetValue","OnMouseUp","OnMouseDown","OnClick","setTimeout","Message","setAdaptedInstance"],"sources":["script.js"],"mappings":"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"}