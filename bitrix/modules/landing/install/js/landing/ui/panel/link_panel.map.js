{"version":3,"file":"link_panel.map.js","names":["BX","namespace","attr","Landing","Utils","join","random","setTextContent","isPlainObject","isString","textToPlaceholders","findParent","escapeText","UI","Panel","Link","id","data","Content","apply","this","arguments","layout","classList","add","overlay","appendFooterButton","Button","BaseButton","text","Loc","getMessage","onClick","save","bind","className","hide","window","parent","document","body","appendChild","instance","getInstance","title","prototype","constructor","__proto__","show","node","form","innerHTML","Block","Node","Form","BaseForm","manifest","name","field","getField","addField","clear","appendForm","checkReadyToSave","call","EditorPanel","Text","Field","range","contextDocument","getSelection","getRangeAt","textField","BaseField","currentField","isEditable","link","cloneContents","querySelector","startContainer","tagName","href","target","getAttribute","remove","header","allowedTypes","LinkUrl","TYPE_BLOCK","TYPE_PAGE","Main","options","params","type","push","TYPE_CATALOG","content","innerText","toString","siteId","site_id","landingId","filter","isChanged","setValue","getValue","value","removeAllRanges","addRange","enableEdit","tmpHref","selection","execCommand","anchorNode","parentElement","includes","hrefInput","getPlaceholderData","then","placeholdersData","replace","RegExp","attrs"],"sources":["link_panel.js"],"mappings":"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"}