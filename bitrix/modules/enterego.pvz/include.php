<?php

CModule::AddAutoloadClasses("", array(
    '\CommonPVZ\DeliveryHelper' => '/bitrix/modules/enterego.pvz/lib/CommonPVZ/DeliveryHelper.php',
    '\CommonPVZ\CommonPVZ' => '/bitrix/modules/enterego.pvz/lib/CommonPVZ/CommonPVZ.php',
    '\CommonPVZ\CommonDoorDeliveryHandler' => '/bitrix/modules/enterego.pvz/lib/CommonPVZ/handlerDelivery.php',
    '\CommonPVZ\OshishaDelivery' => '/bitrix/modules/enterego.pvz/lib/CommonPVZ/OshishaDelivery.php',
    '\CommonPVZ\DoorDeliveryProfile' => '/bitrix/modules/enterego.pvz/lib/CommonPVZ/DoorDeliveryProfile.php',
    '\CommonPVZ\PVZDeliveryProfile' => '/bitrix/modules/enterego.pvz/lib/CommonPVZ/PVZDeliveryProfile.php',
    '\CommonPVZ\SDEKDelivery' => '/bitrix/modules/enterego.pvz/lib/CommonPVZ/SDEKDelivery.php',
    '\CommonPVZ\RussianPostDelivery' => '/bitrix/modules/enterego.pvz/lib/CommonPVZ/RussianPostDelivery.php',
    '\CommonPVZ\PEKDelivery' => '/bitrix/modules/enterego.pvz/lib/CommonPVZ/PEKDelivery.php',
    '\CommonPVZ\FivePostDelivery' => '/bitrix/modules/enterego.pvz/lib/CommonPVZ/FivePostDelivery.php',
    '\CommonPVZ\PickPointDelivery' => '/bitrix/modules/enterego.pvz/lib/CommonPVZ/PickPointDelivery.php',
    '\CommonPVZ\PickPointPointsTable' => '/bitrix/modules/enterego.pvz/lib/CommonPVZ/PickPointPointsTable.php',
    '\Enterego\EnteregoDBDelivery' => '/bitrix/modules/enterego.pvz/lib/CommonPVZ/EnteregoDBDelivery.php',
    '\Enterego\EnteregoDeliveries' => '/bitrix/modules/enterego.pvz/lib/CommonPVZ/EnteregoDeliveries.php',
    '\PecomKabinet' => '/bitrix/modules/enterego.pvz/lib/CommonPVZ/pecom_kabinet.php'
));

if ( file_exists($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php') )
{
    require_once($_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php');
}