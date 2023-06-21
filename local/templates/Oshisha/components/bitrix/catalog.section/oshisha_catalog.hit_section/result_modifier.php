<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var CBitrixComponentTemplate $this
 * @var CatalogSectionComponent $component
 */

global $USER;
$filter['USER_ID'] = $USER->GetID();

$queryObject = Bitrix\Catalog\SubscribeTable::getList(array('select' => array('ID', 'ITEM_ID', 'USER_CONTACT'), 'filter' => $filter));

while ($subscribe = $queryObject->fetch())
{
    $arResult['CURRENT_USER_SUBSCRIPTIONS']['ITEMS_IDS'][] = $subscribe['ITEM_ID'];
    $arResult['CURRENT_USER_SUBSCRIPTIONS']['SUBSCRIPTIONS'][] = $subscribe;
}

$component = $this->getComponent();

$sectionsArr = [];
$brandsArr = [];
$brandsItemsArr = [];
$finalBrandsItemsArr = [];
$sectionsItemsArr = [];
$sectionsBrandsItemsArr = [];
//unset($arResult['ITEMS']);
//$itemsRes = CIBlockElement::GetList([], ['PROPERTY_HIT_VALUE' => 'да']);
//while ($item = $itemsRes->fetch()) {
//    $arResult['ITEMS'][$item['ID']] = $item;
//}

foreach ($arResult['ITEMS'] as $item) {
    $brandsItemsArr[$item['IBLOCK_SECTION_ID']][$item['ID']]  = $item;
}

$brandsRes = CIBlockSection::GetList([], ['ID' => array_keys($brandsItemsArr)]);
while ($brand = $brandsRes->fetch()) {
    $initialBrandId = $brand['ID'];
    while ($brand['DEPTH_LEVEL'] > 2 ) {
        $brandRes = CIBlockSection::GetByID($brand['IBLOCK_SECTION_ID']);
        $brand = $brandRes->fetch();
    }
    $brandsArr[$brand['ID']] = $brand;
    $finalBrandsItemsArr[$brand['ID']]
        = $finalBrandsItemsArr[$brand['ID']]
        ? ($finalBrandsItemsArr[$brand['ID']] + $brandsItemsArr[$initialBrandId])
        : $brandsItemsArr[$initialBrandId];
    $sectionsItemsArr[$brand['IBLOCK_SECTION_ID']]
        = $sectionsItemsArr[$brand['IBLOCK_SECTION_ID']]
        ? ($sectionsItemsArr[$brand['IBLOCK_SECTION_ID']] + $brandsItemsArr[$initialBrandId])
        : $brandsItemsArr[$initialBrandId];
    $sectionsBrandsItemsArr[$brand['IBLOCK_SECTION_ID']][$brand['ID']]
        = $sectionsBrandsItemsArr[$brand['IBLOCK_SECTION_ID']][$brand['ID']]
        ? ($sectionsBrandsItemsArr[$brand['IBLOCK_SECTION_ID']][$brand['ID']] + $brandsItemsArr[$initialBrandId])
        : $brandsItemsArr[$initialBrandId];
}

foreach ($sectionsItemsArr as &$sectionItems) {
    foreach ($sectionItems as &$sectionItem) {
        $sectionItem['SERVICE_FIELD_POPULARITY'] = intval($sectionItem['PROPERTIES']['SERVICE_FIELD_POPULARITY']['VALUE']);
    }
}

foreach ($sectionsItemsArr as $sectionItemsKey => &$sectionItems) {
    $popularity = array();
    foreach ($sectionItems as $key => $row) {
        $popularity[$key] = $row['SERVICE_FIELD_POPULARITY'];
    }
    array_multisort($popularity, SORT_DESC, $sectionItems);
}

$tempSectionsItemsArr = $sectionsItemsArr;
foreach ($tempSectionsItemsArr as $sectionKey => $sectItems) {
    foreach ($sectItems as $sectionItemKey => $sectionItem) {
        unset($sectionsItemsArr[$sectionKey][$sectionItemKey]);
        $sectionsItemsArr[$sectionKey][$sectionItem['ID']] = $sectionItem;
    }
}

$sectionsRes = CIBlockSection::GetList([], ['ID' => array_keys($sectionsBrandsItemsArr)]);
while ($section = $sectionsRes->fetch()) {
    $sectionsArr[$section['ID']] = $section;
}

$arResult['BRANDS_ITEMS'] = $finalBrandsItemsArr;
$arResult['SECTIONS_ITEMS'] =$sectionsItemsArr;
$arResult['SECTIONS_BRANDS_ITEMS'] = $sectionsBrandsItemsArr;
$arResult['BRANDS'] = $brandsArr;
$arResult['SECTIONS'] = $sectionsArr;

$arParams = $component->applyTemplateModifications();

// Блокировка показа Вы смотрели если параметр установлен, если его не существует, присваиваем.
if (!isset($arParams['ACTIVE_BLOCK_YOU_SEE'])) {
	$arParams['ACTIVE_BLOCK_YOU_SEE'] = 'Y';
}