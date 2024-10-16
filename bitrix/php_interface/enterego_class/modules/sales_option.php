<?php
define('BX_SESSION_ID_CHANGE', false);
define('BX_SKIP_POST_UNQUOTE', true);
define('NO_AGENT_CHECK', true);
define("STATISTIC_SKIP_ACTIVITY_CHECK", true);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;

if (!$USER->IsAuthorized()) {
	exit("not auth");
} else {
	$act = $_POST['action'];
	if ($act === 'SetParamSale') {
		COption::SetOptionString('activation_price_admin', 'USE_CUSTOM_SALE_PRICE', $_POST['param']);
		if (!empty($_POST['date_start']) && !empty($_POST['date_end'])) {
			COption::SetOptionString('activation_price_admin', 'PERIOD',
				json_encode(['start' => $_POST['date_start'], 'end' => $_POST['date_end']]));
		}
		exit($_POST['param']);//управление использования специального вида цен для скидок
	} elseif ($act === 'SetParamPriceList') {
		COption::SetOptionString('priceList_xlsx', 'priceListArrayCustom', $_POST['param']);
	} else if ($act === 'SetParamInfo') {
		COption::SetOptionString('activation_info_admin', 'CHECKED_INFO', $_POST['param']);
		if (!empty($_POST['text_info']) && !empty($_POST['text_info_mobile'])) {
			COption::SetOptionString('activation_info_admin', 'PERIOD',
				json_encode([
					'start' => $_POST['date_start'],
					'end' => $_POST['date_end'],
					'text_info' => $_POST['text_info'],
					'link_info' => $_POST['link_info'],
					'text_info_mobile' => $_POST['text_info_mobile'],
				]));
		}
		exit($_POST['param']);//управление использования специального вида цен для скидок
	} else if($act === 'SetParamExhibition') {
        COption::SetOptionString('exhibition_info_admin_params', 'CHECKED_EXHIBITION', $_POST['param']);
        if (!empty($_POST['dateStart']) && !empty($_POST['dateEnd'])) {
            COption::SetOptionString('exhibition_info_admin_params', 'PERIOD',
                json_encode(['start' => $_POST['dateStart'], 'end' => $_POST['dateEnd']]));
        }
    }

}