<?php

namespace CommonPVZ;

use Bitrix\Sale\Location\LocationTable;
use Bitrix\Main\Config\Option;

class DellinDelivery extends CommonPVZ
{
    public string $delivery_code = 'Dellin';

    public string $delivery_name = 'Dellin';

    public static string $code = 'Dellin';
    private $dellin_cache_id = 'dellin_delivery_prices';
    private string $api_key;

    public function __construct() {
        parent::__construct();
        $this->api_key = $this->configs['apikey'];
        $this->delivery_name = "Деловые Линии";
    }

    public static function getInstanceForDoor($deliveryParams): array
    {
        //TODO site id in seetings
        if (SITE_ID !== 'N2') {
            return [];
        }

        if (Option::get(DeliveryHelper::$MODULE_ID, 'Dellin_door_active') === 'Y') {
            return  [new DellinDelivery()];
        }
        return [];
    }

    public static function getInstanceForPvz($deliveryParams): array
    {
        //TODO site id in seetings
        if (SITE_ID !== 'N2') {
            return [];
        }
        if (Option::get(DeliveryHelper::$MODULE_ID, 'Dellin_pvz_active') === 'Y') {
            return  [new DellinDelivery()];
        }
        return [];
    }

    protected function connect()
    {
        try {
            $this->api_key = $this->configs['apikey'];
            return true;
        } catch (\Throwable $e) {
            $this->errors[] = $e->getMessage();
            return array('errors' => $this->errors);
        }
    }

    public function updatePointsForDellin() {
        $result = DellindeliveryApicore::GetTerminals($this->api_key);
        DellinPointsTable::deleteAll();

        $res = LocationTable::getList(array(
            'filter' => array('=NAME.LANGUAGE_ID' => LANGUAGE_ID, '=TYPE.ID' => '5'),
            'select' => array('*', 'NAME_RU' => 'NAME.NAME', 'TYPE_CODE' => 'TYPE.CODE')
        ));
        $listLocation = [];
        while ($arLocation = $res->fetch()) {
            $listLocation[] = $arLocation;
        }
        foreach ($result as $value) {
            $curLocation = null;
            foreach ($listLocation as $location) {
                if ($location['NAME_RU'] === $value['cityName']) {
                    $curLocation = $location;
                    break;
                }
            }

            if ($curLocation===null) {
                continue;
            }

            $point = new DellinPointsTable();
            if ($value['cityName'] != 'Москва') {
                foreach ($value['terminals'] as $terminal) {
                    if ($terminal->giveoutCargo === true) {

                        $response = $point->add([
                            'ID' => $terminal->id,
                            'BITRIX_CODE' => $curLocation['CODE'],
                            'CODE' => $terminal->id,
                            'PHONE_NUMBER' => $terminal->mainPhone ?? '',
                            'WORK_TIME' => $terminal->calcSchedule->arrival ?? '',
                            'FULL_ADDRESS' => $terminal->fullAddress,
                            'STREET_KLADR' => $terminal->addressCode->street_code ?? '',
                            'ADDRESS_LAT' => $terminal->latitude,
                            'ADDRESS_LNG' => $terminal->longitude,
                        ]);
                    }
                }
            }
        }
    }

    public function getPVZ(string $city_name, array &$result_array, int &$id_feature, string $code_city, array $packages, $dimensionsHash, $sumDimensions)
    {
        $arParams = ['filter'=>['BITRIX_CODE'=>$code_city]];
        $res = DellinPointsTable::getList($arParams);
        while ($point = $res->fetch()){
            $features_obj['type'] = 'Feature';
            $features_obj['id'] = $id_feature;
            $id_feature += 1;
            $features_obj['geometry'] = [
                'type' => 'Point',
                'coordinates' => [
                    $point['ADDRESS_LAT'],
                    $point['ADDRESS_LNG'],
                ]
            ];
            $features_obj['properties'] = [
                'code_pvz' => $point['CODE'],
                'type' => 'PVZ',
                'fullAddress' => $point['FULL_ADDRESS'],
                'phone' => $point['PHONE_NUMBER'],
                'workTime' => $point['WORK_TIME'],
                'deliveryName' => $this::$code,
                'iconCaption' => 'Деловые линии',
                'hintContent' => $point['FULL_ADDRESS'],
                "openEmptyBalloon" => true,
                "clusterCaption" => 'Деловые линии',
                "street_kladr" => $point['STREET_KLADR']
            ];
            $features_obj['options'] = [
                'preset' => 'islands#yellowIcon'
            ];

            $result_array[] = $features_obj;
        }
    }

