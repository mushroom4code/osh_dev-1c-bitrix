{"version":3,"file":"reaction.bundle.map.js","names":["this","BX","Messenger","v2","Component","exports","main_core_events","main_core","ui_reactionsSelect","ui_lottie","im_v2_component_elements","im_v2_application_core","im_v2_const","im_v2_lib_user","im_v2_lib_logger","_store","babelHelpers","classPrivateFieldLooseKey","_restClient","ReactionService","constructor","Object","defineProperty","writable","value","classPrivateFieldLooseBase","Core","getStore","getRestClient","setReaction","messageId","reaction","Logger","warn","dispatch","userId","getUserId","callMethod","RestMethod","imV2ChatMessageReactionAdd","catch","error","console","removeReaction","imV2ChatMessageReactionDelete","SHOW_DELAY","HIDE_DELAY","ReactionSelector","props","type","Number","required","data","computed","reactionsData","$store","getters","ownReactionSet","_this$reactionsData","_this$reactionsData$o","ownReactions","size","methods","startShowTimer","showTimeout","setTimeout","showSelector","clearShowTimer","clearTimeout","setHideTimeout","selector","ReactionsSelect","name","position","$refs","subscribeToSelectorEvents","show","subscribe","selectEvent","_this$selector","getData","getReactionService","hide","hideTimeout","_this$selector2","onIconClick","currentReaction","reactionType","like","reactionService","template","ReactionUser","components","Avatar","AvatarSize","user","avatarStyle","avatar","backgroundImage","_store$1","_restClient$1","_userManager","UserService","UserManager","loadReactionUsers","users","queryParams","filter","imV2ChatMessageReactionTail","then","response","setUsersToModel","values","map","id","Error","AdditionalUsers","UserListPopup","String","Boolean","bindElement","emits","showPopup","loadingAdditionalUsers","additionalUsers","watch","newValue","oldValue","loadUsers","getUserService","userIds","onPopupClose","$emit","prepareAdditionalUsers","firstViewerId","dialog","lastMessageViews","firstViewer","userService","USERS_TO_SHOW","SHOW_USERS_DELAY","ReactionItem","counter","Array","selected","animate","showAdditionalUsers","showUsers","userLimitIsNotReached","weHaveUsersData","length","preparedUsers","sort","a","b","reverse","reactionClass","reactionCssClass","mounted","playAnimation","animation","Lottie","loadAnimation","animationData","getLottieAnimation","container","reactionIcon","loop","autoplay","renderer","rendererSettings","viewBoxOnly","Event","bind","destroy","play","startShowUsersTimer","showUsersTimeout","clearShowUsersTimer","onClick","animateItemFunction","ReactionList","Reaction","message","reactionCounters","_this$reactionsData$r","_this$reactionsData2","Set","showReactionsContainer","keys","EventEmitter","emit","EventType","scrollToBottom","chatId","threshold","DialogScrollThreshold","nearTheBottom","onReactionSelect","event","_this$ownReactions","has","getReactionUsers","reactionUsers","Message","Ui","UI","Elements","Application","Const","Lib"],"sources":["reaction.bundle.js"],"mappings":"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"}