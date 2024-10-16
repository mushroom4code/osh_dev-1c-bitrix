<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */

/** @var CBitrixComponent $component */

use Bitrix\Main\Loader;
use Bitrix\Main\Page\Asset;
use Enterego\EnteregoHelper;

$this->setFrameMode(true);

global $searchFilter;

if (defined('IS_MERCH_PROPERTY')) {
    $GLOBALS['searchFilter'] = $GLOBALS['searchFilter']
        ? array_merge($GLOBALS['searchFilter'], ['!=PROPERTY_'.IS_MERCH_PROPERTY.'_VALUE' => 'Да'])
        : ['!=PROPERTY_'.IS_MERCH_PROPERTY.'_VALUE' => 'Да'];
}

if (Loader::includeModule("sale")) {
    $recommendedData = array();
    $recommendedCacheId = array('IBLOCK_ID' => $arParams['IBLOCK_ID']);

    $obCache = new CPHPCache();
    if ($obCache->InitCache(36000, serialize($recommendedCacheId), '/catalog/recommended')) {
        $recommendedData = $obCache->GetVars();
    } elseif ($obCache->StartDataCache()) {
        if (Loader::includeModule('catalog')) {
            $arSku = CCatalogSku::GetInfoByProductIBlock($arParams['IBLOCK_ID']);
            $recommendedData['OFFER_IBLOCK_ID'] = (!empty($arSku) ? $arSku['IBLOCK_ID'] : 0);
            $recommendedData['IBLOCK_LINK'] = '';
            $recommendedData['ALL_LINK'] = '';
            $rsProps = CIBlockProperty::GetList(
                array('SORT' => 'ASC', 'ID' => 'ASC'),
                array('IBLOCK_ID' => $arParams['IBLOCK_ID'], 'PROPERTY_TYPE' => 'E', 'ACTIVE' => 'Y')
            );
            $found = false;
            while ($arProp = $rsProps->Fetch()) {
                if ($found) {
                    break;
                }

                if ($arProp['CODE'] == '') {
                    $arProp['CODE'] = $arProp['ID'];
                }

                $arProp['LINK_IBLOCK_ID'] = intval($arProp['LINK_IBLOCK_ID']);
                if ($arProp['LINK_IBLOCK_ID'] != 0 && $arProp['LINK_IBLOCK_ID'] != $arParams['IBLOCK_ID']) {
                    continue;
                }

                if ($arProp['LINK_IBLOCK_ID'] > 0) {
                    if ($recommendedData['IBLOCK_LINK'] == '') {
                        $recommendedData['IBLOCK_LINK'] = $arProp['CODE'];
                        $found = true;
                    }
                } else {
                    if ($recommendedData['ALL_LINK'] == '') {
                        $recommendedData['ALL_LINK'] = $arProp['CODE'];
                    }
                }
            }

            if ($found) {
                if (defined('BX_COMP_MANAGED_CACHE')) {
                    global $CACHE_MANAGER;
                    $CACHE_MANAGER->StartTagCache('/catalog/recommended');
                    $CACHE_MANAGER->RegisterTag('iblock_id_' . $arParams['IBLOCK_ID']);
                    $CACHE_MANAGER->EndTagCache();
                }
            }
        }

        $obCache->EndDataCache($recommendedData);
    }
}


$elementOrder = array();
if ($arParams['USE_SEARCH_RESULT_ORDER'] === 'N') {
    $elementOrder = array(
        "ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
        "ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
        "ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
        "ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
    );
}

