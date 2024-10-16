<?php
/**
 * @var $arParams
 * @var $APPLICATION
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;

Loader::includeModule("iblock");
Loader::includeModule("highloadblock");

if (!$arParams['HLBLOCK_NAME']) {
    return;
}

$CurPage = $APPLICATION->GetCurPage();
$arPage = [];
$arPageTemp = explode("/", $CurPage);
foreach ($arPageTemp as $key_p => $page) {
    if ($page != "")
        $arPage[] = $page;
}
//Есть внутренние
if ($CurPage != $arParams['SEF_URL']) {
    $componentPage = 'detail';
    $arParams['CODE'] = end($arPage);
} else {

    $componentPage = 'section';
}
$this->IncludeComponentTemplate($componentPage);

