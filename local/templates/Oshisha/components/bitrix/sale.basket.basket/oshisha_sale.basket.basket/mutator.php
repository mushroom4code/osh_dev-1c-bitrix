<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Sale\Fuser;
use Bitrix\Sale\PriceMaths;
use Enterego\EnteregoHelper;

/**
 *
 * This file modifies result for every request (including AJAX).
 * Use it to edit output result for "{{ mustache }}" templates.
 *
 * @var array $result
 */
$mobileColumns = isset($this->arParams['COLUMNS_LIST_MOBILE'])
    ? $this->arParams['COLUMNS_LIST_MOBILE']
    : $this->arParams['COLUMNS_LIST'];
$mobileColumns = array_fill_keys($mobileColumns, true);

$result['BASKET_ITEM_RENDER_DATA'] = array();

// TODO - доработать получение базовой цены
$result['BASKET_ITEM_RENDER_DATA_CUSTOM'] = EnteregoHelper::basketCustomSort($this->basketItems);

$item = $this->basketItems;
global $USER, $SETTINGS;

$id_USER = $USER->GetID();
$FUser_id = Fuser::getId($id_USER);
$item_id = [];

foreach ($item as $row) {
    $item_id[] = $row['ID'];
}

$count_likes = DataBase_like::getLikeFavoriteAllProduct($item_id, $FUser_id);

