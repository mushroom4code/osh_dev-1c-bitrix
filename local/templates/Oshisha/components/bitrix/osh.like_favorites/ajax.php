<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

//use Ent_like\DataBase_like;

$arResult = '';

if (!empty($_POST['product_array'])) {

    $product_array = json_decode($_POST['product_array']);

    if (!empty($product_array->product_id) && ( !empty($product_array->user_id || !empty($product_array->fuser_id) ) )
        && !empty($product_array->method)) {
        $result = DataBase_like::SetRemoveLikeFavorite($product_array->user_id, $product_array->product_id,
            $product_array->value, $product_array->method, $product_array->fuser_id);
        $arResult = $result === true ? 'success' : 'Ошибка записи в бд';
    } else {
        $arResult = 'Ошибка записи данных - некорректные данные для записи';
    }

}

header('Content-Type: application/json');
echo json_encode($arResult);
