<?php

use Bitrix\Main\Page\Asset;
use Bitrix\Main\UI\Extension;

/** @var  CAllMain|CMain $APPLICATION
 ** @var  CAllUser $USER
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

IncludeTemplateLangFile($_SERVER["DOCUMENT_ROOT"] . "/bitrix/templates/" . SITE_TEMPLATE_ID . "/header.php");

CJSCore::Init(array("fx"));
Extension::load("ui.bootstrap4");

$curPage = $APPLICATION->GetCurPage(true);
$MESS["CITY_CHOOSE_TITLE"] = 'Выберите город';

$MESS["CITY_CHOOSE_PLACEHOLDER"] = 'Ваш город ...';
?><!DOCTYPE html>
<html xml:lang="<?= LANGUAGE_ID ?>" lang="<?= LANGUAGE_ID ?>">
<head>
    <title><?php $APPLICATION->ShowTitle() ?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, width=device-width">

    <link rel="shortcut icon" type="image/x-icon" href="<?php echo SITE_TEMPLATE_PATH; ?>/images/favicon.ico"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">

	
    <?php
	 Asset::getInstance()->addCss("/local/assets/js/arcticmodal/jquery.arcticmodal-0.3.css");
	 Asset::getInstance()->addCss("/local/assets/js/arcticmodal/themes/simple.css");
	 Asset::getInstance()->addJs("/local/assets/js/arcticmodal/jquery.arcticmodal-0.3.min.js");


    Asset::getInstance()->addCss("https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap");
    Asset::getInstance()->addCss("https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css");
    Asset::getInstance()->addJs("https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/script.js");
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/style.css");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/list.js");
    Asset::getInstance()->addJs('https://use.fontawesome.com/d071b13f63.js');
    Asset::getInstance()->addJs('https://code.jquery.com/jquery-3.6.0.min.js');
    Asset::getInstance()->addJs("https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.js");
    Asset::getInstance()->addCss("https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.css");
    Asset::getInstance()->addCss("https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.css");
    Asset::getInstance()->addCss("https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css");
    Asset::getInstance()->addJs("https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js");
    Asset::getInstance()->addJS('https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.6/jquery.inputmask.min.js');
    Asset::getInstance()->addJs("https://cdnjs.cloudflare.com/ajax/libs/air-datepicker/2.2.3/js/datepicker.js");
    Asset::getInstance()->addCss("https://cdnjs.cloudflare.com/ajax/libs/air-datepicker/2.2.3/css/datepicker.css");
   Asset::getInstance()->addCss("https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.css");
  

   
   $APPLICATION->ShowHead(); ?>
    <link rel="stylesheet" href="/local/assets/js/swiper/swiper-bundle.min.css">
	    <script src="/local/assets/js/swiper/swiper-bundle.min.js"></script>
</head>
<body class="bx-background-image">
<div id="panel"><?php $APPLICATION->ShowPanel(); ?>
</div>
<div id="bx_eshop_wrap">
    <header>
        <div class="header_top_panel">
            <div class="header_logo_mobile">
                <a href="<?= SITE_DIR ?>">
                    <?php $APPLICATION->IncludeComponent(
                        "bitrix:main.include",
                        "",
                        array(
                            "AREA_FILE_SHOW" => "file",
                            "PATH" => SITE_DIR . "include/company_logo_mobile.php"),
                        false
                    ); ?>
                </a>
            </div>
			<div class="right_mobile_top">
			<div class="search_mobile"></div>
            <a class="box_for_menu" data-toggle="collapse" href="#MenuHeader" aria-controls="MenuHeader"
               aria-expanded="false">
                <div id="icon" class="Icon open">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </a>
			</div>
            <div class="container_header flex_header">
                <div class="box_with_city flex_header">
                    <span class="d-flex flex-row align-items-center">
                        <img src="/local/assets/images/icon_location.svg" class="icon_location">
                        <a href="#" class="text_header"><div class="place">
                        <button type="button" class="place__button" data-toggle="modal" data-target="#placeModal">
                            <?php
                            // отключение композитного кеша вне компонента
                            Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("city-title");
                            // динамический контент?>
                            <span id="city-title" class="text_header" data-city="<?= $_SESSION["code_region"]?>">
                                        <?php include($_SERVER["DOCUMENT_ROOT"] . SITE_TEMPLATE_PATH."/geolocation/location_current.php") ?>
                                        <?php include($_SERVER["DOCUMENT_ROOT"] . SITE_TEMPLATE_PATH."/geolocation/location_select.php") ?>
                                    </span>
                            <?php Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("city-title", ""); ?>
                        </button>
                    </div></a>
                    </span>
                   <?/* <a href="/about/address-shops/" class="text_header adresa">Адреса магазинов</a>*/?>
                </div>
                <div class="box_with_menu_header flex_header flex_header_right">
                    <a href="/about/o-nas/" class="text_header">О нас</a>
                    <a href="/about/contacts/" class="text_header">Контакты</a>
                    <a href="/about/delivery/" class="text_header">Доставка и оплата</a>
					<a href="javascript:void(0)" class="text_header callback">Обратный звонок</a>
                    <?php if ($USER->IsAuthorized()) { ?>
                        <a href="/personal/support/" class="text_header" style="display:none">Поддержка</a>
                    <?php } else {?>
                        <a href="/about/FAQ/#support" class="text_header">Поддержка</a>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="header_top collapse" id="MenuHeader">
            <div class="mobile top_menu">
                <div>
                    <?php $APPLICATION->IncludeComponent(
                        "bitrix:menu",
                        "oshisha_menu_mobile",
                        array(
                            "ROOT_MENU_TYPE" => "left",
                            "MENU_CACHE_TYPE" => "A",
                            "MENU_CACHE_TIME" => "36000000",
                            "MENU_CACHE_USE_GROUPS" => "Y",
                            "MENU_THEME" => "site",
                            "CACHE_SELECTED_ITEMS" => "N",
                            "MENU_CACHE_GET_VARS" => array(),
                            "MAX_LEVEL" => "3",
                            "CHILD_MENU_TYPE" => "left",
                            "USE_EXT" => "Y",
                            "DELAY" => "N",
                            "ALLOW_MULTI_SELECT" => "N",
                            "COMPONENT_TEMPLATE" => "bootstrap_v4"
                        ),
                        false
                    ); ?>
                    <div class="ul_menu ul_menu_2">
                        <div class="box_top_panel">
                            <a href="/about/o-nas/" class="link_menu_top">
                                <span class="text_catalog_link not_weight">О нас</span>
                            </a>
                            <a href="/about/contacts/" class="link_menu_top">
                                <span class="text_catalog_link not_weight">Контакты</span>
                            </a>
                            <a href="/about/delivery/" class="link_menu_top ">
                                <span class="text_catalog_link not_weight">Доставка и оплата</span>
                            </a>

                            <a href="/about/FAQ/" class="link_menu_top ">
                                <span class="text_catalog_link not_weight">FAQ</span>
                            </a>
                        </div>
                        <span class="bx-header-phone-number">
									<?php $APPLICATION->IncludeComponent(
                                        "bitrix:main.include",
                                        "",
                                        array(
                                            "AREA_FILE_SHOW" => "file",
                                            "PATH" => SITE_DIR . "include/telephone.php"
                                        ),
                                        false
                                    ); ?>
								</span>
                        <div class="box_with_contact" >
                            <span><i class="fa fa-circle header_icon" aria-hidden="true"></i></span>
                            <span> <i class="fa fa-circle header_icon" aria-hidden="true"></i></span>

                            <a href="#" class=" ">
                                <div class="place">
                                    <button type="button" class="place__button" data-toggle="modal"
                                            data-target="#placeModal">
                                    <span class="text_catalog_link not_weight">
                                        <?php include($_SERVER["DOCUMENT_ROOT"] . SITE_TEMPLATE_PATH."/geolocation/location_current.php") ?>
                                    </span>
                                    </button>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container_header">
            <!--        header menu search/login/basket/like     -->
            <div class="header_box_logo">
                <div class="box_left_header">
                    <div class="header_logo_desktop">
                        <a href="<?= SITE_DIR ?>">
                            <?php $APPLICATION->IncludeComponent(
                                "bitrix:main.include",
                                "",
                                array(
                                    "AREA_FILE_SHOW" => "file",
                                    "PATH" => SITE_DIR . "include/company_logo.php"),
                                false
                            ); ?>
                        </a>
                    </div>
                    <div class="text_header_menu">
                            <span>Вся продукция <br>для кальяна
                </span>
                    </div>
                </div>
                <div class="box_right_header">
                    <div class="box_with_search ">
                        <?php $APPLICATION->IncludeComponent(
                            "bitrix:search.title",
                            "oshisha_search.title",
                            array(
                                "NUM_CATEGORIES" => "1",
                                "TOP_COUNT" => "5",
                                "CHECK_DATES" => "N",
                                "SHOW_OTHERS" => "N",
                                "PAGE" => SITE_DIR . "catalog/",
                                "CATEGORY_0_TITLE" => GetMessage("SEARCH_GOODS"),
                                "CATEGORY_0" => array(
                                    0 => "iblock_catalog",
                                ),
                                "CATEGORY_0_iblock_catalog" => array(
                                    0 => "all",
                                ),
                                "CATEGORY_OTHERS_TITLE" => GetMessage("SEARCH_OTHER"),
                                "SHOW_INPUT" => "Y",
                                "INPUT_ID" => "title-search-input",
                                "CONTAINER_ID" => "search",
                                "PRICE_CODE" => array(
                                    2 => "Розничная",
                                    7 => "b2b",
                                    8 => "Сайт скидка"
                                ),
                                "FILL_ITEM_ALL_PRICES"=>"Y",
                                "SHOW_PREVIEW" => "Y",
                                "PREVIEW_WIDTH" => "75",
                                "PREVIEW_HEIGHT" => "75",
                                "CONVERT_CURRENCY" => "Y",
                                "USE_LANGUAGE_GUESS" => "N"
                            ),
                            true
                        ); ?>
                    </div>

                    <div class="block_menu_mobile bx-header-personal">
                        <?php $APPLICATION->IncludeComponent(
                            "bitrix:sale.basket.basket.line",
                            "oshisha_sale.basket.basket.line",
                            array(
                                "PATH_TO_BASKET" => SITE_DIR . "personal/cart/",
                                "PATH_TO_PERSONAL" => SITE_DIR . "personal/",
                                "SHOW_PERSONAL_LINK" => "N",
                                "SHOW_NUM_PRODUCTS" => "Y",
                                "SHOW_TOTAL_PRICE" => "Y",
                                "SHOW_PRODUCTS" => "N",
                                "POSITION_FIXED" => "N",
                                "SHOW_AUTHOR" => "Y",
                                "PATH_TO_REGISTER" => SITE_DIR . "login/",
                                "PATH_TO_PROFILE" => SITE_DIR . "personal/"
                            ),
                            false,
                            array()
                        ); ?>
                    </div>
                </div>
            </div>
            <div class="box_with_menu">
                <div class="menu_header">
                    <?php $APPLICATION->IncludeComponent(
                        "bitrix:menu",
                        "oshisha_menu",
                        array(
                            "ROOT_MENU_TYPE" => "left",
                            "MENU_CACHE_TYPE" => "A",
                            "MENU_CACHE_TIME" => "36000000",
                            "MENU_CACHE_USE_GROUPS" => "Y",
                            "MENU_THEME" => "site",
                            "CACHE_SELECTED_ITEMS" => "N",
                            "MENU_CACHE_GET_VARS" => array(),
                            "MAX_LEVEL" => "4",
                            "CHILD_MENU_TYPE" => "left",
                            "USE_EXT" => "Y",
                            "DELAY" => "N",
                            "IBLOCK_ID" => IBLOCK_CATALOG, 
                            "TYPE" => "1c_catalog",
                            "ALLOW_MULTI_SELECT" => "N",
                            "COMPONENT_TEMPLATE" => "bootstrap_v4",
                            "SECTION_PAGE_URL" => "#SECTION_ID#/",
                            "DETAIL_PAGE_URL" => "#SECTION_ID#/#ELEMENT_ID#",
                        ),
                        false
                    ); ?>
                </div>
            </div>
        </div>
    </header>

    <div class="section_wrapper">
        <div class="container_header">


            <?php $needSidebar = preg_match("~^" . SITE_DIR . "(catalog|personal\/cart|personal\/order\/make)/~", $curPage); ?>
            <div >
            <div class="bx-content <?=STATIC_P?>">