foreach ($item as $row) {

    //enterego - remove gift from basket if condition not execute
    if (EnteregoHelper::productIsGift($row['PRODUCT_ID']) && $row['PRICE'] !== 0.0) {
        (new CSaleBasket)->Delete($row['ID']);
        unset($row);
        continue;
    }

    if (intval($SETTINGS['MAX_QUANTITY']) > 0 && $SETTINGS['MAX_QUANTITY'] < $row['AVAILABLE_QUANTITY'])
        $row['AVAILABLE_QUANTITY'] = $SETTINGS['MAX_QUANTITY'];

    $product_prices = '';
    $price = [];
    $show_product_prices = false;
    $propsUseSale = CIBlockElement::GetProperty(IBLOCK_CATALOG, $row['PRODUCT_ID'], array(), array('CODE' => 'USE_DISCOUNT'));
    $newProp = $propsUseSale->Fetch();
    $res = CIBlockElement::GetList(
        array(),
        array("ID" => $row['PRODUCT_ID']),
        false,
        false,
        array(
            "QUANTITY",
            "PRICE_" . RETAIL_PRICE,
            "PRICE_" . B2B_PRICE,
            'PRICE_' . BASIC_PRICE,
            "PRICE_" . SALE_PRICE_TYPE_ID)
    );
    $showDiscountPrice = (float)$row['DISCOUNT_PRICE'] > 0;
    if ($ar_res = $res->fetch()) {
        if (!empty($ar_res)) {
            //set available quantity from storage quantity
            $row['AVAILABLE_QUANTITY'] =  $ar_res['QUANTITY'];
            $str_product_prices = '';
            $product_prices_sql = $ar_res["PRICE_" . BASIC_PRICE];
            if (($newProp['VALUE_XML_ID'] == 'true' || USE_CUSTOM_SALE_PRICE) && (!empty($ar_res["PRICE_" . SALE_PRICE_TYPE_ID])
                    && ((int)$product_prices_sql > (int)$ar_res["PRICE_" . SALE_PRICE_TYPE_ID])) && !$showDiscountPrice) {
                $str_product_prices = explode('.', $product_prices_sql);
                $price['SALE_PRICE'] = $str_product_prices[0] . ' ₽';
                $show_product_prices = true;

            } else {
                if ((int)$row['PRICE_TYPE_ID'] == BASIC_PRICE && !$showDiscountPrice) {
                    $show_product_prices = true;
                    $str_product_prices = explode('.', $ar_res["PRICE_" . RETAIL_PRICE]);
                } else if ((int)$row['PRICE_TYPE_ID'] == B2B_PRICE && !$showDiscountPrice) {
                    $show_product_prices = true;
                    $str_product_prices = explode('.', $ar_res["PRICE_" . BASIC_PRICE]);
                }
            }

            if (!empty($ar_res["PRICE_" . RETAIL_PRICE])) {
                $price['PRICE_DATA'][0]['VAL'] = explode('.', $ar_res["PRICE_" . RETAIL_PRICE])[0] * $row['MEASURE_RATIO'];
                $price['PRICE_DATA'][0]['NAME'] = 'Розничная (до 10к)';
            }
            if (!empty($ar_res["PRICE_" . BASIC_PRICE])) {
                $price['PRICE_DATA'][1]['VAL'] = explode('.', $ar_res["PRICE_" . BASIC_PRICE])[0] * $row['MEASURE_RATIO'];
                $price['PRICE_DATA'][1]['NAME'] = 'Основная (до 30к)';
            }
            if (!empty($ar_res["PRICE_" . B2B_PRICE])) {
                $price['PRICE_DATA'][2]['VAL'] = explode('.', $ar_res["PRICE_" . B2B_PRICE])[0] * $row['MEASURE_RATIO'];
                $price['PRICE_DATA'][2]['NAME'] = 'b2b (от 30к)';
            }

            $product_prices = $str_product_prices[0] . '₽';
            $sale_price_val = (int)$str_product_prices[0];
            $sum_sale = (((round($row['QUANTITY']) / $row['MEASURE_RATIO']) * $price['PRICE_DATA'][0]['VAL']) - round($row['SUM_VALUE']));
            $sum_old = ((round($row['QUANTITY']) / $row['MEASURE_RATIO']) * $price['PRICE_DATA'][0]['VAL']);
        }
    }

    $activeUnitId = $row['PROPERTY_'.PROPERTY_ACTIVE_UNIT.'_VALUE'];

    if (!empty($activeUnitId)) {
        $row[PROPERTY_ACTIVE_UNIT] = CCatalogMeasure::GetList(array(), array("CODE" => $activeUnitId))->fetch();
        if (!empty($row[PROPERTY_ACTIVE_UNIT])) {
            $row[PROPERTY_ACTIVE_UNIT] = $row[PROPERTY_ACTIVE_UNIT]['SYMBOL_RUS'];
        } else {
            $row[PROPERTY_ACTIVE_UNIT] = 'шт';
        }
    } else {
        $row[PROPERTY_ACTIVE_UNIT] = 'шт';
    }

    $rowData = array(
        'ID' => $row['ID'],
        'PRODUCT_ID' => $row['PRODUCT_ID'],
        'NAME' => isset($row['~NAME']) ? $row['~NAME'] : $row['NAME'],
        'QUANTITY' => $row['QUANTITY'],
        'QUANTITY_WITH_RATIO' =>$row['QUANTITY'] / $row['MEASURE_RATIO'],
        'PROPS' => $row['PROPS'],
        'PROPS_ALL' => $row['PROPS_ALL'],
        'HASH' => $row['HASH'],
        'PRICES_NET' => $price,
        'SORT' => $row['SORT'],
        'DETAIL_PAGE_URL' => $row['DETAIL_PAGE_URL'],
        'CURRENCY' => $row['CURRENCY'],
        'DISCOUNT_PRICE_PERCENT' => $row['DISCOUNT_PRICE_PERCENT'],
        'DISCOUNT_PRICE_PERCENT_FORMATED' => $row['DISCOUNT_PRICE_PERCENT_FORMATED'],
        'SHOW_DISCOUNT_PRICE' => $showDiscountPrice,
        'PRICE' => $row['PRICE'],
        'PRICE_FORMATED' => $row['PRICE_FORMATED'],
        'FULL_PRICE' => $row['FULL_PRICE'],
        'FULL_PRICE_FORMATED' => $row['FULL_PRICE_FORMATED'],
        'DISCOUNT_PRICE' => $row['DISCOUNT_PRICE'],
        'DISCOUNT_PRICE_FORMATED' => $row['DISCOUNT_PRICE_FORMATED'],
        'SUM_PRICE' => $row['SUM_VALUE'],
        'SUM_PRICE_FORMATED' => $row['SUM'],
        'SUM_SALE_PRICE' => '',
        'SUM_FULL_PRICE' => $row['SUM_FULL_PRICE'],
        'SUM_FULL_PRICE_FORMATED' => $row['SUM_FULL_PRICE_FORMATED'],
        'SUM_DISCOUNT_PRICE' => $row['SUM_DISCOUNT_PRICE'],
        'SUM_DISCOUNT_PRICE_FORMATED' => $row['SUM_DISCOUNT_PRICE_FORMATED'],
        //SALE PRICE
        'SALE_PRICE' => $product_prices,
        'SALE_PRICE_VAL' => $sum_sale ?? 0,
        'SHOW_SALE_PRICE' => $show_product_prices,
        "SUM_OLD" => $sum_old ?? 0,
        //
        'MEASURE_RATIO' => isset($row['MEASURE_RATIO']) ? $row['MEASURE_RATIO'] : 1,
        'MEASURE_TEXT' => $row['MEASURE_TEXT'],
        'AVAILABLE_QUANTITY' => $row['AVAILABLE_QUANTITY'],
        'AVAILABLE_QUANTITY_WITH_RATIO' => $row['AVAILABLE_QUANTITY'] / $row['MEASURE_RATIO'],
        'CHECK_MAX_QUANTITY' => $row['CHECK_MAX_QUANTITY'],
        'MODULE' => $row['MODULE'],
        'PRODUCT_PROVIDER_CLASS' => $row['PRODUCT_PROVIDER_CLASS'],
        'NOT_AVAILABLE' => $row['NOT_AVAILABLE'] === true,
        'DELAYED' => $row['DELAY'] === 'Y',
        'SKU_BLOCK_LIST' => array(),
        'COLUMN_LIST' => array(),
        'SHOW_LABEL' => false,
        'LABEL_VALUES' => array(),
        'BRAND' => isset($row[$this->arParams['BRAND_PROPERTY'] . '_VALUE'])
            ? $row[$this->arParams['BRAND_PROPERTY'] . '_VALUE']
            : '',
        'GIFT' => $row['GIFT'] ?? false,
        'ACTIVE_UNIT' => $row[PROPERTY_ACTIVE_UNIT],
    );
    foreach ($count_likes['USER'] as $keyLike => $count) {
        if ($keyLike == $row['ID']) {
            //$item['COUNT_LIKE'] = $count['Like'][0];
            $rowData['COUNT_FAV'] = $count['Fav'][0];
            global $rowFavData;
            $rowFavData[$row['ID']] = $count['Fav'][0];
        }
    }
    $res = EnteregoHelper::getItems($row['PRODUCT_ID'], 'VKUS');
    if (!empty($res)) {
        $rowData['PROPS'][PROPERTY_KEY_VKUS] = $res[PROPERTY_KEY_VKUS];
    }

    // show price including ratio
    if ($rowData['MEASURE_RATIO'] != 1) {
        $price = PriceMaths::roundPrecision($rowData['PRICE'] * $rowData['MEASURE_RATIO']);
        if ($price != $rowData['PRICE']) {
            $rowData['PRICE'] = $price;
            $rowData['PRICE_FORMATED'] = CCurrencyLang::CurrencyFormat($price, $rowData['CURRENCY'], true);
        }

        $fullPrice = PriceMaths::roundPrecision($rowData['FULL_PRICE'] * $rowData['MEASURE_RATIO']);
        if ($fullPrice != $rowData['FULL_PRICE']) {
            $rowData['FULL_PRICE'] = $fullPrice;
            $rowData['FULL_PRICE_FORMATED'] = CCurrencyLang::CurrencyFormat($fullPrice, $rowData['CURRENCY'], true);
        }

        $discountPrice = PriceMaths::roundPrecision($rowData['DISCOUNT_PRICE'] * $rowData['MEASURE_RATIO']);
        if ($discountPrice != $rowData['DISCOUNT_PRICE']) {
            $rowData['DISCOUNT_PRICE'] = $discountPrice;
            $rowData['DISCOUNT_PRICE_FORMATED'] = CCurrencyLang::CurrencyFormat($discountPrice, $rowData['CURRENCY'], true);
        }
    }

    $rowData['SHOW_PRICE_FOR'] = (float)$rowData['QUANTITY'] !== (float)$rowData['MEASURE_RATIO'];

    $hideDetailPicture = false;

    if (!empty($row['PREVIEW_PICTURE_SRC'])) {
        $rowData['IMAGE_URL'] = $row['PREVIEW_PICTURE_SRC'];
    } elseif (!empty($row['DETAIL_PICTURE_SRC'])) {
        $hideDetailPicture = true;
        $rowData['IMAGE_URL'] = $row['DETAIL_PICTURE_SRC'];
    }

    if (!empty($row['SKU_DATA'])) {
        $propMap = array();

        foreach ($row['PROPS'] as $prop) {
            $propMap[$prop['CODE']] = !empty($prop['~VALUE']) ? $prop['~VALUE'] : $prop['VALUE'];
        }

        $notSelectable = true;

        foreach ($row['SKU_DATA'] as $skuBlock) {
            $skuBlockData = array(
                'ID' => $skuBlock['ID'],
                'CODE' => $skuBlock['CODE'],
                'NAME' => $skuBlock['NAME']
            );

            $isSkuSelected = false;
            $isImageProperty = false;

            if (count($skuBlock['VALUES']) > 1) {
                $notSelectable = false;
            }

            foreach ($skuBlock['VALUES'] as $skuItem) {
                if ($skuBlock['TYPE'] === 'S' && $skuBlock['USER_TYPE'] === 'directory') {
                    $valueId = $skuItem['XML_ID'];
                } elseif ($skuBlock['TYPE'] === 'E') {
                    $valueId = $skuItem['ID'];
                } else {
                    $valueId = $skuItem['NAME'];
                }

                $skuValue = array(
                    'ID' => $skuItem['ID'],
                    'NAME' => $skuItem['NAME'],
                    'SORT' => $skuItem['SORT'],
                    'PICT' => !empty($skuItem['PICT']) ? $skuItem['PICT']['SRC'] : false,
                    'XML_ID' => !empty($skuItem['XML_ID']) ? $skuItem['XML_ID'] : false,
                    'VALUE_ID' => $valueId,
                    'PROP_ID' => $skuBlock['ID'],
                    'PROP_CODE' => $skuBlock['CODE']
                );

                if (
                    !empty($propMap[$skuBlockData['CODE']])
                    && ($propMap[$skuBlockData['CODE']] == $skuItem['NAME'] || $propMap[$skuBlockData['CODE']] == $skuItem['XML_ID'])
                ) {
                    $skuValue['SELECTED'] = true;
                    $isSkuSelected = true;
                }

                $skuBlockData['SKU_VALUES_LIST'][] = $skuValue;
                $isImageProperty = $isImageProperty || !empty($skuItem['PICT']);
            }

            if (!$isSkuSelected && !empty($skuBlockData['SKU_VALUES_LIST'][0])) {
                $skuBlockData['SKU_VALUES_LIST'][0]['SELECTED'] = true;
            }

            $skuBlockData['IS_IMAGE'] = $isImageProperty;

            $rowData['SKU_BLOCK_LIST'][] = $skuBlockData;
        }
    }

    if ($row['NOT_AVAILABLE']) {
        foreach ($rowData['SKU_BLOCK_LIST'] as $blockKey => $skuBlock) {
            if (!empty($skuBlock['SKU_VALUES_LIST'])) {
                if ($notSelectable) {
                    foreach ($skuBlock['SKU_VALUES_LIST'] as $valueKey => $skuValue) {
                        $rowData['SKU_BLOCK_LIST'][$blockKey]['SKU_VALUES_LIST'][0]['NOT_AVAILABLE_OFFER'] = true;
                    }
                } elseif (!isset($rowData['SKU_BLOCK_LIST'][$blockKey + 1])) {
                    foreach ($skuBlock['SKU_VALUES_LIST'] as $valueKey => $skuValue) {
                        if ($skuValue['SELECTED']) {
                            $rowData['SKU_BLOCK_LIST'][$blockKey]['SKU_VALUES_LIST'][$valueKey]['NOT_AVAILABLE_OFFER'] = true;
                        }
                    }
                }
            }
        }
    }

    if (!empty($result['GRID']['HEADERS']) && is_array($result['GRID']['HEADERS'])) {
        $skipHeaders = [
            'NAME' => true,
            'QUANTITY' => true,
            'PRICE' => true,
            'PREVIEW_PICTURE' => true,
            'SUM' => true,
            'PROPS' => true,
            'DELETE' => true,
            'DELAY' => true,
        ];

        foreach ($result['GRID']['HEADERS'] as &$value) {
            if (
                empty($value['id'])
                || isset($skipHeaders[$value['id']])
                || ($hideDetailPicture && $value['id'] === 'DETAIL_PICTURE')) {
                continue;
            }

            if ($value['id'] === 'DETAIL_PICTURE') {
                $value['name'] = Loc::getMessage('SBB_DETAIL_PICTURE_NAME');

                if (!empty($row['DETAIL_PICTURE_SRC'])) {
                    $rowData['COLUMN_LIST'][] = array(
                        'CODE' => $value['id'],
                        'NAME' => $value['name'],
                        'VALUE' => array(
                            array(
                                'IMAGE_SRC' => $row['DETAIL_PICTURE_SRC'],
                                'IMAGE_SRC_2X' => $row['DETAIL_PICTURE_SRC_2X'],
                                'IMAGE_SRC_ORIGINAL' => $row['DETAIL_PICTURE_SRC_ORIGINAL'],
                                'INDEX' => 0
                            )
                        ),
                        'IS_IMAGE' => true,
                        'HIDE_MOBILE' => !isset($mobileColumns[$value['id']])
                    );
                }
            } elseif ($value['id'] === 'PREVIEW_TEXT') {
                $value['name'] = Loc::getMessage('SBB_PREVIEW_TEXT_NAME');

                if ($row['PREVIEW_TEXT_TYPE'] === 'text' && !empty($row['PREVIEW_TEXT'])) {
                    $rowData['COLUMN_LIST'][] = array(
                        'CODE' => $value['id'],
                        'NAME' => $value['name'],
                        'VALUE' => $row['PREVIEW_TEXT'],
                        'IS_TEXT' => true,
                        'HIDE_MOBILE' => !isset($mobileColumns[$value['id']])
                    );
                }
            } elseif ($value['id'] === 'TYPE') {
                $value['name'] = Loc::getMessage('SBB_PRICE_TYPE_NAME');

                if (!empty($row['NOTES'])) {
                    $rowData['COLUMN_LIST'][] = array(
                        'CODE' => $value['id'],
                        'NAME' => $value['name'],
                        'VALUE' => isset($row['~NOTES']) ? $row['~NOTES'] : $row['NOTES'],
                        'IS_TEXT' => true,
                        'HIDE_MOBILE' => !isset($mobileColumns[$value['id']])
                    );
                }
            } elseif ($value['id'] === 'DISCOUNT') {
                $value['name'] = Loc::getMessage('SBB_DISCOUNT_NAME');

                if ($row['DISCOUNT_PRICE_PERCENT'] > 0 && !empty($row['DISCOUNT_PRICE_PERCENT_FORMATED'])) {
                    $rowData['COLUMN_LIST'][] = array(
                        'CODE' => $value['id'],
                        'NAME' => $value['name'],
                        'VALUE' => $row['DISCOUNT_PRICE_PERCENT_FORMATED'],
                        'IS_TEXT' => true,
                        'HIDE_MOBILE' => !isset($mobileColumns[$value['id']])
                    );
                }
            } elseif ($value['id'] === 'WEIGHT') {
                $value['name'] = Loc::getMessage('SBB_WEIGHT_NAME');

                if (!empty($row['WEIGHT_FORMATED'])) {
                    $rowData['COLUMN_LIST'][] = array(
                        'CODE' => $value['id'],
                        'NAME' => $value['name'],
                        'VALUE' => $row['WEIGHT_FORMATED'],
                        'IS_TEXT' => true,
                        'HIDE_MOBILE' => !isset($mobileColumns[$value['id']])
                    );
                }
            } elseif (!empty($row[$value['id'] . '_SRC'])) {
                $i = 0;

                foreach ($row[$value['id'] . '_SRC'] as &$image) {
                    $image['INDEX'] = $i++;
                }

                $rowData['COLUMN_LIST'][] = array(
                    'CODE' => $value['id'],
                    'NAME' => $value['name'],
                    'VALUE' => $row[$value['id'] . '_SRC'],
                    'IS_IMAGE' => true,
                    'HIDE_MOBILE' => !isset($mobileColumns[$value['id']])
                );
            } elseif (!empty($row[$value['id'] . '_DISPLAY'])) {
                $rowData['COLUMN_LIST'][] = array(
                    'CODE' => $value['id'],
                    'NAME' => $value['name'],
                    'VALUE' => $row[$value['id'] . '_DISPLAY'],
                    'IS_TEXT' => true,
                    'HIDE_MOBILE' => !isset($mobileColumns[$value['id']])
                );
            } elseif (!empty($row[$value['id'] . '_LINK'])) {
                $linkValues = array();

                foreach ($row[$value['id'] . '_LINK'] as $index => $link) {
                    $linkValues[] = array(
                        'LINK' => $link,
                        'IS_LAST' => !isset($row[$value['id'] . '_LINK'][$index + 1])
                    );
                }

                $rowData['COLUMN_LIST'][] = array(
                    'CODE' => $value['id'],
                    'NAME' => $value['name'],
                    'VALUE' => $linkValues,
                    'IS_LINK' => true,
                    'HIDE_MOBILE' => !isset($mobileColumns[$value['id']])
                );
            } elseif (!empty($row[$value['id']])) {
                $rowData['COLUMN_LIST'][] = array(
                    'CODE' => $value['id'],
                    'NAME' => $value['name'],
                    'VALUE' => $row[$value['id']],
                    'IS_TEXT' => true,
                    'HIDE_MOBILE' => !isset($mobileColumns[$value['id']])
                );
            }
        }

        unset($value);
    }

    if (!empty($row['LABEL_ARRAY_VALUE'])) {
        $labels = array();

        foreach ($row['LABEL_ARRAY_VALUE'] as $code => $value) {
            $labels[] = array(
                'NAME' => $value,
                'HIDE_MOBILE' => !isset($this->arParams['LABEL_PROP_MOBILE'][$code])
            );
        }

        $rowData['SHOW_LABEL'] = true;
        $rowData['LABEL_VALUES'] = $labels;
    }

    $result['BASKET_ITEM_RENDER_DATA'][] = $rowData;
}

