{"version":3,"file":"date-formatter.bundle.map.js","names":["this","BX","Im","V2","exports","main_date","DateCode","shortTimeFormat","DateTimeFormat","getFormat","shortDateFormat","dayMonthFormat","longDateFormat","dayOfWeekMonthFormat","fullDateFormat","dayShortMonthFormat","mediumDateFormat","defaultDateTime","Interval","tomorrow","today","yesterday","week","year","olderThanYear","DateTemplate","notification","dateGroup","meeting","recent","messageReadStatus","_date","babelHelpers","classPrivateFieldLooseKey","_matchingFunctions","_isYesterday","_isToday","_isTomorrow","_isCurrentWeek","_isCurrentYear","_isSame","_shiftDate","DateFormatter","static","date","formatCode","formatByCode","template","formatByTemplate","format","classPrivateFieldLooseBase","intervals","Object","keys","matchingInterval","find","interval","templateHasInterval","matchingFunction","intervalIsMatching","console","error","matchingCode","constructor","_date2","defineProperty","value","_shiftDate2","_isSame2","_isCurrentYear2","_isCurrentWeek2","_isTomorrow2","_isToday2","_isYesterday2","writable","Date","currentWeekNumber","setWeekNumber","sameYear","currentYear","getFullYear","setYear","dateLocale","toLocaleDateString","setDateLocale","shift","setDate","getDate","Lib","Main"],"sources":["date-formatter.bundle.js"],"mappings":"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"}