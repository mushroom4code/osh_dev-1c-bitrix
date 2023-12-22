<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Catalog\PriceTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Context;
use Bitrix\Main\DB\SqlQueryException;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Sale\Fuser;
use Enterego\EnteregoBasket;
use Enterego\EnteregoGroupedProducts;

CModule::IncludeModule("iblock");
Loader::includeModule('main');
CModule::IncludeModule("sale");

$request = Context::getCurrent()->getRequest();
$action = $request->getJsonList()->get('action');

/**
 * @param $prodId
 * @param $listGroupedProduct
 * @return array
 * @throws ArgumentException
 * @throws ObjectPropertyException
 * @throws SystemException
 */
function getGroupedProduct($prodId, $listGroupedProduct,$arItems)
{
    $prices = $rsPrice = [];
    $arItems['GROUPED_PRODUCTS'] = $arItems['GROUPED_PROPS_DATA'] = $arResult = [];
    if (!empty($prodId)) {
        $arResult = EnteregoGroupedProducts::getListGroupedProduct($prodId, $listGroupedProduct, $arItems);
        $arResult['SETTING'] = EnteregoGroupedProducts::getDataPropOffers();
        $arResult['PRICE_GREAT'] = BASIC_PRICE;
        $arResult['SALE'] = USE_CUSTOM_SALE_PRICE;

        $rsPrice = PriceTable::getList([
            'select' => ['PRODUCT_ID', 'PRICE', 'CATALOG_GROUP_ID', 'CATALOG_GROUP'],
            'filter' => [
                'PRODUCT_ID' => $listGroupedProduct,
                'CATALOG_GROUP_ID' => [SALE_PRICE_TYPE_ID, B2B_PRICE],
            ],
        ])->fetchAll();

        foreach ($rsPrice as $price) {
            $prices[$price['PRODUCT_ID']]['PRICES'][$price['CATALOG_GROUP_ID']] = $price;
        }

        foreach ($prices as $productId => $product) {
            if (isset($arResult['GROUPED_PRODUCTS'][$productId])) {
                try {
                    $useDiscount = $arResult['GROUPED_PRODUCTS'][$productId]['PROPERTIES']['USE_DISCOUNT']['VALUE_XML_ID'];
                    $arResult['GROUPED_PRODUCTS'][$productId]['PRICES'] =
                        EnteregoBasket::getPricesArForProductTemplate($product, $useDiscount, $productId);
                } catch (SqlQueryException|LoaderException $e) {
                    $arResult['GROUPED_PRODUCTS'][$productId]['PRICES'] = [];
                }
            }
        }

        $dbBasketItems = \CSaleBasket::GetList(
            array(),
            array("FUSER_ID" => Fuser::getId(), "LID" => SITE_ID, "ORDER_ID" => "NULL"),
            false,
            false,
            array("ID", "PRODUCT_ID", "QUANTITY",)
        );
        while ($arItems = $dbBasketItems->Fetch()) {
            if (isset($arResult['GROUPED_PRODUCTS'][$arItems["PRODUCT_ID"]])) {
                $arResult['GROUPED_PRODUCTS'][$arItems["PRODUCT_ID"]]['ACTUAL_BASKET'] = $arItems["QUANTITY"];
            }
        }
    }
    return $arResult;
}

