<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION;
$aMenuLinksExt = array();

if(CModule::IncludeModule('iblock'))
{
    $arFilter = array(
        "TYPE" => "1c_catalog",
        "SITE_ID" => SITE_ID,
		"ID" => IBLOCK_CATALOG,
        'ACTIVE' => 'Y'
    );

    $dbIBlock = CIBlock::GetList(array('SORT' => 'ASC', 'ID' => 'ASC'), $arFilter);
    $dbIBlock = new CIBlockResult($dbIBlock);

    if ($arIBlock = $dbIBlock->GetNext())
    {
        if(defined("BX_COMP_MANAGED_CACHE"))
            $GLOBALS["CACHE_MANAGER"]->RegisterTag("iblock_id_".$arIBlock["ID"]);

        if($arIBlock["ACTIVE"] == "Y")
        {
            $aMenuLinksExt =
                $APPLICATION->IncludeComponent("bitrix:oshisha_menu.sections",
                    "",
                    array(
                        "IS_SEF" => "Y",
                        "SEF_BASE_URL" => "",
                        "SECTION_PAGE_URL" => $arIBlock['SECTION_PAGE_URL'],
                        "DETAIL_PAGE_URL" => $arIBlock['DETAIL_PAGE_URL'],
                        "IBLOCK_TYPE" => $arIBlock['IBLOCK_TYPE_ID'],
                        "IBLOCK_ID" => $arIBlock['ID'],
                        "DEPTH_LEVEL" => "3",
                        "CACHE_TYPE" => "N",
                        "CUSTOM_PARAM_MENU" => "Y"
                    ), false, array('HIDE_ICONS' => 'Y'));
        }
    }

    if(defined("BX_COMP_MANAGED_CACHE"))
        $GLOBALS["CACHE_MANAGER"]->RegisterTag("iblock_id_new");
}

$aMenuLinks = array_merge($aMenuLinksExt, $aMenuLinks);
?>