if (Loader::includeModule('search')) {
    $arElements = $APPLICATION->IncludeComponent(
        "bitrix:search.page",
        "oshisha_searсh.page",
        array(
            "RESTART" => $arParams["RESTART"],
            "NO_WORD_LOGIC" => $arParams["NO_WORD_LOGIC"],
            "USE_LANGUAGE_GUESS" => $arParams["USE_LANGUAGE_GUESS"],
            "CHECK_DATES" => $arParams["CHECK_DATES"],
            "arrFILTER" => array("iblock_" . $arParams["IBLOCK_TYPE"]),
            "arrFILTER_iblock_" . $arParams["IBLOCK_TYPE"] => array($arParams["IBLOCK_ID"]),
            "USE_TITLE_RANK" => "Y",
            "DEFAULT_SORT" => "rank",
            "FILTER_NAME" => "searchFilter",
            "SHOW_WHERE" => "N",
            "arrWHERE" => array(),
            "SHOW_WHEN" => "N",
            "PAGE_RESULT_COUNT" => (isset($arParams["PAGE_RESULT_COUNT"]) ? $arParams["PAGE_RESULT_COUNT"] : 50),
            "DISPLAY_TOP_PAGER" => "N",
            "DISPLAY_BOTTOM_PAGER" => "N",
            "PAGER_TITLE" => "",
            "PAGER_SHOW_ALWAYS" => "N",
            "PAGER_TEMPLATE" => "N",
        ),
        $component,
        array('HIDE_ICONS' => 'Y')
    );

    if (!empty($arElements) && is_array($arElements) && defined('IS_MERCH_PROPERTY')) {
        $arElementsRes = CIBlockElement::getList([], ['ID' => $arElements], false, false, ['ID', 'PROPERTY_'.IS_MERCH_PROPERTY]);
        while ($element = $arElementsRes->fetch()) {
            if ($element['PROPERTY_'.IS_MERCH_PROPERTY.'_VALUE'] == 'Да') {
                unset($arElements[array_search($element['ID'], $arElements)]);
            }
        }
    }

    if (!empty($arElements) && is_array($arElements)) {
        $searchFilter = $searchFilter
            ? array_merge(array("ID" => $arElements), $searchFilter)
            : array("ID" => $arElements);
        if ($arParams['USE_SEARCH_RESULT_ORDER'] === 'Y') {
            $elementOrder = array(
                "ELEMENT_SORT_FIELD" => "ID",
                "ELEMENT_SORT_ORDER" => $arElements
            );

        }
        echo '<div class="mb-5" style="color: #999999;font-weight: 600;font-size: 24px;">Вы искали "' . htmlspecialcharsbx($_GET['q']) . '". Найдено ' . count($arElements) . ' совпадений</div>';
    } else {
        if (is_array($arElements)) {
            echo '<div class="mb-5" style="color: #999999;font-weight: 600;font-size: 37px;">К сожалению, такого товара нет на сайте.</div>';

            if ($USER->IsAuthorized() && SITE_ID !== SITE_EXHIBITION) {
                if (!isset($arParams['USE_BIG_DATA']) || $arParams['USE_BIG_DATA'] != 'N') {
                    ?>
                    <div class="row mb-3 mt-5 by-card">
                        <div class="col" data-entity="parent-container">
                            <h4><b>Рекомендации для вас</b></h4>
                            <?php $APPLICATION->IncludeComponent(
                                "bitrix:catalog.top",
                                "oshisha_catalog.top",
                                array(
                                    "ACTION_VARIABLE" => "action",
                                    "ADD_PICT_PROP" => "-",
                                    "ADD_PROPERTIES_TO_BASKET" => "Y",
                                    "ADD_TO_BASKET_ACTION" => "ADD",
                                    "BASKET_URL" => "/personal/basket.php",
                                    "CACHE_FILTER" => "N",
                                    "CACHE_GROUPS" => "Y",
                                    "CACHE_TIME" => "36000000",
                                    "CACHE_TYPE" => "A",
                                    "COMPARE_NAME" => "CATALOG_COMPARE_LIST",
                                    "COMPATIBLE_MODE" => "Y",
                                    "COMPONENT_TEMPLATE" => "oshisha_catalog.top",
                                    "CONVERT_CURRENCY" => "N",
                                    "CUSTOM_FILTER" => "{\"CLASS_ID\":\"CondGroup\",\"DATA\":{\"All\":\"AND\",\"True\":\"True\"},\"CHILDREN\":[]}",
                                    "DETAIL_URL" => "",
                                    "DISPLAY_COMPARE" => "N",
                                    "ELEMENT_COUNT" => "16",
                                    "ELEMENT_SORT_FIELD" => "timestamp_x",
                                    "ELEMENT_SORT_FIELD2" => "id",
                                    "ELEMENT_SORT_ORDER" => "asc",
                                    "ELEMENT_SORT_ORDER2" => "desc",
                                    "ENLARGE_PRODUCT" => "PROP",
                                    "ENLARGE_PROP" => "-",
                                    "FILTER_NAME" => "",
                                    "HIDE_NOT_AVAILABLE" => "Y",
                                    "HIDE_NOT_AVAILABLE_OFFERS" => "N",
                                    "IBLOCK_ID" => IBLOCK_CATALOG,
                                    "IBLOCK_TYPE" => "1c_catalog",
                                    "LABEL_PROP" => array(),
                                    "LABEL_PROP_MOBILE" => "",
                                    "LABEL_PROP_POSITION" => "top-left",
                                    "LINE_ELEMENT_COUNT" => "4",
                                    "MESS_BTN_ADD_TO_BASKET" => "Забронировать",
                                    "MESS_BTN_BUY" => "Купить",
                                    "MESS_BTN_COMPARE" => "Сравнить",
                                    "MESS_BTN_DETAIL" => "Подробнее",
                                    "MESS_NOT_AVAILABLE" => "Нет в наличии",
                                    "OFFERS_FIELD_CODE" => array(
                                        0 => "",
                                        1 => "",
                                    ),
                                    "OFFERS_LIMIT" => "4",
                                    "OFFERS_SORT_FIELD" => "sort",
                                    "OFFERS_SORT_FIELD2" => "id",
                                    "OFFERS_SORT_ORDER" => "asc",
                                    "OFFERS_SORT_ORDER2" => "desc",
                                    "OFFER_ADD_PICT_PROP" => "MORE_PHOTO",
                                    "PARTIAL_PRODUCT_PROPERTIES" => "N",
                                    "PRICE_CODE" => BXConstants::PriceCode(),
                                    "FILL_ITEM_ALL_PRICES" => "Y",
                                    "PRICE_VAT_INCLUDE" => "Y",
                                    "PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons",
                                    "PRODUCT_DISPLAY_MODE" => "Y",
                                    "PRODUCT_ID_VARIABLE" => "id",
                                    "PRODUCT_PROPS_VARIABLE" => "prop",
                                    "PRODUCT_QUANTITY_VARIABLE" => "quantity",
                                    "PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false}]",
                                    "PRODUCT_SUBSCRIPTION" => "Y",
                                    "PROPERTY_CODE_MOBILE" => "",
                                    "ROTATE_TIMER" => "30",
                                    "SECTION_URL" => "",
                                    "SEF_MODE" => "N",
                                    "SHOW_CLOSE_POPUP" => "N",
                                    "SHOW_DISCOUNT_PERCENT" => "N",
                                    "SHOW_MAX_QUANTITY" => "N",
                                    "SHOW_OLD_PRICE" => "N",
                                    "SHOW_PAGINATION" => "Y",
                                    "SHOW_PRICE_COUNT" => "1",
                                    "SHOW_SLIDER" => "Y",
                                    "SLIDER_INTERVAL" => "3000",
                                    "SLIDER_PROGRESS" => "N",
                                    "TEMPLATE_THEME" => "blue",
                                    "USE_ENHANCED_ECOMMERCE" => "N",
                                    "USE_PRICE_COUNT" => "N",
                                    "USE_PRODUCT_QUANTITY" => "N",
                                    "VIEW_MODE" => "SLIDER"
                                ),
                                false
                            ); ?>
                        </div>
                    </div>
                    <?
                }
            }
            return;
        }
    }
} else {
    $searchQuery = '';
    if (isset($_REQUEST['q']) && is_string($_REQUEST['q']))
        $searchQuery = trim($_REQUEST['q']);
    if ($searchQuery !== '') {
        $searchFilter = $searchFilter
            ? array_merge(array('*SEARCHABLE_CONTENT' => $searchQuery), $searchFilter)
            : array('*SEARCHABLE_CONTENT' => $searchQuery);
    }
    unset($searchQuery);
}