$jsonForModal = [];
if ($action === 'fastProduct') {

    $prodId = $request->getJsonList()->get('prodId');
    $specialPrice = 0;
    $prop_see_in_window = [];
    $item = CIBlockElement::GetList([], ['ID' => $prodId], false, false,
        ['ID', 'PRODUCT', 'MORE_PHOTO_VALUE', 'PROPERTIES', 'DETAIL_PAGE_URL', 'NAME', 'DETAIL_PICTURE',
            'CATALOG_QUANTITY', 'QUANTITY', 'CATALOG_PRICE_' . B2B_PRICE, IS_DISCOUNT_VALUE,
            'PROPERTY_PRODUCTS_LIST_ON_PROP', 'CATALOG_PRICE_' . SALE_PRICE_TYPE_ID])->GetNext();

    $rsMainPropertyValues = CIBlockElement::GetProperty(IBLOCK_CATALOG, $prodId, []);
    while ($arMainPropertyValue = $rsMainPropertyValues->GetNext()) {
        $xmlId = $arMainPropertyValue['PROPERTY_VALUE_ID'];
        if (!empty($arMainPropertyValue['VALUE'])) {
            // TODO - переформировать в массив с перебором по ключу
            $item['PROPERTIES'][$arMainPropertyValue['CODE']]['ID'] = $arMainPropertyValue['ID'];
            $item['PROPERTIES'][$arMainPropertyValue['CODE']]['NAME'] = $arMainPropertyValue['NAME'];
            $item['PROPERTIES'][$arMainPropertyValue['CODE']]['CODE'] = $arMainPropertyValue['CODE'];
            $item['PROPERTIES'][$arMainPropertyValue['CODE']]['SORT'] = $arMainPropertyValue['SORT'];
            $item['PROPERTIES'][$arMainPropertyValue['CODE']]['PROPERTY_VALUE_ID'] = $arMainPropertyValue['PROPERTY_VALUE_ID'];
            $item['PROPERTIES'][$arMainPropertyValue['CODE']]['VALUE'] = $arMainPropertyValue['VALUE'];
            $item['PROPERTIES'][$arMainPropertyValue['CODE']]['VALUE_XML_ID'] = $arMainPropertyValue['VALUE_XML_ID'];
            $item['PROPERTIES'][$arMainPropertyValue['CODE']]['VALUE_ENUM'] = $arMainPropertyValue['VALUE_ENUM'];
            if (!empty($xmlId)) {
                // TODO - сделать нормлаьно получение значений вариаций свой-ва если свой-во список
                $rsRefProperty = CIBlockElement::GetProperty(IBLOCK_CATALOG, $arMainPropertyValue['ID'],
                    [], ['EMPTY' => 'N', 'ACTIVE' => "Y", 'CODE' => $arMainPropertyValue['CODE']]);
                if ($arRefProperty = $rsRefProperty->Fetch()) {
                    $item['PROPERTIES'][$arMainPropertyValue['CODE']]['VALUES'][] = $arRefProperty;
                }
            }
        }
    }

    $item['PRODUCT'] = [
        'CATALOG_QUANTITY' => $item['CATALOG_QUANTITY'],
        'QUANTITY' => $item['QUANTITY'],
        'PRICE' => round($item['CATALOG_PRICE_' . B2B_PRICE]),
        'SALE_PRICE' => round($item['CATALOG_PRICE_' . SALE_PRICE_TYPE_ID]),
        'SALE_BOOL' => $item['PROPERTY_USE_DISCOUNT_VALUE_VALUE'] === 'Да',
    ];

    if (!empty($price['USER_PRICE'])) {
        $specialPrice = $price['USER_PRICE']['PRICE'];
    }

//    if (!empty($price['SALE_PRICE']['PRICE']) &&
//        ($useDiscount['VALUE_XML_ID'] ?? false == 'true' || USE_CUSTOM_SALE_PRICE)) {
//
//        $specialPrice = ($specialPrice === 0 || $price['SALE_PRICE']['PRICE'] < $specialPrice)
//            ? $price['SALE_PRICE']['PRICE']
//            : $specialPrice;
//    }

    if (empty($morePhoto[0])) {
        $morePhoto[0]['SRC'] = '/local/templates/Oshisha/images/no-photo.gif';
    }

    foreach ($item['PROPERTIES'] as $key => $props_val) {
        if ($item['POPUP_PROPS'][$key]['SEE_POPUP_WINDOW'] == 'Y' && !empty($props_val['VALUE'])) {
            $prop_see_in_window[] = $props_val;
        }
    }

    try {
        $item['GROUPED_PRODUCT'] = getGroupedProduct($prodId, $item['PROPERTIES']['PRODUCTS_LIST_ON_PROP']['VALUE'],$item);
    } catch (ObjectPropertyException $e) {
    } catch (ArgumentException $e) {
    } catch (SystemException $e) {
    }
// TODO - допилить получение переменных - все-таки подумать брать ли некоторые из шиблона
// TODO - или вырезать кусок из получения данных по связ товарам
    $jsonForModal = [
        'ID' => $prodId,
//        'BUY_LINK' => $arItemIDs['BUY_LINK'] ,
//        'QUANTITY_ID' => $arItemIDs['QUANTITY_ID'],
        'TYPE_PRODUCT' => 'PRODUCT',
        'DETAIL_PAGE_URL' => $item['DETAIL_PAGE_URL'],
        'MORE_PHOTO' => $morePhoto,
        'PRODUCT' => $item['PRODUCT'],
        'PREVIEW_PICTURE' => CFile::GetPath(($item['PREVIEW_PICTURE'] ?? $item['DETAIL_PICTURE']))
            ?? '/local/templates/Oshisha/images/no-photo.gif',
//        'USE_DISCOUNT' => $useDiscount['VALUE'],
//        'ACTUAL_BASKET' => $priceBasket,
        'PRICE' => $price['PRICE_DATA'],
        'SALE_PRICE' => round($specialPrice),
        'POPUP_PROPS' => $prop_see_in_window ?? 0,
        'NAME' => $item['NAME'],
        'LIKE' => [
            'ID_PROD' => $item['ID_PROD'],
            'F_USER_ID' => $item['F_USER_ID'],
            'COUNT_LIKE' => $item['COUNT_LIKE'] ?? 0,
            'COUNT_LIKES' => $item['COUNT_LIKES'] ?? 0,
            'COUNT_FAV' => $item['COUNT_FAV'] ?? 0,
        ],
        'USE_CUSTOM_SALE_PRICE' => USE_CUSTOM_SALE_PRICE,
        'BASE_PRICE' => BASIC_PRICE,
        'ADVANTAGES_PRODUCT' => $item['PROPERTIES']['ADVANTAGES_PRODUCT']['VALUE'] ?? [],
        'GROUPED_PRODUCT' => $item['GROUPED_PRODUCT']
    ];
    echo json_encode($jsonForModal);
} else {
    echo json_encode(['errors' => 'yes']);
}
exit();