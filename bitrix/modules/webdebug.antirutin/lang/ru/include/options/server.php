<?
$strLang = 'WDA_OPTIONS_';
$strHint = $strLang.'HINT_';

$MESS[$strLang.'OPTION_SERVER'] = 'Настройки для автоматического запуска ';
$MESS[$strLang.'OPTION_PHP_PATH'] = 'Путь к PHP на сервере';
	$MESS[$strHint.'OPTION_PHP_PATH'] = 'Укажите здесь корректный путь до интерпретатора php на сервера.<br/><br/>
Это крайне важно для корректного автоматического запуска. Точное значение можно уточнить у техподдержки хостинга.<br/><br/>
Имейте в виду, что версия PHP по указанному пути должна в точности соответствовать текущей используемой на сайте версии PHP.';
$MESS[$strLang.'OPTION_PHP_CONFIG'] = 'Дополнительные конфиги PHP';
	$MESS[$strHint.'OPTION_PHP_CONFIG'] = 'Укажите здесь дополнительные параметры запуска для PHP.<br/><br/>
Это может понадобиться для некоторых серверов. Например, если на сервере для cli-запуска по умолчанию отключен short_open_tags, или если по умолчанию доступно очень мало оперативной памяти (memory_limit), или чтобы убрать предупреждение о том что mbstring deprecated. Эти конфиги прописываются по умолчанию<br/><br/>
Возможно использование и другие конфигов.';
$MESS[$strLang.'OPTION_PHP_MBSTRING'] = 'Добавлять параметры mbstring';
	$MESS[$strHint.'OPTION_PHP_MBSTRING'] = 'Отметьте галочку, чтобы в команду запуска автоматически добавлялись параметры mbstring (func_overload и internal_encoding) в соответствии с кодировкой сайта.';
?>