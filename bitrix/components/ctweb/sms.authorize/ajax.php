<?php

/** @var Cmain $APPLICATION */
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

$APPLICATION->IncludeComponent(
    "ctweb:sms.authorize",
    "top",
    array(
        "ALLOW_MULTIPLE_USERS" => "Y"
    )
);

