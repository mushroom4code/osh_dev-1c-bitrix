<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Conversion\Internals\MobileDetect;
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;

/**
 * @global CMain $APPLICATION
 * @var CBitrixComponent $component
 * @var array $arParams
 * @var array $arResult
 * @var array $arCurSection
 * @var array $item
 */
if (isset($arParams['USE_COMMON_SETTINGS_BASKET_POPUP']) && $arParams['USE_COMMON_SETTINGS_BASKET_POPUP'] == 'Y') {
    $basketAction = isset($arParams['COMMON_ADD_TO_BASKET_ACTION']) ? $arParams['COMMON_ADD_TO_BASKET_ACTION'] : '';
} else {
    $basketAction = isset($arParams['SECTION_ADD_TO_BASKET_ACTION']) ? $arParams['SECTION_ADD_TO_BASKET_ACTION'] : '';
}
$mobile = new MobileDetect();
$sort = [
    'by' => [
        'by' => 'SERVICE_FIELD_POPULARITY', // 'PROPERTY_MINIMUM_PRICE',//'CATALOG_PRICE_'.$GLOBALS['PRICE_TYPE_ID'],
        'order' => 'DESC'
    ],
];

if ($_GET['sort_by']) {
    $_SESSION['sort']['by']['by'] = $_GET['sort_by'];
    $_SESSION['sort']['by']['order'] = $_GET['sort_order'];
}

if ($_SESSION['sort']) {
    $sort = [
        'by' => [
            'by' => $_SESSION['sort']['by']['by'],
            'order' => $_SESSION['sort']['by']['order']
        ],
    ];

    $defSortByVal = $_SESSION['sort']['by']['by'];
    $defSortOrder = $_SESSION['sort']['by']['order'];
    $defLim = $_SESSION['sort']['limit'];

    $ELEMENT_SORT_FIELD = $sort['by']['by'];
    $ELEMENT_SORT_ORDER = strtoupper($sort['by']['order']);
    $ELEMENT_SORT_FIELD2 = 'SORT';
    $ELEMENT_SORT_ORDER2 = 'ASC';
} else {
    $ELEMENT_SORT_FIELD2 = $sort['by']['by'];
    $ELEMENT_SORT_ORDER2 = strtoupper($sort['by']['order']);
    $ELEMENT_SORT_FIELD = 'SORT';
    $ELEMENT_SORT_ORDER = 'ASC';
}
if (empty($defSortByVal))
    $defSortBy = 'SORT_FIELD_3';

$sortBy = array(
    'SORT_FIELD_1',
    'SORT_FIELD_2',
    'SORT_FIELD_3',
    'SORT_FIELD_4',
    'SORT_FIELD_5',
    'SORT_FIELD_6',
    'SORT_FIELD_7',
    'SORT_FIELD_8',
    'SORT_FIELD_9'
);

$codeSortBy = array(
    'PROPERTY_MINIMUM_PRICE',//'CATALOG_PRICE_'.$GLOBALS['PRICE_TYPE_ID'],
    'PROPERTY_MINIMUM_PRICE',//'CATALOG_PRICE_'.$GLOBALS['PRICE_TYPE_ID'],
    'SORT',
    'SORT',
    'CREATED_DATE',
    'CREATED_DATE',
    'NAME',
    'NAME',
    'ID',
);
$sortOrder = array(
    'asc',
    'desc',
    'desc',
    'asc',
    'desc',
    'asc',
    'asc',
    'desc',
    'desc',
);

$activeSort = 0;

foreach ($sortBy as $key => $item) {
    if (empty($item))
        continue;


    if (!isset($defSortByVal) && isset($defSortBy) && $item == $defSortBy) {
        $defSortByVal = $codeSortBy[$key];
        $activeSort = $key;
    } elseif (isset($defSortByVal) && !isset($defSortBy) && $defSortByVal == $codeSortBy[$key] && $defSortOrder == $sortOrder[$key]) {
        $activeSort = $key;
        $defSortBy = $item;
    }
}

if ($_SESSION['sort']['by']['by'])
    $defSortByVal = $_SESSION['sort']['by']['by'];

if (!isset($sort['by']['by']) || empty($sort['by']['by']) && !empty($defSortByVal))
    $sort['by']['by'] = $defSortByVal;

