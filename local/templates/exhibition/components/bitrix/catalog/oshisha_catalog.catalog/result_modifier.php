<?php

/** @var $arResult array */

use Enterego\EnteregoDiscount;

//const CATALOG_GIFT_ID=1873;
const CATALOG_GIFT_ID = 478;

if ($arResult['FOLDER'] === '/diskont/') {
    $cat = new EnteregoDiscount();
    $arResult['SECTION_LIST'] = $cat->getSectionProductsForFilter();
} else {
    $arOrderS = array('DEPTH_LEVEL' => 'ASC', 'SORT' => 'ASC',);
    $arFilterS = array('ACTIVE' => 'Y', 'IBLOCK_ID' => IBLOCK_CATALOG_EX, 'GLOBAL_ACTIVE' => 'Y',);
    $arSelectS = array('*');

    $rsSections = CIBlockSection::GetList($arOrderS, $arFilterS, false, $arSelectS);
    $sectionLinc = array();
    $arResult['ROOT'] = array();
    $sectionLinc[0] = &$arResult['ROOT'];
    $arSections = [];
    while ($arSection = $rsSections->GetNext()) {
        $arSection['TEXT'] = $arSection['NAME'];
        $arSection['LINK'] = $arSection['CODE'];
        if ($arSection['DEPTH_LEVEL'] > 1) {
            $arSection['DEPTH_LEVEL'] = $arSection['DEPTH_LEVEL'] - 1;

        }

        $arSections[$arSection['ID']] = $arSection;
    }

    foreach ($arSections as $arSection) {
        if ($arSection['DEPTH_LEVEL'] == 1 && $arSection['IBLOCK_SECTION_ID'] > 0) {
            $sectionLinc[intval($arSection['IBLOCK_SECTION_ID'])]['CHILDS'][$arSection['ID']] = $arSection;
            $sectionLinc[$arSection['ID']] = &$sectionLinc[intval($arSection['IBLOCK_SECTION_ID'])]['CHILDS'][$arSection['ID']];
        } else {
            $sectionLinc[intval($arSection['IBLOCK_SECTION_ID'])]['CHILD'][$arSection['ID']] = $arSection;
            $sectionLinc[$arSection['ID']] = &$sectionLinc[intval($arSection['IBLOCK_SECTION_ID'])]['CHILD'][$arSection['ID']];
        }

    }

    $arResult['SECTION_LIST'] = $sectionLinc[0]['CHILD'];
}