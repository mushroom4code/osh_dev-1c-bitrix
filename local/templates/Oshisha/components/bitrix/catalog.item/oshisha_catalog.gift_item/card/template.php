<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Enterego\EnteregoHelper;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $item
 * @var array $actualItem
 * @var array $minOffer
 * @var array $itemIds
 * @var array $price
 * @var array $measureRatio
 * @var bool $haveOffers
 * @var bool $showSubscribe
 * @var array $morePhoto
 * @var bool $showSlider
 * @var bool $itemHasDetailUrl
 * @var string $imgTitle
 * @var string $productTitle
 * @var string $buttonSizeClass
 * @var CatalogSectionComponent $component
 */
$mainId = $this->GetEditAreaId($item['ID']);

$arItemIDs = array(
    'ID' => $mainId,
    'DISCOUNT_PERCENT_ID' => $mainId . '_dsc_pict',
    'STICKER_ID' => $mainId . '_sticker',
    'BIG_SLIDER_ID' => $mainId . '_big_slider',
    'BIG_IMG_CONT_ID' => $mainId . '_bigimg_cont',
    'SLIDER_CONT_ID' => $mainId . '_slider_cont',
    'OLD_PRICE_ID' => $mainId . '_old_price',
    'PRICE_ID' => $mainId . '_price',
    'DISCOUNT_PRICE_ID' => $mainId . '_price_discount',
    'PRICE_TOTAL' => $mainId . '_price_total',
    'SLIDER_CONT_OF_ID' => $mainId . '_slider_cont_',
    'QUANTITY_ID' => $mainId . '_quantity',
    'QUANTITY_DOWN_ID' => $mainId . '_quant_down',
    'QUANTITY_UP_ID' => $mainId . '_quant_up',
    'QUANTITY_MEASURE' => $mainId . '_quant_measure',
    'QUANTITY_LIMIT' => $mainId . '_quant_limit',
    'BUY_LINK' => $mainId . '_buy_link',
    'ADD_BASKET_LINK' => $mainId . '_add_basket_link',
    'BASKET_ACTIONS_ID' => $mainId . '_basket_actions',
    'NOT_AVAILABLE_MESS' => $mainId . '_not_avail',
    'COMPARE_LINK' => $mainId . '_compare_link',
    'TREE_ID' => $mainId . '_skudiv',
    'DISPLAY_PROP_DIV' => $mainId . '_sku_prop',
    'DISPLAY_MAIN_PROP_DIV' => $mainId . '_main_sku_prop',
    'OFFER_GROUP' => $mainId . '_set_group_',
    'BASKET_PROP_DIV' => $mainId . '_basket_prop',
    'SUBSCRIBE_LINK' => $mainId . '_subscribe',
    'TABS_ID' => $mainId . '_tabs',
    'TAB_CONTAINERS_ID' => $mainId . '_tab_containers',
    'SMALL_CARD_PANEL_ID' => $mainId . '_small_card_panel',
    'TABS_PANEL_ID' => $mainId . '_tabs_panel'
);
$favorite = '';
$styleForTaste = '';
$taste = $item['PROPERTIES'][PROPERTY_KEY_VKUS];
$codeProp = $item['PROPERTIES']['CML2_TRAITS'];
$useDiscount = $item['PROPERTIES']['USE_DISCOUNT'];
$newProduct = $item['PROPERTIES'][PROP_NEW];
$hitProduct = $item['PROPERTIES'][PROP_HIT];

$rowResHidePrice = $item['PROPERTIES']['SEE_PRODUCT_AUTH']['VALUE'];
$show_price = true;
if ($rowResHidePrice == 'Нет' && !$USER->IsAuthorized()) {
    $show_price = false;
}


$priceBasket = 0;
$styleForNo = '';
if ($item['PRODUCT']['QUANTITY'] == '0') {
    /*$styleForTaste = 'blur_check';*/
    $styleForNo = 'not_av';
}

foreach ($item['ACTUAL_BASKET'] as $key => $val) {
    if ($key == $item['ID']) {
        $priceBasket = $val;
    }
}

