<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();


$APPLICATION->SetTitle($arResult['UF_NAME']);
//$APPLICATION->AddChainItem('Бренды', '/brands/');
$APPLICATION->AddChainItem($arResult['UF_NAME'],'/brands/'.$arResult['UF_CODE'].'/');

?>

<div class="brand_detail">
	<div class="brand_detail_first">
		<?if( $arResult['UF_FILE']):?>
		<?$UF_FILE = CFile::ResizeImageGet($arResult['UF_FILE'], array('width'=>150, 'height'=>150), BX_RESIZE_IMAGE_PROPORTIONAL, true)?>	
		<div class="brand_logo"><img src="<?=$UF_FILE['src']?>"></div>
		<?endif;?>
		<div class="brand_name">
            <h1><?=$arResult['UF_NAME']?></h1>
            <span><?=$arResult['UF_DESCRIPTION']?></span>
        </div>
	</div>
	<div class="brand_detail_second">
	<?=$arResult['UF_DESCR']?>
	
	</div>
</div>
<h5>Товары бренда <?=$arResult['UF_NAME']?></h5>
<div class="brand_products ">
<?

$GLOBALS['ArFilter'] = array(
    'PROPERTY_BREND' => $arResult['UF_XML_ID']
);

$APPLICATION->IncludeComponent(
    "bitrix:catalog.section",
    "oshisha_catalog.section",
    array(
        "IBLOCK_TYPE" => "1c_catalog",
        "IBLOCK_ID" => $arParams['IBLOCK_ID'],
        "TEMPLATE_THEME" => "site",
        "DETAIL_SHOW_MAX_QUANTITY"=>"Y",
        "HIDE_NOT_AVAILABLE" => "L",
        "BASKET_URL" => "/personal/cart/",
        "ACTION_VARIABLE" => "action",
        "PRODUCT_ID_VARIABLE" => "id",
        "SECTION_ID_VARIABLE" => "SECTION_ID",
        "PRODUCT_QUANTITY_VARIABLE" => "quantity",
        "PRODUCT_PROPS_VARIABLE" => "prop",
        "SEF_MODE" => "Y",
        "SEF_FOLDER" => "/catalog_new/",
        "AJAX_MODE" => "N",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "Y",
        "AJAX_OPTION_HISTORY" => "N",
        "CACHE_TYPE" => "N",
        "CACHE_TIME" => "",
        "CACHE_FILTER" => "Y",
        "CACHE_GROUPS" => "Y",
        "SET_TITLE" => "Y",
        "ADD_SECTION_CHAIN" => "Y",
        "ADD_ELEMENT_CHAIN" => "Y",
        "SET_STATUS_404" => "Y",
        "DETAIL_DISPLAY_NAME" => "N",
        "USE_ELEMENT_COUNTER" => "Y",
        "USE_FILTER" => "Y",
        "FILTER_NAME" => "ArFilter",
        "FILTER_VIEW_MODE" => "VERTICAL",
        "USE_COMPARE" => "N",
        "PRICE_CODE" => BXConstants::PriceCode(),
        "FILL_ITEM_ALL_PRICES" => "Y",
        "USE_PRICE_COUNT" => "N",
        "SHOW_PRICE_COUNT" => "100",
        "PRICE_VAT_INCLUDE" => "Y",
        "PRICE_VAT_SHOW_VALUE" => "N",
        "PRODUCT_PROPERTIES" => "",
        "USE_PRODUCT_QUANTITY" => "Y",
        "CONVERT_CURRENCY" => "N",
        "QUANTITY_FLOAT" => "N",
        "OFFERS_CART_PROPERTIES" => array(
            0 => "SIZES_SHOES",
            1 => "SIZES_CLOTHES",
            2 => "COLOR_REF",
        ),
        "SHOW_TOP_ELEMENTS" => "N",
        "SECTION_COUNT_ELEMENTS" => "Y",
        "SECTION_TOP_DEPTH" => "1",
        "SECTIONS_VIEW_MODE" => "TILE",
        "SECTIONS_SHOW_PARENT_NAME" => "N",
        "PAGE_ELEMENT_COUNT" => "16",
        "LINE_ELEMENT_COUNT" => "4",
        "ELEMENT_SORT_FIELD" =>'name',
        "ELEMENT_SORT_ORDER" => "asc",
        "ELEMENT_SORT_FIELD2" => 'name',
        "ELEMENT_SORT_ORDER2" => "desc",
        "PROPERTY_CODE" => array(
            0 => "NEWPRODUCT",
            1 => "SALELEADER",
            2 => "SPECIALOFFER",
            3 => "",
        ),
        "INCLUDE_SUBSECTIONS" => "Y",
        "LIST_META_KEYWORDS" => "-",
        "LIST_META_DESCRIPTION" => "-",
        "LIST_BROWSER_TITLE" => "-",
        "OFFERS_FIELD_CODE" => array(
            0 => "NAME",
            1 => "PREVIEW_PICTURE",
            2 => "DETAIL_PICTURE",
            3 => "",
        ),
        "OFFERS_PROPERTY_CODE" => array(
            0 => "SIZES_SHOES",
            1 => "SIZES_CLOTHES",
            2 => "COLOR_REF",
            3 => "MORE_PHOTO",
            4 => "ARTNUMBER",
            5 => "",
        ),
        "OFFERS_LIMIT" => "0",
        "SECTION_BACKGROUND_IMAGE" => "-",
        "DETAIL_PROPERTY_CODE" => array(
            0 => "NEWPRODUCT",
            1 => "MANUFACTURER",
            2 => "MATERIAL",
        ),
        "DETAIL_META_KEYWORDS" => "-",
        "DETAIL_META_DESCRIPTION" => "-",
        "DETAIL_BROWSER_TITLE" => "-",
        "DETAIL_OFFERS_FIELD_CODE" => array(
            0 => "NAME",
            1 => "",
        ),
        "DETAIL_OFFERS_PROPERTY_CODE" => array(
            0 => "ARTNUMBER",
            1 => "SIZES_SHOES",
            2 => "SIZES_CLOTHES",
            3 => "COLOR_REF",
            4 => "MORE_PHOTO",
            5 => "",
        ),
        "DETAIL_BACKGROUND_IMAGE" => "-",
        "LINK_IBLOCK_TYPE" => "",
        "LINK_IBLOCK_ID" => "",
        "LINK_PROPERTY_SID" => "",
        "LINK_ELEMENTS_URL" => "link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#",
        "USE_ALSO_BUY" => "Y",
        "ALSO_BUY_ELEMENT_COUNT" => "4",
        "ALSO_BUY_MIN_BUYES" => "1",
        "OFFERS_SORT_FIELD" => "sort",
        "OFFERS_SORT_ORDER" => "desc",
        "OFFERS_SORT_FIELD2" => "id",
        "OFFERS_SORT_ORDER2" => "desc",
        "PAGER_TEMPLATE" => "round",
        "DISPLAY_TOP_PAGER" => "N",
        "DISPLAY_BOTTOM_PAGER" => "Y",
        "PAGER_TITLE" => "Товары",
        "PAGER_SHOW_ALWAYS" => "N",
        "PAGER_DESC_NUMBERING" => "N",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000000",
        "PAGER_SHOW_ALL" => "N",
        "ADD_PICT_PROP" => "MORE_PHOTO",
        "LABEL_PROP" => array(
            0 => PROPERTY_KEY_VKUS,
        ),
        "PRODUCT_DISPLAY_MODE" => "Y",
        "OFFER_ADD_PICT_PROP" => "MORE_PHOTO",
        "OFFER_TREE_PROPS" => array(
            0 => "SIZES_SHOES",
            1 => "SIZES_CLOTHES",
            2 => "COLOR_REF",
            3 => "",
        ),
        "SHOW_DISCOUNT_PERCENT" => "Y",
        "SHOW_OLD_PRICE" => "Y",
        "MESS_BTN_BUY" => "Купить",
        "MESS_BTN_ADD_TO_BASKET" => "В корзину",
        "MESS_BTN_COMPARE" => "Сравнение",
        "MESS_BTN_DETAIL" => "Подробнее",
        "MESS_NOT_AVAILABLE" => "Нет в наличии",
        "DETAIL_USE_VOTE_RATING" => "Y",
        "DETAIL_VOTE_DISPLAY_AS_RATING" => "rating",
        "DETAIL_USE_COMMENTS" => "Y",
        "DETAIL_BLOG_USE" => "Y",
        "DETAIL_VK_USE" => "N",
        "DETAIL_FB_USE" => "Y",
        "AJAX_OPTION_ADDITIONAL" => "",
        "USE_STORE" => "Y",
        "BIG_DATA_RCM_TYPE" => "personal",
        "FIELDS" => array(
            0 => "SCHEDULE",
            1 => "STORE",
            2 => "",
        ),
        "USE_MIN_AMOUNT" => "N",
        "STORE_PATH" => "/store/#store_id#",
        "MAIN_TITLE" => "Наличие на складах",
        "MIN_AMOUNT" => "10",
        "DETAIL_BRAND_USE" => "Y",
        "DETAIL_BRAND_PROP_CODE" => array(
            0 => "",
            1 => "BRAND_REF",
            2 => "",
        ),
        "COMPATIBLE_MODE" => "N",
        "SIDEBAR_SECTION_SHOW" => "Y",
        "SIDEBAR_DETAIL_SHOW" => "Y",
        "SIDEBAR_PATH" => "/catalog/sidebar.php",
        "COMPONENT_TEMPLATE" => "oshisha_catalog.catalog",
        "HIDE_NOT_AVAILABLE_OFFERS" => "N",
        "LABEL_PROP_MOBILE" => array(
        ),
        "LABEL_PROP_POSITION" => "top-left",
        "COMMON_SHOW_CLOSE_POPUP" => "N",
        "PRODUCT_SUBSCRIPTION" => "Y",
        "DISCOUNT_PERCENT_POSITION" => "bottom-right",
        "SHOW_MAX_QUANTITY" => "Y",
        "MESS_BTN_SUBSCRIBE" => "Подписаться",
        "SIDEBAR_SECTION_POSITION" => "right",
        "SIDEBAR_DETAIL_POSITION" => "right",
        "USER_CONSENT" => "N",
        "USER_CONSENT_ID" => "0",
        "USER_CONSENT_IS_CHECKED" => "Y",
        "USER_CONSENT_IS_LOADED" => "N",
        "USE_MAIN_ELEMENT_SECTION" => "N",
        "DETAIL_STRICT_SECTION_CHECK" => "N",
        "SET_LAST_MODIFIED" => "N",
        "ADD_SECTIONS_CHAIN" => "Y",
        "USE_SALE_BESTSELLERS" => "Y",
        "FILTER_HIDE_ON_MOBILE" => "N",
        "INSTANT_RELOAD" => "N",
        "ADD_PROPERTIES_TO_BASKET" => "Y",
        "PARTIAL_PRODUCT_PROPERTIES" => "N",
        "USE_COMMON_SETTINGS_BASKET_POPUP" => "N",
        "COMMON_ADD_TO_BASKET_ACTION" => "ADD",
        "TOP_ADD_TO_BASKET_ACTION" => "ADD",
        "SECTION_ADD_TO_BASKET_ACTION" => "ADD",
        "DETAIL_ADD_TO_BASKET_ACTION" => array(
            0 => "ADD",
        ),
        "DETAIL_ADD_TO_BASKET_ACTION_PRIMARY" => array(
            0 => "ADD",
        ),
        "SEARCH_PAGE_RESULT_COUNT" => "50",
        "SEARCH_RESTART" => "Y",
        "SEARCH_NO_WORD_LOGIC" => "Y",
        "SEARCH_USE_LANGUAGE_GUESS" => "Y",
        "SEARCH_CHECK_DATES" => "Y",
        "SECTIONS_HIDE_SECTION_NAME" => "N",
        "LIST_PROPERTY_CODE_MOBILE" => array(
        ),
        "LIST_PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons",
        "LIST_PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false}]",
        "LIST_ENLARGE_PRODUCT" => "PROP",
        "LIST_SHOW_SLIDER" => "Y",
        "LIST_SLIDER_INTERVAL" => "3000",
        "LIST_SLIDER_PROGRESS" => "N",
        "DETAIL_SET_CANONICAL_URL" => "N",
        "DETAIL_CHECK_SECTION_ID_VARIABLE" => "N",
        "SHOW_DEACTIVATED" => "N",
        "DETAIL_MAIN_BLOCK_PROPERTY_CODE" => array(
        ),
        "DETAIL_BLOG_URL" => "catalog_comments",
        "DETAIL_BLOG_EMAIL_NOTIFY" => "N",
        "DETAIL_FB_APP_ID" => "",
        "DETAIL_IMAGE_RESOLUTION" => "16by9",
        "DETAIL_PRODUCT_INFO_BLOCK_ORDER" => "sku,props",
        "DETAIL_PRODUCT_PAY_BLOCK_ORDER" => "rating,price,priceRanges,quantityLimit,quantity,buttons",
        "DETAIL_SHOW_SLIDER" => "N",
        "DETAIL_DETAIL_PICTURE_MODE" => array(
            0 => "POPUP",
            1 => "MAGNIFIER",
        ),
        "DETAIL_ADD_DETAIL_TO_SLIDER" => "N",
        "DETAIL_DISPLAY_PREVIEW_TEXT_MODE" => "E",
        "MESS_PRICE_RANGES_TITLE" => "Цены",
        "MESS_DESCRIPTION_TAB" => "Описание",
        "MESS_PROPERTIES_TAB" => "Характеристики",
        "MESS_COMMENTS_TAB" => "Комментарии",
        "DETAIL_SHOW_POPULAR" => "Y",
        "DETAIL_SHOW_VIEWED" => "Y",
        "USE_GIFTS_DETAIL" => "Y",
        "USE_GIFTS_SECTION" => "Y",
        "USE_GIFTS_MAIN_PR_SECTION_LIST" => "Y",
        "GIFTS_DETAIL_PAGE_ELEMENT_COUNT" => "3",
        "GIFTS_DETAIL_HIDE_BLOCK_TITLE" => "N",
        "GIFTS_DETAIL_BLOCK_TITLE" => "Выберите один из подарков",
        "GIFTS_DETAIL_TEXT_LABEL_GIFT" => "Подарок",
        "GIFTS_SECTION_LIST_PAGE_ELEMENT_COUNT" => "3",
        "GIFTS_SECTION_LIST_HIDE_BLOCK_TITLE" => "N",
        "GIFTS_SECTION_LIST_BLOCK_TITLE" => "Подарки к товарам этого раздела",
        "GIFTS_SECTION_LIST_TEXT_LABEL_GIFT" => "Подарок",
        "GIFTS_SHOW_DISCOUNT_PERCENT" => "Y",
        "GIFTS_SHOW_OLD_PRICE" => "Y",
        "GIFTS_SHOW_NAME" => "Y",
        "GIFTS_SHOW_IMAGE" => "Y",
        "GIFTS_MESS_BTN_BUY" => "Выбрать",
        "GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT" => "3",
        "GIFTS_MAIN_PRODUCT_DETAIL_HIDE_BLOCK_TITLE" => "N",
        "GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE" => "Выберите один из товаров, чтобы получить подарок",
        "STORES" => array(
            0 => "",
            1 => "",
        ),
        "USER_FIELDS" => array(
            0 => "",
            1 => "",
        ),
        "SHOW_EMPTY_STORE" => "Y",
        "SHOW_GENERAL_STORE_INFORMATION" => "N",
        "USE_BIG_DATA" => "N",
        "USE_ENHANCED_ECOMMERCE" => "N",
        "PAGER_BASE_LINK_ENABLE" => "N",
        "LAZY_LOAD" => "Y",
        "LOAD_ON_SCROLL" => "N",
        "SHOW_404" => "N",
        "MESSAGE_404" => "",
        "DISABLE_INIT_JS_IN_COMPONENT" => "N",
        "DETAIL_SET_VIEWED_IN_COMPONENT" => "N",
        "LIST_ENLARGE_PROP" => "-",
        "MESS_BTN_LAZY_LOAD" => "Показать ещё",
        "SEF_URL_TEMPLATES" => array(
            "sections" => "",
            "section" => "#SECTION_CODE#/",
            "element" => "#SECTION_CODE#/#ELEMENT_CODE#/",
            "compare" => "compare/",
            "smart_filter" => "#SECTION_CODE#/filter/#SMART_FILTER_PATH#/apply/",
        )
    ),
    false
);
?>

</div>