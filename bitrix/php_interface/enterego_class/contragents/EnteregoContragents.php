<?php

namespace Enterego\contragents;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use CUser;
use Enterego\ORM\EnteregoORMContragentsTable;
use Enterego\ORM\EnteregoORMRelationshipUserContragentsTable;

class EnteregoContragents
{
    /**
     * @param int $user_id
     * @return array
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */

    public const typeUric = 'uric';
    public const typeIp = 'ip';

    /**
     * @param int $user_id
     * @param int $contragent_id
     * @return array|string[]
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function setRelationShip(int $user_id = 0, int $contragent_id = 0): array
    {
        $result = [];
        $res = EnteregoORMRelationshipUserContragentsTable::getList(
            array(
                'select' => array(
                    'ID_CONTRAGENT',
                ),
                'filter' => array(
                    'USER_ID' => $user_id,
                    'ID_CONTRAGENT' => $contragent_id
                ),
            )
        )->fetch();

        $resultSelect = EnteregoORMContragentsTable::getList(
            array(
                'select' => array('ID_CONTRAGENT'),
                'filter' => array(
                    "ID_CONTRAGENT" => $contragent_id
                ),
            )
        )->fetch();

        if (empty($res) && !empty($resultSelect)) {

            $addResultRel = EnteregoORMRelationshipUserContragentsTable::add(array(
                'ID_CONTRAGENT' => $contragent_id,
                'USER_ID' => $user_id,
            ));

            $user = new CUser();
            $user->Update($user_id, ['PERSONAL_NOTES' => 'Обновлена связь между пользователем и контр-том '
                    . ConvertTimeStamp(false, "FULL")]
            );

            $result = $addResultRel->isSuccess() ?
                ['success' => 'Ожидайте подтверждения связи'] :
                ['error' => 'Вы не смогли запросить связь - попробуйте еще раз или обратитесь к менеджеру'];
        }
        return $result;
    }

    /**
     * @param int $user_id
     * @param array[] $filters
     * @return array
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getContragentsByUserId(int $user_id = 0, array $filters = []): array
    {
        $result = [];

        if ($user_id !== 0) {

            $ids_new = [];
            $resultUserRelationships = EnteregoORMRelationshipUserContragentsTable::getList(
                array(
                    'select' => array(
                        'ID_CONTRAGENT',
                    ),
                    'filter' => array(
                        'USER_ID' => $user_id
                    ),
                )
            );

            if (!empty($resultUserRelationships)) {

                while ($ids_str = $resultUserRelationships->fetch()) {
                    $ids_new[] = $ids_str['ID_CONTRAGENT'];
                }

                if (!empty($ids_new)) {
                    $filters["@ID_CONTRAGENT"] = $ids_new;

                    $resultSelect = EnteregoORMContragentsTable::getList(
                        array(
                            'select' => array('*'),
                            'filter' => $filters,
                        )
                    );
                    if (!empty($resultSelect)) {
                        while ($contargent = $resultSelect->fetch()) {
                            $result[] = $contargent;
                        }
                    }
                }
            }
        }


        return $result;
    }

    /**
     * @param int $user_id
     * @param array $arData
     * @return string[]
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function addContragent(int $user_id = 0, array $arData = []): array
    {
        $result = ['error' => 'Такой контрагент уже существует'];
        $resultSelect = EnteregoORMContragentsTable::getList(
            array(
                'select' => array('ID_CONTRAGENT', 'XML_ID'),
                'filter' => array($arData),
            )
        )->fetch();

        if (empty($resultSelect)) {
            $addResult = EnteregoORMContragentsTable::add($arData);

            if ($addResult->isSuccess()) {
                $newId = $addResult->getId();
                EnteregoORMContragentsTable::update(
                    array('ID_CONTRAGENT' => $newId),
                    array('XML_ID' => uniqid('contrxml_'))
                );

                $addResultRel = $user_id !== 0 ? EnteregoORMRelationshipUserContragentsTable::add(
                    array(
                        'ID_CONTRAGENT' => $newId,
                        'USER_ID' => $user_id,
                    )
                ) : false;

                $user = new CUser();
                $user->Update($user_id, ['PERSONAL_NOTES' => 'Обновлена связь между пользователем и контр-том '
                        . ConvertTimeStamp(false, "FULL")]
                );

                $result = $addResultRel->isSuccess() ?
                    ['success' => 'Ожидайте подтверждения связи'] :
                    ['error' => 'Вы не смогли добавить контрагента - попробуйте еще раз'];

            }
        } else {
            $result = ['error' => ['code' => '', 'item' => $resultSelect]];
        }

        return $result;
    }

    /**
     * @param array $arData
     * @return bool
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function addOrUpdateContragent(array $arData = []): bool
    {
        $result = false;
        $resultSelect = EnteregoORMContragentsTable::getList(
            array(
                'select' => array('ID_CONTRAGENT', 'XML_ID'),
                'filter' => array('XML_ID' => $arData['XML_ID']),
            )
        )->fetch();

        if (empty($resultSelect)) {
            $addResult = EnteregoORMContragentsTable::add($arData);
            if ($addResult->isSuccess()) {
                $result = true;
            }
        } else {
            $newArData = $arData;
            unset($newArData['XML_ID']);
            $updateResult = EnteregoORMContragentsTable::update(array('ID_CONTRAGENT' => $resultSelect['ID_CONTRAGENT']), $newArData);
            if ($updateResult->isSuccess()) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * @param array $filter
     * @return bool|array
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getContragentByFilter(array $filter = []): bool|array
    {
        $result = false;
        if (!empty($filter)) {
            $resultSelect = EnteregoORMContragentsTable::getList(
                array(
                    'select' => array('ID_CONTRAGENT', 'TYPE', 'NAME_ORGANIZATION', 'PHONE_COMPANY', 'EMAIL', 'INN'),
                    'filter' => $filter,
                )
            )->fetch();

            $result = empty($resultSelect) ? true : $resultSelect;
        }
        return $result;
    }


    /**
     * @param int $user_id
     * @return array
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getActiveContragentForUser(int $user_id = 0): array
    {
        $result = [];
        $ids_new = [];
        $resultUserRelationships = EnteregoORMRelationshipUserContragentsTable::getList(
            array(
                'select' => array(
                    'ID_CONTRAGENT',
                ),
                'filter' => array(
                    'USER_ID' => $user_id
                ),
            )
        );

        while ($ids_str = $resultUserRelationships->fetch()) {
            $ids_new[] = $ids_str['ID_CONTRAGENT'];
        }
        if (!empty($ids_new)) {
            $resultSelect = EnteregoORMContragentsTable::getList(
                array(
                    'select' => array('ID_CONTRAGENT'),
                    'filter' => array(
                        "@ID_CONTRAGENT" => $ids_new,
                        'STATUS_CONTRAGENT' => 1
                    ),
                )
            );
            if (!empty($resultSelect)) {
                while ($contargent = $resultSelect->fetch()) {
                    $result[] = $contargent;
                }
            }
        }
        return $result;
    }
}