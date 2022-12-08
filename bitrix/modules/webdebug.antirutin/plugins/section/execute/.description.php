<p>Плагин позволяет выполнять произвольные действия с разделами инфоблоков.</p>
<p>Это специальный плагин, который подойдет только для специальных целей: например, для разработчиков, или для выполнения произвольных действий, при предоставлении кода разработчиком модуля.</p>
<p>Имейте ввиду, что любой прописанный код выполняется на сервере, что <b>небезопасно</b> само по себе. Кроме того, важно понимать что прописанный здесь код может как выполнить полезную работу, так и что-либо сломать.</p>
<p>Краткие нюансы работы плагина:</p>
<ul>
	<li>указанный код должен вернуть либо <code><b>true</b></code> (обработка раздела успешно завершена) либо <code><b>false</b></code> (при выполнении произошла ошибка, при этом текст ошибки можно задать через <nobr><code><b>$this->setError('Error text')</b></code></nobr>),</li>
	<li>код выполняется в контексте класса плагина (<code><b>PluginSection</b></code>, наследован от <code><b>Plugin</b></code>), поэтому доступна переменная <code><b>$this</b></code>,</li>
	<li>идентификатор (ID) текущего раздела доступен в переменной <code><b>$intSectiontId</b></code>,</li>
	<li>идентификатор инфоблока доступен в <nobr><code><b>$this->intIBlockId</b></code></nobr>,</li>
	<li>большинство полезных функций, которые может понадобиться при разработке уже разработано, смотрите классы <code><b>Plugin</b></code>, <code><b>PluginSection</b></code>, <code><b>Helper</b></code>, <code><b>IBlock</b></code>, также в качестве примеров можно смотреть код других плагинов.</li>
</ul>