$totalData = array(
    'DISABLE_CHECKOUT' => (int)$result['ORDERABLE_BASKET_ITEMS_COUNT'] === 0,
    'PRICE' => $result['allSum'],
    'PRICE_FORMATED' => $result['allSum_FORMATED'],
    'PRICE_WITHOUT_DISCOUNT_FORMATED' => $result['PRICE_WITHOUT_DISCOUNT'],
    'CURRENCY' => $result['CURRENCY']
);

if ($result['DISCOUNT_PRICE_ALL'] > 0) {
    $totalData['DISCOUNT_PRICE_FORMATED'] = $result['DISCOUNT_PRICE_FORMATED'];
}

if ($result['allWeight'] > 0) {
    $totalData['WEIGHT_FORMATED'] = $result['allWeight_FORMATED'];
}

if ($this->priceVatShowValue === 'Y') {
    $totalData['SHOW_VAT'] = true;
    $totalData['VAT_SUM_FORMATED'] = $result['allVATSum_FORMATED'];
    $totalData['SUM_WITHOUT_VAT_FORMATED'] = $result['allSum_wVAT_FORMATED'];
}

if ($this->hideCoupon !== 'Y' && !empty($result['COUPON_LIST'])) {
    $totalData['COUPON_LIST'] = $result['COUPON_LIST'];

    foreach ($totalData['COUPON_LIST'] as &$coupon) {
        if ($coupon['JS_STATUS'] === 'ENTERED') {
            $coupon['CLASS'] = 'danger';
        } elseif ($coupon['JS_STATUS'] === 'APPLYED') {
            $coupon['CLASS'] = 'muted';
        } else {
            $coupon['CLASS'] = 'danger';
        }
    }
}

$result['TOTAL_RENDER_DATA'] = $totalData;