$productTitle = str_replace("\xC2\xA0", " ", $productTitle);
$productTitle = str_replace("\xC2\xA0", " ", $productTitle);
$item['DETAIL_PAGE_URL'] = '/catalog/product/' . $item['CODE'] . '/'; ?>
<div class="<?= ($item['SECOND_PICT'] ? 'bx_catalog_item double' : 'bx_catalog_item'); ?>
<?php if (!$show_price) { ?> blur_photo <?php } ?>">
    <div class="bx_catalog_item_container product-item align-items-center <?php if (count($taste['VALUE']) > 0): ?>is-taste<?php endif; ?>">
        <?php if (($newProduct['VALUE'] == 'Да') && ($hitProduct['VALUE'] != 'Да')) { ?>
            <span class="taste new-product" data-background="#F55F5C">NEW</span>
        <?php }
        if ($hitProduct['VALUE'] == 'Да') { ?>
            <span class="taste new-product" data-background="#F55F5C">ХИТ</span>
        <?php }
        if (count($taste['VALUE']) > 0) { ?>
            <div class="toggle_taste card-price">
                <div class="variation_taste">
                    <?php
                    foreach ($taste['VALUE'] as $key => $name) {
                        foreach ($taste['VALUE_XML_ID'] as $keys => $value) {
                            if ($key === $keys) {
                                $color = explode('#', $value); ?>
                                <span class="taste" data-background="<?= '#' . $color[1] ?>" id="<?= $color[0] ?>">
                                    <?= $name ?>
                                </span>
                            <?php }
                        }
                    } ?>
                </div>
            </div>
        <?php } ?>
        <div class="image_cart">
            <a class=" <?= $styleForTaste ?>"
               href="<?= $item['DETAIL_PAGE_URL']; ?>">
                <?php if (!empty($item['PREVIEW_PICTURE']['SRC'])) { ?>
                    <img src="<?= $item['PREVIEW_PICTURE']['SRC']; ?>" alt="<?= $item['PREVIEW_PICTURE']['SRC']; ?>"/>
                <?php } else { ?>
                    <img src="/local/templates/Oshisha/images/no-photo.gif"
                         alt="<?= $item['PREVIEW_PICTURE']['SRC']; ?>"/>
                <?php } ?>
            </a>
        </div>

        <div class="box_with_title_like d-flex text-center">
            <?php if (count($taste['VALUE']) > 0) { ?>
                <div class="toggle_taste_line">
                    <div class="variation_taste">
                        <?php

                        foreach ($taste['VALUE'] as $key => $name) {
                            foreach ($taste['VALUE_XML_ID'] as $keys => $value) {
                                if ($key === $keys) {
                                    $color = explode('#', $value); ?>
                                    <span class="taste" data-background="<?= '#' . $color[1] ?>" id="<?= $color[0] ?>">
                                    <?= $name ?>
                            </span>
                                <?php }
                            }
                        } ?>
                    </div>
                </div>
            <?php } ?>

            <?php if ($GLOBALS['UserTypeOpt'] === true) { ?>
                <div class="codeProduct font-10 mr-4">
                    <?php
                    foreach ($codeProp['DESCRIPTION'] as $key => $code) {
                        if ($code === 'Код') {
                            echo $codeProp['VALUE'][$key];
                        }
                    } ?>
                </div>
            <?php } ?>
            <div class="box_with_text">
                <a class="bx_catalog_item_title <?= $styleForNo ?>" href="<?= $item['DETAIL_PAGE_URL']; ?>"
                   title="<?= $productTitle; ?>">
                    <?= $productTitle; ?>
                </a>
                <?php if (count($taste['VALUE']) > 0) { ?>
                <?php }
                if (!empty($item['DETAIL_TEXT'])) { ?>
                    <p class="detail-text"><?= $item['DETAIL_TEXT'] ?></p>
                <?php } ?>
            </div>
        </div>
        <?php
        $showSubscribeBtn = false;
        $compareBtnMessage = ($arParams['MESS_BTN_COMPARE'] != '' ? $arParams['MESS_BTN_COMPARE'] : GetMessage('CT_BCT_TPL_MESS_BTN_COMPARE'));
        if (!isset($item['OFFERS']) || empty($item['OFFERS'])) { ?>
            <div class="bx_catalog_item_controls">
            <?php if ($price['PRICE_DATA'][1]['PRICE'] !== '0' && $item['PRODUCT']['QUANTITY'] !== '0') { ?>
                <div class="justify-content-center">
                    <?php if ($show_price) { ?>
                        <div class="product-item-button-container" id="<?= $itemIds['BASKET_ACTIONS'] ?>">
                            <button class="btn btn_red js-add2basket-gift"
                                    data-product_id="<?= $item['ID']; ?>"
                                    id="<?= $itemIds['BUY_LINK'] ?>"
                                    href="javascript:void(0)" rel="nofollow">
                                <?= ($arParams['ADD_TO_BASKET_ACTION'] === 'BUY' ? $arParams['MESS_BTN_BUY'] : $arParams['MESS_BTN_ADD_TO_BASKET']) ?>
                            </button>
                        </div>
                    <?php } ?>
                </div>
                <div style="clear: both;"></div>
                </div>
            <?php }
            $emptyProductProperties = empty($item['PRODUCT_PROPERTIES']);
            if ('Y' == $arParams['ADD_PROPERTIES_TO_BASKET'] && !$emptyProductProperties) { ?>
                <div id="<?= $arItemIDs['BASKET_PROP_DIV']; ?>" style="display: none;">
                    <?php
                    if (!empty($item['PRODUCT_PROPERTIES_FILL'])) {
                        foreach ($item['PRODUCT_PROPERTIES_FILL'] as $propID => $propInfo) {
                            ?>
                            <input type="hidden"
                                   name="<?= $arParams['PRODUCT_PROPS_VARIABLE']; ?>[<?= $propID; ?>]"
                                   value="<?= htmlspecialcharsbx($propInfo['ID']); ?>">
                            <?php if (isset($item['PRODUCT_PROPERTIES'][$propID]))
                                unset($item['PRODUCT_PROPERTIES'][$propID]);
                        }
                    }
                    $emptyProductProperties = empty($item['PRODUCT_PROPERTIES']); ?>
                </div>
                <?php
            }
        } else {
            if ('Y' == $arParams['PRODUCT_DISPLAY_MODE']) {
                $canBuy = $item['JS_OFFERS'][$item['OFFERS_SELECTED']]['CAN_BUY'];

                unset($canBuy);
            }
            $boolShowOfferProps = ('Y' == $arParams['PRODUCT_DISPLAY_MODE'] && $item['OFFERS_PROPS_DISPLAY']);
            $boolShowProductProps = (isset($arItem['DISPLAY_PROPERTIES']) && !empty($arItem['DISPLAY_PROPERTIES']));
            if ($boolShowProductProps || $boolShowOfferProps) { ?>
                <div class="bx_catalog_item_articul">
                    <?php if ($boolShowProductProps) {
                        foreach ($item['DISPLAY_PROPERTIES'] as $arOneProp) {
                            ?><br><strong><?= $arOneProp['NAME']; ?></strong> <?
                            echo(
                            is_array($arOneProp['DISPLAY_VALUE'])
                                ? implode(' / ', $arOneProp['DISPLAY_VALUE'])
                                : $arOneProp['DISPLAY_VALUE']
                            );
                        }
                    }
                    if ($boolShowOfferProps) { ?>
                        <span id="<?= $arItemIDs['DISPLAY_PROP_DIV']; ?>"
                              style="display: none;"></span>
                    <?php } ?>
                </div>
                <?php
            }
            if ('Y' == $arParams['PRODUCT_DISPLAY_MODE']) {
                if (!empty($item['OFFERS_PROP'])) {
                    $arSkuProps = array();
                    if ($item['OFFERS_PROPS_DISPLAY']) {
                        foreach ($item['JS_OFFERS'] as $keyOffer => $arJSOffer) {
                            $strProps = '';
                            if (!empty($arJSOffer['DISPLAY_PROPERTIES'])) {
                                foreach ($arJSOffer['DISPLAY_PROPERTIES'] as $arOneProp) {
                                    $strProps .= '<br>' . $arOneProp['NAME'] . ' <strong>' . (
                                        is_array($arOneProp['VALUE'])
                                            ? implode(' / ', $arOneProp['VALUE'])
                                            : $arOneProp['VALUE']
                                        ) . '</strong>';
                                }
                            }
                            $item['JS_OFFERS'][$keyOffer]['DISPLAY_PROPERTIES'] = $strProps;
                        }
                    }


                }
            }

        }
        ?>
    </div>
    <div id="result_box"></div>
</div>