if (!isset($sort['by']['order']) || empty($sort['by']['order']))
    $sort['by']['order'] = $sortOrder[(array_search($defSortBy, $sortBy) !== false ? array_search($defSortBy, $sortBy) : 'asc')];

$contentBlockClass = "col";
$sort_active = $_COOKIE['orientation'] === 'line' ? "icon_sort_line_active" : "icon_sort_bar_active";

function sort_by_name($a, $b)
{
    if ($a["NAME"] == $b["NAME"]) {
        return 0;
    }
    return ($a["NAME"] < $b["NAME"]) ? -1 : 1;
}

$catalogElementField = $APPLICATION->get_cookie("PAGE_ELEMENT_COUNT") ? $APPLICATION->get_cookie("PAGE_ELEMENT_COUNT") : "36";
if ($_GET['page'] != '') {
    $APPLICATION->set_cookie("PAGE_ELEMENT_COUNT", $_GET['page'], false, "/", SITE_SERVER_NAME);
    $catalogElementField = intval($_GET['page']);
}
$arParams["PAGE_ELEMENT_COUNT"] = $catalogElementField;
// Переменная для убора функционала под мобильное приложение
$showUserContent = Enterego\PWA\EnteregoMobileAppEvents::getUserRulesForContent();
// Категории с иерархией
$arCategory = [];
require_once($_SERVER["DOCUMENT_ROOT"] . '/local/templates/Oshisha/include/catalogMenu.php');
?>
<div class="row mb-4 box_with_prod">
    <?php if ($isFilter) : ?>
        <div class=" box_filter_catalog
        <?= (isset($arParams['FILTER_HIDE_ON_MOBILE']) &&
        $arParams['FILTER_HIDE_ON_MOBILE'] === 'Y' ? ' d-none d-sm-block' : '') ?>">
            <?php if (!$mobile->isMobile()) { ?>
                <div class="row">
                    <div class="catalog-section-list-tile-list">
                        <?php foreach ($arResult['SECTION_LIST'] as $arSection) {
                            if ($showUserContent || !$showUserContent && $arSection['NAME'] === 'Чай') {
                                createNewItemsChild($arSection, $smartFil, $arCategory);
                            }
                        } ?>
                    </div>
                </div>
            <?php }

            //region Filter
            if ($isFilter): ?>
                <div class="bx-sidebar-block <?= \Enterego\EnteregoHitsHelper::checkIfHits($APPLICATION) ? 'd-none' : '' ?>">
                    <?php

                    $APPLICATION->IncludeComponent("bitrix:catalog.smart.filter",
                        "oshisha_catalog.smart.filter", array(
                            "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                            "SECTION_ID" => $arCurSection['ID'],
                            "PREFILTER_NAME" => $arParams["PREFILTER_NAME"],
                            "FILTER_NAME" => $arParams["FILTER_NAME"],
                            "PRICE_CODE" => $arParams["PRICE_CODE"],
                            "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                            "CACHE_TIME" => $arParams["CACHE_TIME"],
                            "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                            "SAVE_IN_SESSION" => "Y",
                            "FILTER_VIEW_MODE" => $arParams["FILTER_VIEW_MODE"],
                            "XML_EXPORT" => "N",
                            "SECTION_TITLE" => "NAME",
                            "SECTION_DESCRIPTION" => "DESCRIPTION",
                            'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
                            "TEMPLATE_THEME" => $arParams["TEMPLATE_THEME"],
                            'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                            'CURRENCY_ID' => $arParams['CURRENCY_ID'],
                            "SEF_MODE" => $arParams["SEF_MODE"],
                            "SEF_RULE" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["smart_filter"],
                            "SMART_FILTER_PATH" => $arResult["VARIABLES"]["SMART_FILTER_PATH"],
                            "PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
                            "INSTANT_RELOAD" => $arParams["INSTANT_RELOAD"],
                        ),
                        array('HIDE_ICONS' => 'Y')
                    );
                    ?>
                </div>
            <?php endif
            //			//endregion
            ?>
            <div class="filter-close js__filter-close">
                <span></span>
                <span></span>
                <span></span>
            </div>

            <div class="filter-view-bar">
                <div class="red_button_cart filter-view js__filter-close disabled_class">Применить</div>
            </div>
        </div>
    <? endif ?>
    <? global $GLOBAL_SECTION; ?>
    <div class="pb-4 <?= (($isFilter) ? "" : "col") ?> padding_product_box">
        <div class="row navigation-wrap">
            <div class="col" id="navigation">
                <?php $APPLICATION->IncludeComponent(
                    "bitrix:breadcrumb",
                    "oshisha_breadcrumb",
                    array(
                        "START_FROM" => "0",
                        "PATH" => "",
                        "SITE_ID" => "-"
                    ),
                    false,
                    array('HIDE_ICONS' => 'Y')
                ); ?>
            </div>
        </div>
        <h1 id="pagetitle"><?php $APPLICATION->ShowTitle(false); ?></h1>
        <p class="message_for_user_minzdrav font-14"></p>
        <div id="osh-filter-horizontal2"></div>
        <div class="osh-block-panel <?= \Enterego\EnteregoHitsHelper::checkIfHits($APPLICATION) ? 'd-none' : '' ?>">
            <div id="osh-filter-horizontal">
                <div id="osh-filter-horizontal-item" class="d-inline-block" data-osh-filter-state="hide"></div>
                <div id="osh-filter-horizontal-item-count" class="osh-filter-item"
                     onclick="smartFilter.allFilterShowHide()">
                </div>
                <div id="osh-filter-horizontal-item-remove" class="osh-filter-item"
                     onclick="smartFilter.removeHorizontalFilterAll()">
                    Очистить все
                    <span class='d-inline-block osh-filter-horizontal-remove'></span>
                </div>
            </div>
            <div class="mb-4 col_navigation">
                <div class="count-per-page">
                    <span>Количество</span>
                    <a href="?page=24"
                       class="page_num <?php if ($arParams['PAGE_ELEMENT_COUNT'] == 24): ?>active<?php endif; ?>">24</a>
                    <a href="?page=36"
                       class="page_num <?php if ($arParams['PAGE_ELEMENT_COUNT'] == 36): ?>active<?php endif; ?>">36</a>
                    <a href="?page=72"
                       class="page_num <?php if ($arParams['PAGE_ELEMENT_COUNT'] == 72): ?>active<?php endif; ?>">72</a>
                </div>
            </div>
            <div class="sort-panel mb-4">
                <div class="sort-panel-flex d-flex flex-row justify-content-end align-items-center ">
                    <div class="sort_panel_wrap">
                        <div class="sort_panel" id="">
                            <a class="sort_order sort_tool" href="#">
                                <span class="sort_orders_by sort_caption"
                                      style="min-width: 150px;">Сортировать по</span>
                                <i class="fa fa-angle-down" aria-hidden="true"></i>
                            </a>
                            <div class="sort_orders_element js__sort_orders_element">
                                <ul>
                                    <li class="catalog_sort_item js__catalog-sort-item"
                                        data-sort="<?= 'PROPERTY_' . SORT_POPULARITY ?>"
                                        data-order="DESC">По популярности
                                    </li>
                                    <li class="catalog_sort_item js__catalog-sort-item"
                                        data-price-id="<?= $GLOBALS['PRICE_TYPE_ID'] ?>"
                                        data-sort="<?= 'PROPERTY_' . SORT_PRICE ?>"
                                        data-order="ASC">По возрастанию цены
                                    </li>
                                    <li class="catalog_sort_item js__catalog-sort-item"
                                        data-price-id="<?= $GLOBALS['PRICE_TYPE_ID'] ?>"
                                        data-sort="<?= 'PROPERTY_' . SORT_PRICE ?>"
                                        data-order="DESC">По убыванию цены
                                    </li>
                                    <li class="catalog_sort_item js__catalog-sort-item"
                                        data-sort="NAME"
                                        data-order="ASC">По названию
                                    </li>
                                    <li class="catalog_sort_item js__catalog-sort-item"
                                        data-sort="CREATED_DATE"
                                        data-order="DESC">По новизне
                                    </li>
                                    <li class="catalog_sort_item js__catalog-sort-item"
                                        data-sort="<?= 'PROPERTY_' . SORT_BREND ?>"
                                        data-order="DESC">По бренду
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="button_panel_wrap">
                        <div class="sort_mobile"></div>
                        <div class="icon_sort_bar xs-d-none" id="card_catalog"></div>
                        <div class="icon_sort_line xs-d-none" id="line_catalog"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box_for_tasted"></div>
        <?php
        if (ModuleManager::isModuleInstalled("sale")) {
            $arRecomData = array();
            $recomCacheID = array('IBLOCK_ID' => $arParams['IBLOCK_ID']);
            $obCache = new CPHPCache();
            if ($obCache->InitCache(36000, serialize($recomCacheID), "/sale/bestsellers")) {
                $arRecomData = $obCache->GetVars();
            } elseif ($obCache->StartDataCache()) {
                if (Loader::includeModule("catalog")) {
                    $arSKU = CCatalogSku::GetInfoByProductIBlock($arParams['IBLOCK_ID']);
                    $arRecomData['OFFER_IBLOCK_ID'] = (!empty($arSKU) ? $arSKU['IBLOCK_ID'] : 0);
                }
                $obCache->EndDataCache($arRecomData);
            }

        }

        //region Catalog Section
        $sectionListParams = array(
            "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
            "SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
            "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
            "CACHE_TYPE" => $arParams["CACHE_TYPE"],
            "CACHE_TIME" => $arParams["CACHE_TIME"],
            "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
            "COUNT_ELEMENTS" => $arParams["SECTION_COUNT_ELEMENTS"],
            "TOP_DEPTH" => $arParams["SECTION_TOP_DEPTH"],
            "SECTION_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
            "VIEW_MODE" => $arParams["SECTIONS_VIEW_MODE"],
            "SHOW_PARENT_NAME" => $arParams["SECTIONS_SHOW_PARENT_NAME"],
            "HIDE_SECTION_NAME" => ($arParams["SECTIONS_HIDE_SECTION_NAME"] ?? "N"),
            "ADD_SECTIONS_CHAIN" => ($arParams["ADD_SECTIONS_CHAIN"] ?? '')
        );
        if ($sectionListParams["COUNT_ELEMENTS"] === "Y") {
            $sectionListParams["COUNT_ELEMENTS_FILTER"] = "CNT_ACTIVE";
            if ($arParams["HIDE_NOT_AVAILABLE"] == "Y") {
                $sectionListParams["COUNT_ELEMENTS_FILTER"] = "CNT_AVAILABLE";
            }
        }

        unset($sectionListParams);

        if ($arParams["USE_COMPARE"] == "Y") {
            $APPLICATION->IncludeComponent(
                "bitrix:catalog.compare.list",
                "bootstrap_v4", array(
                "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                "NAME" => $arParams["COMPARE_NAME"],
                "DETAIL_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["element"],
                "COMPARE_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["compare"],
                "ACTION_VARIABLE" => (!empty($arParams["ACTION_VARIABLE"]) ? $arParams["ACTION_VARIABLE"] : "action"),
                "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
                'POSITION_FIXED' => $arParams['COMPARE_POSITION_FIXED'] ?? '',
                'POSITION' => $arParams['COMPARE_POSITION'] ?? ''
            ),
                $component,
                array("HIDE_ICONS" => "Y")
            );
        }
        //endregion

        if ($_SESSION[$arParams["FILTER_NAME"]][$GLOBAL_SECTION['ID']]['hide_not_available'] == "Y") {
            $arParams["HIDE_NOT_AVAILABLE"] = "Y";
        }
        $curSection = CIBlockSection::GetByID($arCurSection['ID'])->fetch();
        global $ArFilter;
        $sectionParams = array(
            "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
            "FILL_ITEM_ALL_PRICES" => "Y",
            "ELEMENT_SORT_FIELD2" => $ELEMENT_SORT_FIELD2,
            "ELEMENT_SORT_ORDER2" => $ELEMENT_SORT_ORDER2,
            "ELEMENT_SORT_FIELD" => (\Enterego\EnteregoHitsHelper::checkIfStartsWithHit($APPLICATION)
                && !(\Enterego\EnteregoHitsHelper::checkIfHits($APPLICATION))
                && $curSection['DEPTH_LEVEL'] == '1') ? 'PROPERTY_' . SORT_BREND : $ELEMENT_SORT_FIELD,
            "ELEMENT_SORT_ORDER" => (\Enterego\EnteregoHitsHelper::checkIfStartsWithHit($APPLICATION)
                && !(\Enterego\EnteregoHitsHelper::checkIfHits($APPLICATION))
                && $curSection['DEPTH_LEVEL'] == '1') ? 'DESC' : $ELEMENT_SORT_ORDER,
            "PROPERTY_CODE" => (isset($arParams["LIST_PROPERTY_CODE"]) ? $arParams["LIST_PROPERTY_CODE"] : []),
            "PROPERTY_CODE_MOBILE" => $arParams["LIST_PROPERTY_CODE_MOBILE"],
            "META_KEYWORDS" => $arParams["LIST_META_KEYWORDS"],
            "META_DESCRIPTION" => $arParams["LIST_META_DESCRIPTION"],
            "BROWSER_TITLE" => $arParams["LIST_BROWSER_TITLE"],
            "SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
            "INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
            "BASKET_URL" => $arParams["BASKET_URL"],
            "ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
            "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
            "SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
            "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
            "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
            "FILTER_NAME" => $arParams["FILTER_NAME"],
            "CACHE_TYPE" => $arParams["CACHE_TYPE"],
            "CACHE_TIME" => $arParams["CACHE_TIME"],
            "CACHE_FILTER" => $arParams["CACHE_FILTER"],
            "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
            "SET_TITLE" => $arParams["SET_TITLE"],
            "MESSAGE_404" => $arParams["~MESSAGE_404"],
            "SET_STATUS_404" => $arParams["SET_STATUS_404"],
            "SHOW_404" => $arParams["SHOW_404"],
            "FILE_404" => $arParams["FILE_404"],
            "DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
            "PAGE_ELEMENT_COUNT" => $arParams['PAGE_ELEMENT_COUNT'],
            "LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
            "PRICE_CODE" => $arParams["~PRICE_CODE"],
            "USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
            "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],

            "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
            "USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
            "ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
            "PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
            "PRODUCT_PROPERTIES" => (isset($arParams["PRODUCT_PROPERTIES"]) ? $arParams["PRODUCT_PROPERTIES"] : []),

            "DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
            "DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
            "PAGER_TITLE" => $arParams["PAGER_TITLE"],
            "PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
            "PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
            "PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
            "PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
            "PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
            "PAGER_BASE_LINK_ENABLE" => $arParams["PAGER_BASE_LINK_ENABLE"],
            "PAGER_BASE_LINK" => $arParams["PAGER_BASE_LINK"],
            "PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
            "LAZY_LOAD" => $arParams["LAZY_LOAD"],
            "MESS_BTN_LAZY_LOAD" => $arParams["~MESS_BTN_LAZY_LOAD"],
            "LOAD_ON_SCROLL" => $arParams["LOAD_ON_SCROLL"],

            "OFFERS_CART_PROPERTIES" => (isset($arParams["OFFERS_CART_PROPERTIES"]) ? $arParams["OFFERS_CART_PROPERTIES"] : []),
            "OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
            "OFFERS_PROPERTY_CODE" => (isset($arParams["LIST_OFFERS_PROPERTY_CODE"]) ? $arParams["LIST_OFFERS_PROPERTY_CODE"] : []),
            "OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
            "OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
            "OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
            "OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
            "OFFERS_LIMIT" => (isset($arParams["LIST_OFFERS_LIMIT"]) ? $arParams["LIST_OFFERS_LIMIT"] : 0),

            "SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
            "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
            "SECTION_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
            "DETAIL_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["element"],
            "USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
            'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
            'CURRENCY_ID' => $arParams['CURRENCY_ID'],
            'HIDE_NOT_AVAILABLE' => $_GET['hide_not_available'] == 'Y' ? 'Y' : $arParams["HIDE_NOT_AVAILABLE"],
            'HIDE_NOT_AVAILABLE_OFFERS' => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],

            'LABEL_PROP' => $arParams['LABEL_PROP'],
            'LABEL_PROP_MOBILE' => $arParams['LABEL_PROP_MOBILE'],
            'LABEL_PROP_POSITION' => $arParams['LABEL_PROP_POSITION'],
            'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
            'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],
            'PRODUCT_BLOCKS_ORDER' => $arParams['LIST_PRODUCT_BLOCKS_ORDER'],
            'PRODUCT_ROW_VARIANTS' => $arParams['LIST_PRODUCT_ROW_VARIANTS'],
            'ENLARGE_PRODUCT' => $arParams['LIST_ENLARGE_PRODUCT'],
            'ENLARGE_PROP' => isset($arParams['LIST_ENLARGE_PROP']) ? $arParams['LIST_ENLARGE_PROP'] : '',
            'SHOW_SLIDER' => $arParams['LIST_SHOW_SLIDER'],
            'SLIDER_INTERVAL' => isset($arParams['LIST_SLIDER_INTERVAL']) ? $arParams['LIST_SLIDER_INTERVAL'] : '',
            'SLIDER_PROGRESS' => isset($arParams['LIST_SLIDER_PROGRESS']) ? $arParams['LIST_SLIDER_PROGRESS'] : '',

            'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
            'OFFER_TREE_PROPS' => (isset($arParams['OFFER_TREE_PROPS']) ? $arParams['OFFER_TREE_PROPS'] : []),
            'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
            'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
            'DISCOUNT_PERCENT_POSITION' => $arParams['DISCOUNT_PERCENT_POSITION'],
            'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
            'SHOW_MAX_QUANTITY' => $arParams['SHOW_MAX_QUANTITY'],
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
            "ADD_SECTIONS_CHAIN" => $arParams["ADD_SECTIONS_CHAIN"],
            'ADD_TO_BASKET_ACTION' => $basketAction,
            'SHOW_CLOSE_POPUP' => isset($arParams['COMMON_SHOW_CLOSE_POPUP']) ? $arParams['COMMON_SHOW_CLOSE_POPUP'] : '',
            'COMPARE_PATH' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['compare'],
            'COMPARE_NAME' => $arParams['COMPARE_NAME'],
            'USE_COMPARE_LIST' => 'Y',
            'BACKGROUND_IMAGE' => (isset($arParams['SECTION_BACKGROUND_IMAGE']) ? $arParams['SECTION_BACKGROUND_IMAGE'] : ''),
            'COMPATIBLE_MODE' => (isset($arParams['COMPATIBLE_MODE']) ? $arParams['COMPATIBLE_MODE'] : ''),
            'DISABLE_INIT_JS_IN_COMPONENT' => (isset($arParams['DISABLE_INIT_JS_IN_COMPONENT']) ? $arParams['DISABLE_INIT_JS_IN_COMPONENT'] : '')
        );
        if (\Enterego\EnteregoHitsHelper::checkIfHits($APPLICATION)) {
            $intSectionID = $APPLICATION->IncludeComponent(
                "bitrix:enterego.hit_section",
                ".default",
                $sectionParams,
                $component
            );
        } else {
            $intSectionID = $APPLICATION->IncludeComponent(
                "bitrix:catalog.section",
                "oshisha_catalog.section",
                $sectionParams,
                $component
            );
        }
        $GLOBALS['CATALOG_CURRENT_SECTION_ID'] = $intSectionID;

        if (ModuleManager::isModuleInstalled("sale")) {
            if (!empty($arRecomData)) {
                if (!isset($arParams['USE_BIG_DATA']) || $arParams['USE_BIG_DATA'] != 'N') {
                    ?>
                    <div class="row mb-3">
                        <div class="col" data-entity="parent-container">
                            <div class="catalog-block-header" data-entity="header" data-showed="false"
                                 style="display: none; opacity: 0;">
                                <?= GetMessage('CATALOG_PERSONAL_RECOM') ?>
                            </div>
                            <?php
                            $APPLICATION->IncludeComponent("bitrix:catalog.section",
                                "oshisha_catalog.section", array(
                                    "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                                    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                                    "ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
                                    "ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
                                    "ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
                                    "ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
                                    "PROPERTY_CODE" => ($arParams["LIST_PROPERTY_CODE"] ?? []),
                                    "PROPERTY_CODE_MOBILE" => $arParams["LIST_PROPERTY_CODE_MOBILE"],
                                    "INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
                                    "BASKET_URL" => $arParams["BASKET_URL"],
                                    "ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
                                    "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
                                    "SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
                                    "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
                                    "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
                                    "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                                    "CACHE_TIME" => $arParams["CACHE_TIME"],
                                    "CACHE_FILTER" => $arParams["CACHE_FILTER"],
                                    "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                                    "DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
                                    "PAGE_ELEMENT_COUNT" => 0,
                                    "PRICE_CODE" => $arParams["~PRICE_CODE"],
                                    "USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
                                    "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],

                                    "SET_BROWSER_TITLE" => "N",
                                    "SET_META_KEYWORDS" => "N",
                                    "SET_META_DESCRIPTION" => "N",
                                    "SET_LAST_MODIFIED" => "N",
                                    "ADD_SECTIONS_CHAIN" => $arParams["ADD_SECTIONS_CHAIN"],

                                    "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
                                    "USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
                                    "ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
                                    "PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
                                    "PRODUCT_PROPERTIES" => (isset($arParams["PRODUCT_PROPERTIES"]) ? $arParams["PRODUCT_PROPERTIES"] : []),

                                    "OFFERS_CART_PROPERTIES" => (isset($arParams["OFFERS_CART_PROPERTIES"]) ? $arParams["OFFERS_CART_PROPERTIES"] : []),
                                    "OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
                                    "OFFERS_PROPERTY_CODE" => (isset($arParams["LIST_OFFERS_PROPERTY_CODE"]) ? $arParams["LIST_OFFERS_PROPERTY_CODE"] : []),
                                    "OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
                                    "OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
                                    "OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
                                    "OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
                                    "OFFERS_LIMIT" => (isset($arParams["LIST_OFFERS_LIMIT"]) ? $arParams["LIST_OFFERS_LIMIT"] : 0),

                                    "SECTION_ID" => $intSectionID,
                                    "SECTION_CODE" => "",
                                    "SECTION_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
                                    "DETAIL_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["element"],
                                    "USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
                                    'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                                    'CURRENCY_ID' => $arParams['CURRENCY_ID'],
                                    'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
                                    'HIDE_NOT_AVAILABLE_OFFERS' => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],

                                    'LABEL_PROP' => $arParams['LABEL_PROP'],
                                    'LABEL_PROP_MOBILE' => $arParams['LABEL_PROP_MOBILE'],
                                    'LABEL_PROP_POSITION' => $arParams['LABEL_PROP_POSITION'],
                                    'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
                                    'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],
                                    'PRODUCT_BLOCKS_ORDER' => $arParams['LIST_PRODUCT_BLOCKS_ORDER'],
                                    'PRODUCT_ROW_VARIANTS' => "[{'VARIANT':'3','BIG_DATA':true}]",
                                    'ENLARGE_PRODUCT' => $arParams['LIST_ENLARGE_PRODUCT'],
                                    'ENLARGE_PROP' => isset($arParams['LIST_ENLARGE_PROP']) ? $arParams['LIST_ENLARGE_PROP'] : '',
                                    'SHOW_SLIDER' => $arParams['LIST_SHOW_SLIDER'],
                                    'SLIDER_INTERVAL' => isset($arParams['LIST_SLIDER_INTERVAL']) ? $arParams['LIST_SLIDER_INTERVAL'] : '',
                                    'SLIDER_PROGRESS' => isset($arParams['LIST_SLIDER_PROGRESS']) ? $arParams['LIST_SLIDER_PROGRESS'] : '',

                                    "DISPLAY_TOP_PAGER" => 'N',
                                    "DISPLAY_BOTTOM_PAGER" => 'N',
                                    "HIDE_SECTION_DESCRIPTION" => "Y",

                                    "RCM_TYPE" => isset($arParams['BIG_DATA_RCM_TYPE']) ? $arParams['BIG_DATA_RCM_TYPE'] : '',
                                    "SHOW_FROM_SECTION" => 'Y',

                                    'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
                                    'OFFER_TREE_PROPS' => (isset($arParams['OFFER_TREE_PROPS']) ? $arParams['OFFER_TREE_PROPS'] : []),
                                    'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
                                    'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
                                    'DISCOUNT_PERCENT_POSITION' => $arParams['DISCOUNT_PERCENT_POSITION'],
                                    'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
                                    'SHOW_MAX_QUANTITY' => $arParams['SHOW_MAX_QUANTITY'],
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
                                    'ADD_TO_BASKET_ACTION' => $basketAction,
                                    'SHOW_CLOSE_POPUP' => isset($arParams['COMMON_SHOW_CLOSE_POPUP']) ? $arParams['COMMON_SHOW_CLOSE_POPUP'] : '',
                                    'COMPARE_PATH' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['compare'],
                                    'COMPARE_NAME' => $arParams['COMPARE_NAME'],
                                    'USE_COMPARE_LIST' => 'Y',
                                    'BACKGROUND_IMAGE' => '',
                                    'DISABLE_INIT_JS_IN_COMPONENT' => (isset($arParams['DISABLE_INIT_JS_IN_COMPONENT']) ? $arParams['DISABLE_INIT_JS_IN_COMPONENT'] : '')
                                ),
                                $component
                            );
                            ?>
                        </div>
                    </div>
                    <?php
                }
            }
        }
        ?>
    </div>
</div>