    /** Return calculate price delivery
     * @param $params
     * @return float|int|bool - false if error calculate
     */
    public function getPrice($params)
    {
        try {
            while (strlen($this->configs['derivalkladr']) < 25) {
                $this->configs['derivalkladr'] = $this->configs['derivalkladr'] . '0';
            }
            $params['shipment_weight'] = $params['weight'];
            if (empty($params['shipment_weight']))
                $noGeneralWeight = true;
            else
                $noGeneralWeight = false;
            $params['width'] = 0;
            $params['length'] = 0;
            $params['height'] = 0;
            $params['weight'] = 0;
            $params['default_width'] = (int)Option::get(DeliveryHelper::$MODULE_ID, 'Common_defaultwidth');
            $params['default_length'] = (int)Option::get(DeliveryHelper::$MODULE_ID, 'Common_defaultlength');
            $params['default_height'] = (int)Option::get(DeliveryHelper::$MODULE_ID, 'Common_defaultheight');
            $params['default_weight'] = (int)Option::get(DeliveryHelper::$MODULE_ID, 'Common_defaultweight');
            $params['totalVolume'] = 0;
            $defaultPackageVolume = ($params['default_width'] / 1000) * ($params['default_length'] / 1000) * ($params['default_height'] / 1000);
            foreach ($params['packages'] as $package) {
                if (!empty($package['width']) && ($package['width'] > $params['width'])) {
                    $params['width'] = $package['width'];
                }
                if (!empty($package['length']) && ($package['length'] > $params['length'])) {
                    $params['length'] = $package['length'];
                }
                if (!empty($package['height']) && ($package['height'] > $params['height'])) {
                    $params['height'] = $package['height'];
                }
                if (!empty($package['weight']) && ($package['weight'] > $params['weight'])) {
                    $params['weight'] = $package['weight'];
                }
                if ($noGeneralWeight && !empty($package['weight'])) {
                    $params['shipment_weight'] += $package['weight'];
                } elseif ($noGeneralWeight) {
                    $params['shipment_weight'] += $params['default_weight'];
                }
                if(!empty($package['width']) && !empty($package['length']) && !empty($package['height'])) {
                    $params['totalVolume'] += ($package['width'] / 1000) * ($package['length'] / 1000) * ($package['height'] / 1000);
                } else {
                    $params['totalVolume'] += $defaultPackageVolume;
                }
            }
            if (empty($params['width']))
                $params['width'] = $params['default_width'];
            if (empty($params['length']))
                $params['length'] = $params['default_length'];
            if (empty($params['height']))
                $params['height'] = $params['default_height'];
            if (empty($params['weight']))
                $params['weight'] = $params['default_weight'];
            $hashed_values = array($params['width'], $params['length'], $params['height'],
                $params['weight'], $params['totalVolume'], $params['shipment_weight'],
                $params['code_pvz'], count($params['packages']), 'terminal');
            $hash_string = md5(implode('', $hashed_values));

            $is_cache_on = Option::get(DeliveryHelper::$MODULE_ID, 'Common_iscacheon');

            $cache = \Bitrix\Main\Data\Cache::createInstance(); // получаем экземпляр класса
            if ($cache->initCache(3600, $this->dellin_cache_id)) { // проверяем кеш и задаём настройки
                if ($is_cache_on == 'Y') {
                    $cached_vars = $cache->getVars();
                    if (!empty($cached_vars)) {
                        foreach ($cached_vars as $varKey => $var) {
                            if ($varKey === $hash_string) {
                                return $var;
                            }
                        }
                    }
                }
            }

            $derivalDate = date('Y-m-d', strtotime(date('Y-m-d').' +1 days'));
            $data = ['appkey' => $this->api_key,
                'delivery' => [
                    'deliveryType' => [
                        'type' => 'auto'
                    ],
                    'arrival' => [
                        'variant' => 'terminal',
                        'terminalID' => $params['code_pvz']
                    ],
                    'derival' => [
                        'produceDate' => $derivalDate,
                        'variant' => 'address',
                        'address' => [
                            'street' => $this->configs['derivalkladr']
                        ],
                        'time' => [
                            'worktimeStart' => $this->configs['derivalstarttime'].':00',
                            'worktimeEnd' => $this->configs['derivalendtime'].':00'
                        ]
                    ]
                ],
                'cargo' => [
                    'quantity' => count($params['packages']),
                    "freightUID" => "0x98086eae8b603ea911e5e1f0bdbfff24",
                    'width' => ($params['width'] / 1000),
                    'length' => ($params['length'] / 1000),
                    'height' => ($params['height'] / 1000),
                    'weight' => ($params['weight'] / 1000),
                    'totalVolume' => $params['totalVolume'],
                    'totalWeight' => ($params['shipment_weight'] / 1000),
                ]
            ];
            $result = DellindeliveryApicore::SendApiRequest('calculator', $data);

            if (!empty($result->errors)) {
                $this->errors[] = $result->errors;
                return array('errors' => $this->errors);
            } else {
                $finalPrice = $result->data->price - $result->data->derival->price - $result->data->insurance;

                $cache->forceRewriting(true);
                if ($cache->startDataCache()) {
                    $cache->endDataCache((isset($cached_vars) && !empty($cached_vars))
                        ? array_merge($cached_vars, array($hash_string => $finalPrice))
                        : array($hash_string => $finalPrice));
                }

                return $finalPrice;
            }
        } catch (\Throwable $e) {
            $this->errors[] = $e->getMessage();
            return array('errors' => $this->errors);
        }
    }

