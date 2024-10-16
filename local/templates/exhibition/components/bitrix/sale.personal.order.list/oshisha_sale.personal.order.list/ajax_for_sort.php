<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (!CModule::IncludeModule('sale')) {
    exit();
}


$listStatuses = [];
$listStatusNames = Bitrix\Sale\OrderStatus::getAllStatusesNames(LANGUAGE_ID);
foreach($listStatusNames as $key => $data)
{
    $listStatuses['STATUS'][$key] = array('ID'=>$key,'NAME'=>$data);
}

function sortByField(string $field, string $params, ?string $element = null, ?string $index = null): array
{
    global $USER;
    $listOrders = [];


    $products = CSaleOrder::GetList(array($field => $params), array($element => $index, "USER_ID" => $USER->GetID()));

    while ($res = $products->Fetch()) {
        $listOrders[] = $res;
    }

    foreach ($listOrders as &$itemOrder) {
        $itemOrder['IS_NOT_ACTIVE_ITEMS_PRESENT'] = false;
        $ordersBasket = CSaleBasket::GetList(array(), array('ORDER_ID' => $itemOrder['ID']), false, ['nTopCount' => 5]);
        if (!empty($ordersBasket)) {
            while ($result = $ordersBasket->Fetch()) {
                $my_elements = CIBlockElement::GetList(
                    array(),
                    array("ID" => $result['PRODUCT_ID']),
                    false,
                    false,
                    array('ID', 'NAME', 'ACTIVE', 'DETAIL_PAGE_URL', 'PREVIEW_PICTURE', 'DETAIL_PICTURE')
                );
                $ar_fields = $my_elements->GetNext();
                if ($ar_fields['ACTIVE'] == 'N') {
                    $itemOrder['IS_NOT_ACTIVE_ITEMS_PRESENT'] = true;
                }
                $itemOrder['PICTURE'][] = CFile::GetPath($ar_fields['PREVIEW_PICTURE']);
            }
        }
    }
    return $listOrders;
}

require('show_order_block.php');

if($_POST['sortStatus'] === 'show_canceled') {
    $element = 'STATUS_ID';
    $index = 'F';
} else if ($_POST['sortStatus'] === 'show_delivery') {
    $element = 'RESERVED';
    $index = 'Y';
} else {
    $element = null;
    $index = null;
}

switch ($_POST['typeSort']) {
    case "cheap":
        $listOrders = sortByField("PRICE", 'ASC', $element, $index);
        break;
    case "expensive":
        $listOrders = sortByField("PRICE", 'DESC', $element, $index);
        break;
    case "old":
        $listOrders = sortByField("DATE_INSERT", 'ASC', $element, $index);
        break;
    case "new":
        $listOrders = sortByField("DATE_INSERT", 'DESC', $element, $index);
        break;
}


if (isset($listOrders) && $listOrders !== false) {
    showOrderBlock($listStatuses, $listOrders);
    die();
} else {
    echo 'error';
    die();
}

