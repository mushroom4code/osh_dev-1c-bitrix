<?php

/** @var Cmain $APPLICATION */
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');



$APPLICATION->IncludeComponent(
    "ctweb:sms.authorize",
    "profile",
    array(
        "ALLOW_MULTIPLE_USERS" => "Y",
        "PROFILE_AUTH" => $_REQUEST['PROFILE_AUTH'],
        "REGISTER" => $_REQUEST['REGISTER'],
    )
);

