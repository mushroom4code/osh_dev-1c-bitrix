<?php

namespace Enterego;

use Bitrix\Currency\CurrencyManager;
use Bitrix\Main;
use Bitrix\Main\Context;
use CIBlockElement;
use CModule;


Main\EventManager::getInstance()->addEventHandler('sale', 'OnSaleBasketBeforeSaved',
    array('Enterego\EnteregoBasket', 'OnSaleBasketBeforeSaved'));

const IBLOCK_OFFERS = 19;
class EnteregoBasket
{
    /** Формирование корзины идет после формирования свойств заказа
     *      поэтому в зависимости от профиля в заказе устанавливаем цены
     * @param $arUserResult
     * @param $request
     * @param $arParams
     * @param $arResult
     * @return void
     */
//    function OnSaleComponentOrderProperties(&$arUserResult, $request, &$arParams, &$arResult)
//    {
//        if ($arUserResult['PERSON_TYPE_ID']==PERSON_TYPE_BUYER && getUserType() && getCurrentPriceId()==B2B_PRICE) {
//            setCurrentPriceId(BASIC_PRICE);
//        }
//    }

    /**
     * @param \Bitrix\Main\Event $event
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     * @throws \Bitrix\Main\ArgumentTypeException
     * @throws \Bitrix\Main\NotImplementedException
     * @throws \Bitrix\Main\NotSupportedException
     */
    public function OnSaleBasketBeforeSaved(Main\Event $event)
    {
        //TODO на форме заказа вызывается на почти на любое действие
        CModule::IncludeModule('iblock') || die();
        CModule::IncludeModule('sale') || die();

        $product_prices = array();
        $new_basket_data = array();

        /** @var \Bitrix\Sale\Basket $basket */
        $basket = $event->getParameter("ENTITY");
        $basket_items = $basket->getBasketItems();
        $currentPriceTypeId = getCurrentPriceId();

        if (!empty($currentPriceTypeId)) {

            $origin_total_price = 0;

            /** @var \Bitrix\Sale\BasketItem $item */

            foreach ($basket_items as $item) {

                $product_id = $item->getProductId();
                $product_quantity = $item->getQuantity();

                $product_prices[$product_id]['QUANTITY'] = $product_quantity;
                $product_prices[$product_id]['ITEM'] = $item;
                $new_basket_data[$product_id]['QUANTITY'] = $product_quantity;

                $origin_price = self::loadProductPrice($product_id, RETAIL_PRICE);
                if ($origin_price) {
                    $new_basket_data[$product_id]['PRICE'] = $origin_price;
                    $origin_total_price += $product_quantity * $origin_price;
                    $product_prices[$product_id]['PRICE'] = $origin_price;
                    $product_prices[$product_id]['PRICE_TYPE'] = RETAIL_PRICE;
                }
            }

            if ($origin_total_price <= 10000) {
                $price_id = RETAIL_PRICE;
            } elseif ($origin_total_price <= 30000) {
                $price_id = BASIC_PRICE;
            } else {
                $price_id = B2B_PRICE;
            }


            foreach ($product_prices as $product_id => $price_data) {
				// OFFERS OR PRODUCT
	            $offer = CIBlockElement::GetProperty(
		            IBLOCK_CATALOG_OFFERS ?? IBLOCK_OFFERS,
		            $product_id,
		            array(),
		            array('CODE' => 'CML2_LINK'));
	            $productIdOnOffer = $offer->Fetch();
	            $productId = isset($productIdOnOffer['VALUE']) && !empty($productIdOnOffer['VALUE']) ? $productIdOnOffer['VALUE'] : $product_id;
	            // OFFERS OR PRODUCT
                $propsUseSale = CIBlockElement::GetProperty(
                    IBLOCK_CATALOG,
	                $productId,
                    array(),
                    array('CODE' => 'USE_DISCOUNT'));
                $newProp = $propsUseSale->Fetch();

                if (USE_CUSTOM_SALE_PRICE || $newProp['VALUE_XML_ID'] == 'true') {

                    $price_type = "CATALOG_PRICE_" . SALE_PRICE_TYPE_ID;
                    $result = CIBlockElement::GetList(
                        array(),
                        array("ID" => $product_id),
                        false,
                        false,
                        array("$price_type"));

                    if ($ar_res = $result->fetch()) {
                        if (((int)$price_data['PRICE'] > (int)$ar_res["$price_type"]) && !empty($ar_res["$price_type"])) {
                            $product_prices[$product_id]['PRICE'] = $ar_res["$price_type"];
                            $product_prices[$product_id]['PRICE_ID'] = SALE_PRICE_TYPE_ID;
                        } else {
                            $ids = USE_CUSTOM_SALE_PRICE ? $currentPriceTypeId : $price_id;
                            $price_ids = "CATALOG_PRICE_" . $ids;
                            $res = CIBlockElement::GetList(
                                array(),
                                array("ID" => $product_id),
                                false,
                                false,
                                array("$price_ids"));

                            if ($arData = $res->fetch()) {
                                $product_prices[$product_id]['PRICE'] = $arData["$price_ids"];
                                $product_prices[$product_id]['PRICE_ID'] = $ids;
                            }
                        }
                    }
                } else {

                    $res = CIBlockElement::GetList(
                        array(),
                        array("ID" => $product_id),
                        false,
                        false,
                        array("CATALOG_PRICE_$price_id"));

                    if ($ar_res = $res->fetch()) {
                        $product_prices[$product_id]['PRICE'] = $ar_res["CATALOG_PRICE_$price_id"];
                        $product_prices[$product_id]['PRICE_ID'] = $price_id;
                    }
                }


                $item = $price_data['ITEM'];

                $item->setFields([
                    'QUANTITY' => $price_data['QUANTITY'],
                    'CURRENCY' => CurrencyManager::getBaseCurrency(),
                    'LID' => Context::getCurrent()->getSite(),
                    'PRICE' => $product_prices[$product_id]['PRICE'],
                    'CUSTOM_PRICE' => 'N',
                    'BASE_PRICE' => $product_prices[$product_id]['PRICE'],
                    'PRICE_TYPE_ID' => $product_prices[$product_id]['PRICE_ID'],
                ]);

            }
        }

    }

