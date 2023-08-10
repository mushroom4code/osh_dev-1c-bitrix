{"version":3,"file":"notification-content.bundle.map.js","names":["this","BX","Messenger","v2","Component","exports","main_core_events","main_polyfill_intersectionobserver","ui_dialogs_messagebox","im_v2_provider_service","im_v2_lib_utils","im_v2_lib_parser","im_public","im_v2_component_elements","im_v2_lib_dateFormatter","ui_forms","ui_vue3_vuex","im_v2_lib_user","main_core","im_v2_application_core","im_v2_const","im_v2_lib_logger","NotificationItemAvatar","name","components","Avatar","props","userId","type","Number","required","computed","isSystem","dialogId","toString","user","$store","getters","template","NotificationQuickAnswer","MessengerButton","Button","notification","Object","emits","data","quickAnswerText","quickAnswerResultMessage","showQuickAnswer","isSending","successSentQuickAnswer","ButtonSize","ButtonColor","methods","toggleQuickAnswer","$nextTick","$refs","focus","sendQuickAnswer","trim","$emit","id","text","callbackSuccess","response","result_message","resultMessage","message","callbackError","NotificationItemConfirmButtons","buttons","Array","preparedButtons","map","button","value","COMMAND_PARAMS","split","TEXT","click","getButtonColor","Primary","LightBorder","NotificationItemContent","Attach","notificationItem","hasQuickAnswer","_this$notification$pa","params","CAN_ANSWER","content","Parser","decodeNotification","attachList","_this$notification$pa2","ATTACH","onContentClick","event","executeClickEvent","onConfirmButtonsClick","onSendQuickAnswer","NotificationItemHeader","AvatarSize","ChatTitle","date","sectionCode","authorId","hasName","length","title","$Bitrix","Loc","getMessage","userDialogId","titleClasses","extranet","hasMoreUsers","_this$notificationIte","USERS","moreUsers","phrase","start","end","canDelete","NotificationTypesCodes","simple","itemDate","DateFormatter","formatByTemplate","DateTemplate","onUserTitleClick","openChat","onMoreUsersClick","users","onDeleteClick","NotificationItem","searchMode","Boolean","default","isUnread","read","userData","onDoubleClick","NotificationPlaceholder","itemsToShow","NotificationSearchPanel","schema","searchQuery","searchType","searchDate","filterTypes","originalSchema","modulesToRemove","forEach","moduleId","LIST","calendar","NAME","sender","blog","socialnetwork","intranet","modulesToShowInFilter","notificationFilterTypes","push","watch","search","onDateFilterClick","get","popup","close","node","target","field","bTime","callback_after","NotificationScrollButton","unreadCounter","notificationsOnScreen","notificationCollection","hasUnreadOnScreen","some","_this$notificationMap","notificationMapCollection","firstUnreadId","item","find","firstUnreadBelowVisible","minIdOnScreen","Math","min","hasUnreadBelowVisible","unreadCounterBeforeVisible","i","has","showScrollButton","arrowButtonClass","arrowDown","formattedCounter","mapState","state","notifications","collection","onScrollButtonClick","idToScroll","firstUnreadNode","selector","document","querySelector","offsetTop","latestNotification","latestNotificationNode","LIMIT_PER_PAGE","NotificationSearchService","constructor","store","restClient","userManager","isLoading","lastId","hasMoreItemsToLoad","Core","getStore","getRestClient","UserManager","loadFirstPage","requestItems","firstPage","loadNextPage","Promise","resolve","searchInModel","filter","result","toLowerCase","includes","settingName","parseDate","Date","itemDateForCompare","getTime","setHours","dateFromInput","queryParams","getSearchRequestParams","callMethod","RestMethod","imNotifyHistorySearch","then","responseData","Logger","warn","isLastPage","getLastItemId","setUsersToModel","catch","error","requestParams","SEARCH_TEXT","SEARCH_TYPE","LIMIT","CONVERT_TEXT","toISOString","Type","isArrayFilled","destroy","NotificationReadService","itemsToRead","Set","changeReadStatusBlockTimeout","readOnClientWithDebounce","Runtime","debounce","readOnClient","readRequestWithDebounce","readRequest","addToReadQueue","notificationIds","isNumber","add","size","idToReadFrom","imNotifyRead","clear","dispatch","ids","readAll","console","changeReadStatus","notificationId","clearTimeout","setTimeout","action","only_current","NotificationContent","ChatInfoPopup","UserListPopup","Loader","directives","mounted","element","binding","instance","observer","observe","beforeUnmount","unobserve","isInitialLoading","isNextPageLoading","windowFocused","showSearchPanel","showSearchResult","popupBindElement","showChatInfoPopup","chatInfoDialogId","showUserListPopup","userListIds","searchResultCollection","isReadAllAvailable","isEmptyState","emptyStateIcon","emptyStateTitle","newValue","oldValue","created","notificationService","NotificationService","notificationSearchService","notificationReadService","searchOnServerDelayed","searchOnServer","Event","bind","window","onWindowFocus","onWindowBlur","initObserver","hasFocus","EventEmitter","subscribe","EventType","mention","openChatInfo","onOpenChatInfo","unsubscribe","unbind","IntersectionObserver","entries","entry","parseInt","dataset","isIntersecting","delete","intersectionRatio","intersectionRect","height","rootBounds","root","threshold","from","fill","zero","index","oneScreenRemaining","scrollTop","clientHeight","scrollHeight","setSearchResult","items","offset","scroll","top","behavior","onScroll","onScrollSearchResult","onScrollNotifications","onClickReadAll","messageBox","MessageBox","MessageBoxButtons","YES_CANCEL","onYes","onCancel","show","sendConfirmAction","$event","getData","onSearch","localResult","skipValidation","Content","UI","Dialogs","Provider","Service","Lib","Elements","Im","V2","Vue3","Vuex","Application","Const"],"sources":["notification-content.bundle.js"],"mappings":"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"}