    public function getPriceDoorDelivery($params)
    {
        try {
            while (strlen($params['street_kladr_to']) < 25) {
                $params['street_kladr_to'] = $params['street_kladr_to'].'0';
            }
            while (strlen($this->configs['derivalkladr']) < 25) {
                $this->configs['derivalkladr'] = $this->configs['derivalkladr'] . '0';
            }

            if (empty($params['shipment_weight']))
                $noGeneralWeight = true;
            else
                $noGeneralWeight = false;

            $params['width'] = 0;
            $params['length'] = 0;
            $params['height'] = 0;
            $params['weight'] = 0;
            $params['default_width'] = (int)Option::get(DeliveryHelper::$MODULE_ID, 'Common_defaultwidth');
            $params['default_length'] = (int)Option::get(DeliveryHelper::$MODULE_ID, 'Common_defaultlength');
            $params['default_height'] = (int)Option::get(DeliveryHelper::$MODULE_ID, 'Common_defaultheight');
            $params['default_weight'] = (int)Option::get(DeliveryHelper::$MODULE_ID, 'Common_defaultweight');
            $params['totalVolume'] = 0;
            $defaultPackageVolume = ($params['width'] / 1000) * ($params['length'] / 1000) * ($params['height'] / 1000);
            foreach ($params['packages'] as $package) {
                if (!empty($package['width']) && ($package['width'] > $params['width'])) {
                    $params['width'] = $package['width'];
                }
                if (!empty($package['length']) && ($package['length'] > $params['length'])) {
                    $params['length'] = $package['length'];
                }
                if (!empty($package['height']) && ($package['height'] > $params['height'])) {
                    $params['height'] = $package['height'];
                }
                if (!empty($package['weight']) && ($package['weight'] > $params['weight'])) {
                    $params['weight'] = $package['weight'];
                }
                if ($noGeneralWeight && !empty($package['weight'])) {
                    $params['shipment_weight'] += $package['weight'];
                } elseif ($noGeneralWeight) {
                    $params['shipment_weight'] += $params['default_weight'];
                }
                if(!empty($package['width']) && !empty($package['length']) && !empty($package['height'])) {
                    $params['totalVolume'] += ($package['width'] / 1000) * ($package['length'] / 1000) * ($package['height'] / 1000);
                } else {
                    $params['totalVolume'] += $defaultPackageVolume;
                }
            }
            if (empty($params['width']))
                $params['width'] = $params['default_width'];
            if (empty($params['length']))
                $params['length'] = $params['default_length'];
            if (empty($params['height']))
                $params['height'] = $params['default_height'];
            if (empty($params['weight']))
                $params['weight'] = $params['default_weight'];
            $hashed_values = array($params['width'], $params['length'], $params['height'],
                $params['weight'], $params['totalVolume'], $params['shipment_weight'],
                $params['street_kladr_to'], count($params['packages']), 'address');
            $hash_string = md5(implode('', $hashed_values));

            $is_cache_on = Option::get(DeliveryHelper::$MODULE_ID, 'Common_iscacheon');

            $cache = \Bitrix\Main\Data\Cache::createInstance(); // получаем экземпляр класса
            if ($cache->initCache(3600, $this->dellin_cache_id)) { // проверяем кеш и задаём настройки
                if ($is_cache_on == 'Y') {
                    $cached_vars = $cache->getVars();
                    if (!empty($cached_vars)) {
                        foreach ($cached_vars as $varKey => $var) {
                            if ($varKey === $hash_string) {
                                return $var;
                            }
                        }
                    }
                }
            }

            $derivalDate = date('Y-m-d', strtotime(date('Y-m-d').' +1 days'));
            $data = ['appkey' => $this->api_key,
                'delivery' => [
                    'deliveryType' => [
                        'type' => 'auto'
                    ],
                    'arrival' => [
                        'variant' => 'address',
                        'address' => [
                            'street' => $params['street_kladr_to']
                        ],
                        'time' => [
                            'worktimeStart' => '10:00',
                            'worktimeEnd' => '18:00'
                        ]
                    ],
                    'derival' => [
                        'produceDate' => $derivalDate,
                        'variant' => 'address',
                        'address' => [
                            'street' => $this->configs['derivalkladr']
                        ],
                        'time' => [
                            'worktimeStart' => $this->configs['derivalstarttime'].':00',
                            'worktimeEnd' => $this->configs['derivalendtime'].':00'
                        ]
                    ]
                ],
                'cargo' => [
                    'quantity' => count($params['packages']),
                    "freightUID" => "0x98086eae8b603ea911e5e1f0bdbfff24",
                    'width' => ($params['width'] / 1000),
                    'length' => ($params['length'] / 1000),
                    'height' => ($params['height'] / 1000),
                    'weight' => ($params['weight'] / 1000),
                    'totalVolume' => $params['totalVolume'],
                    'totalWeight' => ($params['shipment_weight'] / 1000),
                ]
            ];
            $result = DellindeliveryApicore::SendApiRequest('calculator', $data);

            if (!empty($result->errors)) {
                $this->errors[] = $result->errors;
                return array('errors' => $this->errors);
            } else if(!empty($result)) {
                $finalPrice = $result->data->price - $result->data->derival->price - $result->data->insurance;
                $cache->forceRewriting(true);
                if ($cache->startDataCache()) {
                    $cache->endDataCache((isset($cached_vars) && !empty($cached_vars))
                        ? array_merge($cached_vars, array($hash_string => $finalPrice))
                        : array($hash_string => $finalPrice));
                }

                return $finalPrice;
            } else {
                $this->errors[] = 'Empty result was returned';
                return array('errors' => $this->errors);
            }
        } catch (\Throwable $e) {
            $this->errors[] = $e->getMessage();
            return array('errors' => $this->errors);
        }
    }
}