    /**
     * @param $product_id
     * @param $price_type_id
     * @return int|null
     */
    private static function loadProductPrice($product_id, $price_type_id): ?int
    {
        $res = CIBlockElement::GetList(
            array(),
            array("ID" => $product_id),
            false,
            false,
            ["CATALOG_PRICE_$price_type_id"]);

        if ($ar_res = $res->Fetch()) {
            return $ar_res["CATALOG_PRICE_$price_type_id"];
        }
        return null;
    }

    /**
     * @param $arPrices
     * @param $useDiscount
     * @return array
     */
    public static function getPricesArForProductTemplate($arPrices, $useDiscount): array
    {
        $price = [];
        $sale = $arPrices['PRICES'][SALE_PRICE_TYPE_ID];
        $retail = $arPrices['PRICES'][RETAIL_PRICE];
        $base = $arPrices['PRICES'][BASIC_PRICE];
        $b2b = $arPrices['PRICES'][B2B_PRICE];

        if (USE_CUSTOM_SALE_PRICE || $useDiscount['VALUE_XML_ID'] == 'true') {
            if (!empty($sale) && ((int)$sale['PRICE'] < (int)$retail['PRICE'])) {
                $price['SALE_PRICE'] = $sale;
            }
        }
        if (!empty($retail)) {
            $price['PRICE_DATA'][0] = $retail;
            $price['PRICE_DATA'][0]['NAME'] = 'Розничная (до 10к)';
        }
        if (!empty($base)) {
            $price['PRICE_DATA'][1] = $base;
            $price['PRICE_DATA'][1]['NAME'] = 'Основная (до 30к)';
        }
        if (!empty($b2b)) {
            $price['PRICE_DATA'][2] = $b2b;
            $price['PRICE_DATA'][2]['NAME'] = 'b2b (от 30к)';
        }

        return $price;
    }

    /**
     * Basket action
     *
     * Callback method for recalculate product for basket rules
     * @param array $order
     * @param array $action
     * @return void
     * @throws Main\ArgumentException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     * @param callable $filter Filter for basket items.
     */
    public static function SetSpecialPriceType(array &$order, array $action, $filter)
    {
        if (empty($action['VALUE'])){
            return;
        }

        if (empty($order['BASKET_ITEMS']) || !is_array($order['BASKET_ITEMS']))
            return;

        if (!Main\Loader::includeModule('catalog')){
            return;
        }

        $filterBasket = (is_callable($filter) ? array_filter($order['BASKET_ITEMS'], $filter) : $order['BASKET_ITEMS']);

        if (empty($filterBasket)) {
            return;
        }

        $arProductId = array_column($filterBasket, 'PRODUCT_ID' );
        $rsPrice = \Bitrix\Catalog\PriceTable::getList([
            'select' => ['PRODUCT_ID','PRICE'],
            'filter' => [
                'PRODUCT_ID'=>$arProductId,
                'CATALOG_GROUP.NAME' => $action['VALUE'],
            ]
        ])->fetchAll();
        if (empty($rsPrice)) {
            return;
        }

        $basketPrice = [];
        foreach ($rsPrice as $item) {
            $basketPrice[$item['PRODUCT_ID']] = (float) $item['PRICE'];
        }

        foreach ($order['BASKET_ITEMS'] as $basketCode => $basketRow) {
            if (empty($basketPrice[$basketRow['PRODUCT_ID']])){
                continue;
            }
            $discountPrice = $basketPrice[$basketRow['PRODUCT_ID']];
            if ($basketRow['PRICE'] > $discountPrice){

                $oldPrice = $basketRow['PRICE'];
                $basketRow['PRICE'] = $discountPrice;
                $basketRow['DISCOUNT_PRICE'] = $oldPrice - $discountPrice;

                $order['BASKET_ITEMS'][$basketCode] = $basketRow;
            }
        }
    }

}