if (!empty($searchFilter) && is_array($searchFilter)) {
    $componentParams = array(
            "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
            "PAGE_ELEMENT_COUNT" => $arParams["PAGE_ELEMENT_COUNT"],
            "LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
            "PROPERTY_CODE" => $arParams["PROPERTY_CODE"],
            "PROPERTY_CODE_MOBILE" => (isset($arParams["PROPERTY_CODE_MOBILE"]) ? $arParams["PROPERTY_CODE_MOBILE"] : []),
            "OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
            "OFFERS_FIELD_CODE" => $arParams["OFFERS_FIELD_CODE"],
            "OFFERS_PROPERTY_CODE" => $arParams["OFFERS_PROPERTY_CODE"],
            "OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
            "OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
            "OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
            "OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
            "OFFERS_LIMIT" => $arParams["OFFERS_LIMIT"],
            "SECTION_URL" => $arParams["SECTION_URL"],
            "DETAIL_URL" => $arParams["DETAIL_URL"],
            "BASKET_URL" => $arParams["BASKET_URL"],
            "ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
            "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
            "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
            "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
            "SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
            "CACHE_TYPE" => $arParams["CACHE_TYPE"],
            "CACHE_TIME" => $arParams["CACHE_TIME"],
            "DISPLAY_COMPARE" => $arParams["DISPLAY_COMPARE"],
            "PRICE_CODE" => $arParams["~PRICE_CODE"],
            "FILL_ITEM_ALL_PRICES" => "Y",
            "USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
            "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
            "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
            "PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
            "USE_PRODUCT_QUANTITY" => $arParams["USE_PRODUCT_QUANTITY"],
            "ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
            "PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
            "CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
            "CURRENCY_ID" => $arParams["CURRENCY_ID"],
            "HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
            "HIDE_NOT_AVAILABLE_OFFERS" => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],
            "DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
            "DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
            "PAGER_TITLE" => $arParams["PAGER_TITLE"],
            "PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
            "PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
            "PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
            "PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
            "PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
            "LAZY_LOAD" => (isset($arParams["LAZY_LOAD"]) ? $arParams["LAZY_LOAD"] : 'N'),
            "MESS_BTN_LAZY_LOAD" => (isset($arParams["~MESS_BTN_LAZY_LOAD"]) ? $arParams["~MESS_BTN_LAZY_LOAD"] : ''),
            "LOAD_ON_SCROLL" => (isset($arParams["LOAD_ON_SCROLL"]) ? $arParams["LOAD_ON_SCROLL"] : 'N'),
            "FILTER_NAME" => "searchFilter",
            "SECTION_ID" => "",
            "SECTION_CODE" => "",
            "SECTION_USER_FIELDS" => array(),
            "INCLUDE_SUBSECTIONS" => "Y",
            "SHOW_ALL_WO_SECTION" => "Y",
            "META_KEYWORDS" => "",
            "META_DESCRIPTION" => "",
            "BROWSER_TITLE" => "",
            "ADD_SECTIONS_CHAIN" => "N",
            "SET_TITLE" => "N",
            "SET_STATUS_404" => "N",
            "CACHE_FILTER" => "N",
            "CACHE_GROUPS" => "N",

            'LABEL_PROP' => (isset($arParams['LABEL_PROP']) ? $arParams['LABEL_PROP'] : ''),
            'LABEL_PROP_MOBILE' => (isset($arParams['LABEL_PROP_MOBILE']) ? $arParams['LABEL_PROP_MOBILE'] : ''),
            'LABEL_PROP_POSITION' => (isset($arParams['LABEL_PROP_POSITION']) ? $arParams['LABEL_PROP_POSITION'] : ''),
            'ADD_PICT_PROP' => (isset($arParams['ADD_PICT_PROP']) ? $arParams['ADD_PICT_PROP'] : ''),
            'PRODUCT_DISPLAY_MODE' => (isset($arParams['PRODUCT_DISPLAY_MODE']) ? $arParams['PRODUCT_DISPLAY_MODE'] : ''),
            'PRODUCT_BLOCKS_ORDER' => (isset($arParams['PRODUCT_BLOCKS_ORDER']) ? $arParams['PRODUCT_BLOCKS_ORDER'] : ''),
            'PRODUCT_ROW_VARIANTS' => (isset($arParams['PRODUCT_ROW_VARIANTS']) ? $arParams['PRODUCT_ROW_VARIANTS'] : ''),
            'ENLARGE_PRODUCT' => (isset($arParams['ENLARGE_PRODUCT']) ? $arParams['ENLARGE_PRODUCT'] : ''),
            'ENLARGE_PROP' => (isset($arParams['ENLARGE_PROP']) ? $arParams['ENLARGE_PROP'] : ''),
            'SHOW_SLIDER' => (isset($arParams['SHOW_SLIDER']) ? $arParams['SHOW_SLIDER'] : 'Y'),
            'SLIDER_INTERVAL' => (isset($arParams['SLIDER_INTERVAL']) ? $arParams['SLIDER_INTERVAL'] : '3000'),
            'SLIDER_PROGRESS' => (isset($arParams['SLIDER_PROGRESS']) ? $arParams['SLIDER_PROGRESS'] : 'N'),

            'OFFER_ADD_PICT_PROP' => (isset($arParams['OFFER_ADD_PICT_PROP']) ? $arParams['OFFER_ADD_PICT_PROP'] : ''),
            'OFFER_TREE_PROPS' => (isset($arParams['OFFER_TREE_PROPS']) ? $arParams['OFFER_TREE_PROPS'] : []),
            'PRODUCT_SUBSCRIPTION' => (isset($arParams['PRODUCT_SUBSCRIPTION']) ? $arParams['PRODUCT_SUBSCRIPTION'] : ''),
            'SHOW_DISCOUNT_PERCENT' => (isset($arParams['SHOW_DISCOUNT_PERCENT']) ? $arParams['SHOW_DISCOUNT_PERCENT'] : ''),
            'SHOW_OLD_PRICE' => (isset($arParams['SHOW_OLD_PRICE']) ? $arParams['SHOW_OLD_PRICE'] : ''),
            'SHOW_MAX_QUANTITY' => (isset($arParams['SHOW_MAX_QUANTITY']) ? $arParams['SHOW_MAX_QUANTITY'] : ''),
            'MESS_SHOW_MAX_QUANTITY' => (isset($arParams['~MESS_SHOW_MAX_QUANTITY']) ? $arParams['~MESS_SHOW_MAX_QUANTITY'] : ''),
            'RELATIVE_QUANTITY_FACTOR' => (isset($arParams['RELATIVE_QUANTITY_FACTOR']) ? $arParams['RELATIVE_QUANTITY_FACTOR'] : ''),
            'MESS_RELATIVE_QUANTITY_MANY' => (isset($arParams['~MESS_RELATIVE_QUANTITY_MANY']) ? $arParams['~MESS_RELATIVE_QUANTITY_MANY'] : ''),
            'MESS_RELATIVE_QUANTITY_FEW' => (isset($arParams['~MESS_RELATIVE_QUANTITY_FEW']) ? $arParams['~MESS_RELATIVE_QUANTITY_FEW'] : ''),
            'MESS_BTN_BUY' => (isset($arParams['~MESS_BTN_BUY']) ? $arParams['~MESS_BTN_BUY'] : ''),
            'MESS_BTN_ADD_TO_BASKET' => (isset($arParams['~MESS_BTN_ADD_TO_BASKET']) ? $arParams['~MESS_BTN_ADD_TO_BASKET'] : ''),
            'MESS_BTN_SUBSCRIBE' => (isset($arParams['~MESS_BTN_SUBSCRIBE']) ? $arParams['~MESS_BTN_SUBSCRIBE'] : ''),
            'MESS_BTN_DETAIL' => (isset($arParams['~MESS_BTN_DETAIL']) ? $arParams['~MESS_BTN_DETAIL'] : ''),
            'MESS_NOT_AVAILABLE' => (isset($arParams['~MESS_NOT_AVAILABLE']) ? $arParams['~MESS_NOT_AVAILABLE'] : ''),
            'MESS_BTN_COMPARE' => (isset($arParams['~MESS_BTN_COMPARE']) ? $arParams['~MESS_BTN_COMPARE'] : ''),

            'USE_ENHANCED_ECOMMERCE' => (isset($arParams['USE_ENHANCED_ECOMMERCE']) ? $arParams['USE_ENHANCED_ECOMMERCE'] : ''),
            'DATA_LAYER_NAME' => (isset($arParams['DATA_LAYER_NAME']) ? $arParams['DATA_LAYER_NAME'] : ''),
            'BRAND_PROPERTY' => (isset($arParams['BRAND_PROPERTY']) ? $arParams['BRAND_PROPERTY'] : ''),

            'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
            'ADD_TO_BASKET_ACTION' => (isset($arParams['ADD_TO_BASKET_ACTION']) ? $arParams['ADD_TO_BASKET_ACTION'] : ''),
            'SHOW_CLOSE_POPUP' => (isset($arParams['SHOW_CLOSE_POPUP']) ? $arParams['SHOW_CLOSE_POPUP'] : ''),
            'COMPARE_PATH' => (isset($arParams['COMPARE_PATH']) ? $arParams['COMPARE_PATH'] : ''),
            'COMPARE_NAME' => (isset($arParams['COMPARE_NAME']) ? $arParams['COMPARE_NAME'] : ''),
            'USE_COMPARE_LIST' => (isset($arParams['USE_COMPARE_LIST']) ? $arParams['USE_COMPARE_LIST'] : '')
        ) + $elementOrder;
    ?>
    <div class="search_result">
        <?
        $APPLICATION->IncludeComponent(
            "bitrix:catalog.section",
            "oshisha_catalog.section",
            $componentParams,
            $arResult["THEME_COMPONENT"],
            array('HIDE_ICONS' => 'Y')
        ); ?>
    </div>
    <?
}
