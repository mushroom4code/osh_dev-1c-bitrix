{"version":3,"file":"sequenceactivity.map.js","names":["_SequenceActivityCurClick","_SequenceActivityClick","act_i","i","presetId","preset","arAllActivities","find","item","defaultProps","activity","Properties","Title","HTMLEncode","Type","Children","AddActivity","CreateActivity","_SequenceActivityMyActivityClick","isn","arUserParams","BX","type","isArray","SequenceActivity","ob","BizProcActivity","childsContainer","iHead","LineMouseOver","e","this","parentNode","style","backgroundImage","LineMouseOut","OnClick","jsMnu_WFAct","groupId","oSubMenu","arAllActGroups","hasOwnProperty","activityGroupId","rootActivity","push","ICON","TEXT","ONCLICK","ind","presets","isArrayFilled","forEach","getClass","BPMESS","length","MENU","icon","name","window","jsPopup_WFAct","PopupHide","PopupMenu","ShowMenu","lastDrop","ondragging","X","Y","childActivities","arrow","rows","cells","childNodes","pos","left","right","top","bottom","onmouseover","onmouseout","h1id","DragNDrop","AddHandler","ondrop","oActivity","obj","parentActivity","ctrlKey","metaKey","pa","Name","d","s","alert","deleteRow","j","pop","h2id","ActivityRemoveChild","RemoveChild","ch","onclick","RemoveResources","self","RemoveHandler","removeChild","c","insertRow","insertCell","align","vAlign","Draw","CreateLine","BPTemplateIsModified","height","background","appendChild","document","createElement","src","width","ActivityDraw","container","_crt","className","id","parseInt","AfterSDraw"],"sources":["sequenceactivity.js"],"mappings":"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"}