{"version":3,"sources":["call_hint_popup.js"],"names":["BX","namespace","Call","CallHint","options","this","popup","title","prop","getString","util","htmlspecialchars","message","icon","bindElement","getElementNode","targetContainer","callFolded","getBoolean","autoCloseDelay","getInteger","buttonsLayout","buttons","getArray","callbacks","onClose","getFunction","DoNothing","autoCloseTimeout","prototype","show","clearTimeout","setTimeout","onAutoClose","bind","PopupWindow","content","render","padding","contentPadding","className","contentBackground","maxWidth","angle","position","offset","events","destroy","onDestroy","create","props","children","html","getPopupMessage","renderButtons","click","close","map","button","Util","isDesktop","hotKeyMessage","hotkey","browser","IsMac","replace","getPopupHeight"],"mappings":"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","file":"call_hint_popup.map.js"}